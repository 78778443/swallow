<?php

namespace app\model;

use think\facade\Db;
use think\facade\Session;
use think\Model;


class CodeQlModel extends Model
{

    public static function getDetailCount()
    {


        $countList = [];

        return $countList;
    }

    public static function ai_message()
    {

        //读取上游数据
        $list = Db::table('codeql')->where(['ai_message' => ''])->select()->toArray();


        //处理数据
        $data = [];
        foreach ($list as $value) {
            $aiMessage = ChatGPT::tongyi($value['prompt']);
            $translatedJson = json_decode($aiMessage, true);
            $aiMessage = $translatedJson['output']['choices'][0]['message']['content'] ?? $aiMessage;

            //更新扫描时间
            $data = ['ai_message' => $aiMessage];
            Db::table('codeql')->where(['id' => $value['id']])->update($data);
        }
    }

    public static function start()
    {
        //安装工具
        self::autoDownTool();

        //读取上游数据
        $where = [];
        $list = Db::table('git_addr')->whereNotNull('code_path')->where($where)
            ->whereTime('codeql_scan_time', '<=', date('Y-m-d', time() - (7 * 86400)))
            ->select()->toArray();


        //处理数据

        foreach ($list as $value) {
            $data = [];
            $codePath = $value['code_path'];
            //检测项目语言
            $language = self::detectLanguage($codePath);
            //执行检测脚本
            $resultsPath = self::execTool($codePath, $language);

            //录入检测结果
            $tempList = self::writeData($resultsPath, $value);

            //开始执行
            $data = array_merge($data, $tempList);

            if (empty($data)) {
                print_r("codeql 扫描失败:{$value['code_path']}\n");
                continue;
            }

            Db::table('codeql')->replace()->strict(false)->insertAll($data);

            //更新扫描时间
            $data = ['codeql_scan_time' => date('Y-m-d H:i:s')];
            Db::table('git_addr')->where(['id' => $value['id']])->update($data);
        }
    }

    //自动检测项目语言
    public static function detectLanguage($codePath)
    {
        // 检查是否有Java项目文件
        if (file_exists($codePath . '/pom.xml') || file_exists($codePath . '/build.gradle')) {
            return 'java';
        }
        // 检查是否有JavaScript项目文件
        if (file_exists($codePath . '/package.json')) {
            return 'javascript';
        }
        // 检查是否有Go项目文件
        if (file_exists($codePath . '/go.mod') || file_exists($codePath . '/main.go')) {
            return 'go';
        }
        // 检查是否有Python项目文件
        if (file_exists($codePath . '/requirements.txt') || file_exists($codePath . '/setup.py')) {
            return 'python';
        }
        // 默认返回python
        return 'python';
    }

    public static function autoDownTool()
    {


//        $url = "https://github.com/github/codeql-cli-binaries/releases/latest/download/codeql-linux64.zip";

        // 用户已指定的CodeQL路径
        $userCodeqlPath = 'codeql';
        $codeqlBinary = "{$userCodeqlPath}/codeql";
        
        // 检查CodeQL是否存在并创建软链接
        if (file_exists($codeqlBinary) && !file_exists('/usr/local/bin/codeql')) {
            $cmd = "ln -sf {$codeqlBinary} /usr/local/bin/codeql";
            echo "创建CodeQL软链接:{$cmd} \n";
            exec($cmd);
        }

        //下载规则
        if (!file_exists('extend/codeql/rules')) {
            $cmd = "cd extend/codeql/ && git clone --depth=1 https://github.com/github/codeql.git rules";
            echo "下载CodeQL规则集 :{$cmd} \n";
            exec($cmd);
        }
    }

