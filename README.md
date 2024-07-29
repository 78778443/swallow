### 背景

在软件开发中代码审计是确保代码安全性的重要环节。传统的代码审计方法常常依赖人工审查和静态分析工具，耗时且易于遗漏潜在问题。
为了提升代码审计的效率，我引入了CodeQL和GPT的结合，利用CodeQL的强大分析能力和ChatGPT的自然语言处理能力，实现了代码审计效率的显著提升。

在线体验地址: https://swallow.songboy.site/


![](https://oss.songboy.site/blog/eda192d4f4ada8753f0e4b6ee13ae9a.png)

### 技术方案

我的方案基于两个核心工具：CodeQL和GPT。CodeQL是一种强大的代码分析工具，可以通过查询语言扫描和发现代码中的潜在漏洞和安全隐患。
GPT则通过其强大的自然语言理解和生成能力，提供对漏洞的详细解释和修复建议。

#### 主要步骤如下：

1. **代码扫描**：利用CodeQL对目标代码进行扫描，生成包含漏洞信息的SARIF文件。
2. **数据处理**：解析SARIF文件，提取漏洞信息和相关代码片段。
3. **AI分析**：将提取的信息通过ChatGPT进行处理，生成详细的漏洞描述和修复建议。
4. **结果展示**：将分析结果存储到数据库中，并通过Web界面展示。

### 实践

我们通过一个实际项目实现了这一方案，并进行了详细的实验。以下是代码实现的主要部分：

#### 1\. 代码扫描

通过CodeQL对代码仓库进行扫描，生成SARIF格式的扫描结果文件。


```php
public static function execTool($codePath, $language = 'python') {
// 省略部分代码...
$cmd = "codeql database analyze $repoDbPath $qlPackPath --format=sarifv2.1.0 --output=$resultsPath";
exec($cmd);
return $resultsPath;
}
```

#### 2\. 数据处理

解析SARIF文件，提取漏洞信息和相关代码片段。

```php
public static function WriteData($resultsPath) {
$sarifContent = file_get_contents($resultsPath);
$sarifData = json_decode($sarifContent, true);

    // 省略部分代码...
    foreach ($sarifData['runs'][0]['results'] as $result) {
        $data[] = [
            'ruleId' => $result['ruleId'],
            'message' => $result['message']['text'],
            'locations' => json_encode($result['locations']),
            'codeFlows' => json_encode($result['codeFlows'] ?? []),
            'prompt' => rtrim(self::getPrompt($result)),
            'ai_message' => ''
        ];
    }

    return $data;
}
```

#### 3\. AI分析

将提取的信息通过ChatGPT进行处理，生成详细的漏洞描述和修复建议。

```php
public static function ai_message() {
$list = Db::table('codeql')->where(['ai_message' => ''])->select()->toArray();

    foreach ($list as $value) {
        $aiMessage = ChatGPT::tongyi($value['prompt']);
        $translatedJson = json_decode($aiMessage, true);
        $aiMessage = $translatedJson['output']['choices'][0]['message']['content'] ?? $aiMessage;

        $data = ['ai_message' => $aiMessage];
        Db::table('codeql')->where(['id' => $value['id']])->update($data);
    }
}
```

#### 4\. 结果展示

通过Web界面展示分析结果，便于开发人员查看和处理。

```php
public function detail(Request $request) {
$id = $request->param('id');
$info = Db::table('codeql')->where(['id' => $id])->find();
$info['codeFlows'] = json_decode($info['codeFlows'], true);
$info['locations'] = $this->parseSarif(json_decode($info['locations'], true));
$info['prompt'] = str_replace("\n","<br>",$info['prompt']);

    return View::fetch('detail', ['info' => $info]);
}
```

### 结论

通过将CodeQL与GPT相结合，可以实现代码审计效率的显著提升。CodeQL提供了高效的漏洞检测能力，而ChatGPT则通过其强大的自然语言处理能力，提供了详尽的漏洞描述和修复建议。
不仅提高了代码审计的效率，还有效减少人工审计的工作量，提升了代码质量和安全性。

### 联系我

如果您对这一方案感兴趣，或有任何问题和建议，请随时联系我。

邮箱：78778443@qq.com

微信：songboy8888

