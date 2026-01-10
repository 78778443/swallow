<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\model\CodeQlModel;
use think\facade\Db;
use think\facade\Session;
use think\facade\View;
use think\Request;

class CodeQl extends Common
{
    public function index(Request $request)
    {
        // 获取当前用户信息
        $userInfo = Session::get('userInfo');
        if (empty($userInfo)) {
            $this->redirect('/admin/login');
        }

        // 获取参数
        $projectId = $request->param('project_id', 0);
        $rule_id = $request->param('rule_id', '');
        $extra = $request->param('extra', '');
        $status = $request->param('status', 'all');
        $problem_severity = $request->param('problem_severity', null);
        $rule_kind = $request->param('rule_kind', null);
        $security_severity = $request->param('security_severity', null);

        // 构建查询条件
        $where = [];
        if (!empty($projectId)) {
            $where['project_id'] = $projectId;
        }
        if (!empty($rule_id)) {
            $where['ruleId'] = $rule_id;
        }
        if (!empty($problem_severity)) {
            $where['problem_severity'] = $problem_severity;
        }
        if (!empty($rule_kind)) {
            $where['kind'] = $rule_kind;
        }
        if (!empty($security_severity)) {
            $where['security_severity'] = $security_severity;
        }

        // 获取所有唯一的漏洞类型
        $rule_types = Db::name('codeql')
            ->distinct(true)
            ->field('ruleId, name')
            ->where($where)
            ->select()
            ->toArray();


        // 根据状态筛选
        if ($status != 'all') {
            $where['security_severity'] = $status;
        }

        // 查询数据并分页 - 现在直接使用codeql表中的字段
        $list = Db::name('codeql')
            ->where($where)
            ->order('id desc')
            ->paginate([
                'list_rows' => 15,
                'var_page' => 'page',
                'query' => $request->param(),
            ]);

        $bugList['list'] = $list->items();
        $page = $list->render();


        // 准备数据
        $data = [
            'bugList' => $bugList,
            'page' => $page,
            'project_id' => $projectId,
            'rule_types' => $rule_types,
            'param' => $request->param(),
            'status' => $status,
            'problem_severity' => $problem_severity,
            'rule_kind' => $rule_kind,
            'security_severity' => $security_severity
        ];

        return View::fetch('index', $data);
    }


    public function detail(Request $request)
    {
        $userInfo = Session::get('userInfo');
        $id = $request->param('id');
        $where = ['id' => $id];
        $info = Db::table('codeql')->where($where)->find();


        $info['codeFlows'] = json_decode($info['codeFlows'], true);
        foreach ($info['codeFlows'] as &$item) {
            foreach ($item['threadFlows'] as &$val) {
                foreach ($val['locations'] as &$v) {
                    $v['location'] = $this->parseSarif($v['location']);
                }
            }

        }

        $info['locations'] = $this->parseSarif(json_decode($info['locations'], true));
        $info['prompt'] = str_replace("\n", "<br>", $info['prompt']);


        return View::fetch('detail', ['info' => $info]);
    }

    private function parseSarif($list)
    {

        $results = [];
        foreach ($list as $location) {
            if (!isset($location['physicalLocation'])) $location['physicalLocation'] = $location;

            if (!isset($location['physicalLocation']['artifactLocation']['uri'])) continue;
            $artifactLocation = $location['physicalLocation']['artifactLocation']['uri'];
            $region = $location['physicalLocation']['region'];
            $startLine = $region['startLine'];
            $startColumn = $region['startColumn'];
            $endColumn = $region['endColumn'];
            $results[] = [
                'file' => $artifactLocation,
                'start_line' => $startLine,
                'start_column' => $startColumn,
                'end_column' => $endColumn
            ];


        }

        return $results;
    }

    public function readFile(Request $request)
    {
        $filePath = $request->param('file');
        $code_addr_id = $request->param('code_addr_id');


        // Ensure the file path is safe
        $realBase = Db::table('git_addr')->where(['project_id' => $code_addr_id])->value('code_path');
        $realUserPath = realpath($realBase . "/" . $filePath);

        // Check for directory traversal attempts
        if ($realUserPath === false || strpos($realUserPath, $realBase) !== 0) {

            http_response_code(400);
            echo json_encode(['error' => 'Invalid file path']);
            exit;
        }

        // Check if the file exists and is readable
        if (!file_exists($realUserPath) || !is_readable($realUserPath)) {
            http_response_code(404);
            echo json_encode(['error' => 'File not found or not readable']);
            exit;
        }

        // Return file content
        $content = file_get_contents($realUserPath);
        $encodedContent = base64_encode($content);
        return json(['content' => $encodedContent]);

    }
}