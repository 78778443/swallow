<?php

namespace app\admin\model;

use think\facade\Db;
use think\facade\Session;
use think\Model;

class SemgrepModel extends Model
{
    protected $name = 'semgrep';


    public static function getCount(string $domain, int $projectId)
    {
        $data = [];
        $where = [ ['project_id', '=', $projectId]];

        $data['全部'] = Db::table('semgrep')->where($where)->count();
        $data['严重'] = Db::table('semgrep')->where($where)->where(['Folder' => 'Critical'])->cache(60)->count();
        $data['高危'] = Db::table('semgrep')->where($where)->where(['Folder' => 'High'])->cache(60)->count();
        $data['中危'] = Db::table('semgrep')->where($where)->where(['Folder' => 'Medium'])->cache(60)->count();
        $data['低危'] = Db::table('semgrep')->where($where)->where(['Folder' => 'Low'])->cache(60)->count();

        return $data;
    }

    public static function getDetailCount($where)
    {


        $userInfo = Session::get('userInfo');
        //修复率
        $repairNum = Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->where(['is_repair' => 1])->count();
        $unRepairNum = Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->count();
        $repairCount = (empty($repairNum) && empty($unRepairNum)) ? 0 : intval($repairNum / $unRepairNum);


        $countList = [
            ['name' => '风险总数(个)',
                'num' => Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->count(),
                'lists' => [
                    '今日新增' => Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->whereTime('create_time', '>=', date('Y-m-d', time() - (7 * 86400)))->count(),
                    '本周新增' => Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->whereTime('create_time', '>=', date('Y-m-d', time() - (7 * 86400)))->count(),
                ]
            ],
            ['name' => '受影响仓库(个)', 'num' => Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->group('git_addr')->count(),
                'lists' => [
                    '总数 ' => Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->group('git_addr')->count(),
                    '7天新增 ' => Db::table('semgrep')->where(['user_id' => $userInfo['id']])->where($where)->whereTime('create_time', '>=', date('Y-m-d', time() - (7 * 86400)))->group('git_addr')->count()
                ]
            ],
            ['name' => '修复率', 'num' => $repairCount, 'lists' => ['待修复' => $unRepairNum, '已修复' => $repairNum]],

        ];

        return $countList;
    }


}