    public static function WriteData($resultsPath,$gitInfo)
    {
        if (!file_exists($resultsPath)) {
            echo "扫描结果文件不存在:{$resultsPath}\n";
            return [];
        }
        // 解析SARIF文件并存储到数据库
        $sarifContent = file_get_contents($resultsPath);
        $sarifData = json_decode($sarifContent, true);

        $data = [];
        $processedFiles = []; // 用于存储已处理的文件，避免重复处理
        
        foreach ($sarifData['runs'][0]['results'] as $result) {
            $data[] = [
                'ruleId' => $result['ruleId'],
                'message' => $result['message']['text'],
                'locations' => json_encode($result['locations']),
                'codeFlows' => json_encode($result['codeFlows'] ?? []),
                'prompt' => rtrim(self::getPrompt($result,$gitInfo)),
                'user_id' => $gitInfo['user_id'],
                'project_id' => $gitInfo['project_id'],
                'code_addr_id' => $gitInfo['id'],
                'ai_message' => ''
            ];

            // 提取所有相关文件路径并保存到codeql_code表
            $allLocations = [];
            // 添加结果中的locations
            $allLocations = array_merge($allLocations, $result['locations']);
            // 添加codeFlows中的locations
            if (!empty($result['codeFlows'])) {
                foreach ($result['codeFlows'] as $codeFlow) {
                    foreach ($codeFlow['threadFlows'] as $threadFlow) {
                        foreach ($threadFlow['locations'] as $location) {
                            $allLocations[] = $location['location'];
                        }
                    }
                }
            }
            
            // 处理所有locations，提取文件路径
            foreach ($allLocations as $location) {
                $filePath = $location['physicalLocation']['artifactLocation']['uri'];
                $fileKey = $gitInfo['project_id'] . '_' . $filePath;
                
                // 如果该文件已处理，则跳过
                if (isset($processedFiles[$fileKey])) {
                    continue;
                }
                
                try {
                    // 构建完整的文件路径
                    if (strpos($filePath, "file:") === false) {
                        $fullPath = "{$gitInfo['code_path']}/{$filePath}";
                    } else {
                        $fullPath = str_replace("file:", "", $filePath);
                    }
                    
                    // 读取文件内容
                    if (file_exists($fullPath)) {
                        $content = file_get_contents($fullPath);
                        
                        // 保存到codeql_code表
                        $codeData = [
                            'project_id' => $gitInfo['project_id'],
                            'file_path' => $filePath,
                            'content' => $content,
                            'create_time' => date('Y-m-d H:i:s'),
                            'update_time' => date('Y-m-d H:i:s')
                        ];
                        
                        // 使用insertOrUpdate确保只保存一次
                        Db::table('codeql_code')->insertOrUpdate($codeData, ['project_id', 'file_path']);
                        
                        // 标记为已处理
                        $processedFiles[$fileKey] = true;
                    }
                } catch (\Exception $e) {
                    // 如果处理文件时出错，记录错误并继续处理其他文件
                    echo "处理文件时出错: {$filePath} - {$e->getMessage()}\n";
                }
            }
        }

        foreach ($sarifData['runs'][0]['tool']['driver']['rules'] as $rule) {

            $properties = $rule['properties'];
            $properties['tags'] = json_encode($properties['tags'], JSON_UNESCAPED_UNICODE);
            $properties['security_severity'] = $properties['security-severity'];
            unset($properties['security-severity']);
            $properties['problem_severity'] = $properties['problem.severity'];
            unset($properties['problem.severity']);
            $properties['sub_severity'] = $properties['sub-severity'] ?? '';
            unset($properties['sub-severity']);
            Db::table('codeql_rules')->extra('IGNORE')->strict(false)->insert($properties);

        }

        return $data;

    }


    public static function execTool($codePath, $language = 'python')
    {
        //语言类型
        if (!file_exists($codePath)) return false;
        if (!in_array($language, ['python', 'java', 'javascript', 'go'])) $language = 'python';

        //获取codepath最后的名字
        $dirName = basename($codePath) . date('YmdH');
        $repoDbPath = "extend/codeql/repo-db/{$dirName}";
        $qlPackPath = "extend/codeql/rules/{$language}/ql/src/Security/";
        $resultsPath = "extend/codeql/results/{$dirName}.sarif";

        //创建目录
        if (!file_exists(dirname($repoDbPath))) mkdir(dirname($repoDbPath), 0777, true);
        if (!file_exists(dirname($resultsPath))) mkdir(dirname($resultsPath), 0777, true);

        // 使用用户指定的CodeQL完整路径
        $codeqlBinary = '/Users/song/mycode/neolix/swallow/codeql/codeql';

        // 对于Java项目，先删除旧的数据库以确保重新构建
        if ($language == 'java' && file_exists($repoDbPath)) {
            echo "删除旧的Java数据库: {$repoDbPath}\n";
            $cmd = "rm -rf {$repoDbPath}";
            exec($cmd);
        }
        
        if (!file_exists($repoDbPath)) {
            // 创建CodeQL数据库，使用--command true参数跳过构建过程
            $cmd = "{$codeqlBinary} database create $repoDbPath --language={$language} --source-root $codePath --build-mode=none";
            echo "使用--command true参数跳过构建过程\n";
            echo $cmd . PHP_EOL;
            exec($cmd);
        }

        // 检查database是否已经finalize（通过检查是否存在results目录）
        if (file_exists($repoDbPath . '/results')) {
            echo "数据库已完成finalize\n";
        } else {
            $cmd = "{$codeqlBinary} database finalize {$repoDbPath}";
            echo $cmd . PHP_EOL;
            exec($cmd);
        }

        // 分析代码
        $cmd = "{$codeqlBinary} database analyze $repoDbPath $qlPackPath --format=sarifv2.1.0 --output=$resultsPath ";
        echo $cmd . PHP_EOL;
        exec($cmd);

        return $resultsPath;

    }

    public static function getSourceCodeSnippet($filePath, $startLine, $endLine,$gitInfo)
    {
        $originalFilePath = $filePath;
        if (strpos($filePath, "file:") !== false) {
            $filePath = str_replace("file:", "", $filePath);
        } else {
            $filePath = "{$gitInfo['code_path']}/{$filePath}";
        }

        $sourceCodeSnippet = '';
        $fileContent = file($filePath);
        for ($i = $startLine - 1; $i < $endLine; $i++) {
            if (isset($fileContent[$i])) {
                $sourceCodeSnippet .= $fileContent[$i];
            }
        }

        // 将完整文件内容保存到codeql_code表
        $fullContent = file_get_contents($filePath);
        
        // 确保codeql_code表存在
        $createTableSql = "CREATE TABLE IF NOT EXISTS `codeql_code` (
            `id` int NOT NULL AUTO_INCREMENT,
            `project_id` int NOT NULL DEFAULT '0' COMMENT '项目ID',
            `file_path` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '文件路径',
            `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '文件内容',
            `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
            `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE KEY `project_id_file_path` (`project_id`,`file_path`) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;";
        Db::execute($createTableSql);

        // 保存或更新文件内容
        $existingContent = Db::table('codeql_code')
            ->where(['project_id' => $gitInfo['project_id'], 'file_path' => $originalFilePath])
            ->find();

        if ($existingContent) {
            // 更新现有记录
            Db::table('codeql_code')
                ->where(['id' => $existingContent['id']])
                ->update([
                    'content' => $fullContent
                ]);
        } else {
            // 插入新记录
            Db::table('codeql_code')->insert([
                'project_id' => $gitInfo['project_id'],
                'file_path' => $originalFilePath,
                'content' => $fullContent
            ]);
        }

        return $sourceCodeSnippet;
    }

    public static function getPrompt($result,$gitInfo): string
    {

        // 提取漏洞信息
//        if (!isset($result['locations'][0]['physicalLocation']['artifactLocation']['uri'])) return '';
        $filePath = $result['locations'][0]['physicalLocation']['artifactLocation']['uri'];
        $lineNumber = $result['locations'][0]['physicalLocation']['region']['startLine'];
        $vulnerabilityDescription = $result['message']['text'];

        // 初始化代码片段
        $sourceCodeSnippets = '';

        // 遍历 codeFlows 以提取所有相关代码片段
        if (isset($result['codeFlows'][0]['threadFlows'][0]['locations'])) {
            foreach ($result['codeFlows'][0]['threadFlows'][0]['locations'] as $location) {
                if (isset($location['location']['physicalLocation']['region'])) {

                    $filePath = $location['location']['physicalLocation']['artifactLocation']['uri'];
                    $startLine = $location['location']['physicalLocation']['region']['startLine'];
                    $endLine = $location['location']['physicalLocation']['region']['endLine'] ?? $startLine;
                    $snippet = self::getSourceCodeSnippet($filePath, $startLine, $endLine,$gitInfo);
                    $sourceCodeSnippets .= "代码 {$filePath} 片段（行 $startLine 到 $endLine ）：\n$snippet\n\n";
                }
            }
        } else {
            // 如果没有 codeFlows 信息，使用漏洞位置的行号
            $startLine = $lineNumber;
            $endLine = $lineNumber;
            $sourceCodeSnippets = self::getSourceCodeSnippet($filePath, $startLine, $endLine,$gitInfo);
        }

        // 构建审计提示
        $auditPrompt = "审计提示：\n";
        $auditPrompt .= "-------------------------------\n";
        $auditPrompt .= "漏洞类型: {$result['ruleId']}\n";
        $auditPrompt .= "文件路径: $filePath\n";
        $auditPrompt .= "行号: $lineNumber\n\n";
        $auditPrompt .= "漏洞描述:\n$vulnerabilityDescription\n\n";
        $auditPrompt .= "漏洞代码片段:\n$sourceCodeSnippets\n";
        $auditPrompt .= "-------------------------------\n";

        return $auditPrompt;
    }
}