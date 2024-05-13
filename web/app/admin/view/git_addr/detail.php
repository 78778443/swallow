{include file='public/head' /}

<style>
    a {
        text-decoration: none;
    }

    li {
        list-style: none;
    }
</style>
<div class="row">
    <div class="col-1 ">
        {include file='Common/nav' /}
    </div>
    <div class="col-11 tuchu" style="border-radius:5px;">
        <div class="row">
            <div class="row">
                <div class="col-4">
                    <span style="color:#aaa;font-weight: bold;font-size: 16px;">项目名称:</span>
                    <span><?= empty($prInfo['name']) ? '' : $prInfo['name'] ?></span>
                </div>
                <div class="col-8">
                    <span style="color:#aaa;font-weight: bold;font-size: 16px;">项目描述:</span>
                    <span><?= empty($prInfo['desc']) ? '' : $prInfo['desc'] ?></span>
                </div>
                <div class="col-4">
                    <span style="color:#aaa;font-weight: bold;font-size: 16px;">fortify扫描时间:</span>
                    <span><?= empty($gitInfo['fortify_scan_time']) ? '' : $gitInfo['fortify_scan_time'] ?></span>
                </div>
                <div class="col-4">
                    <span style="color:#aaa;font-weight: bold;font-size: 16px;">semgrep扫描时间:</span>
                    <span><?= empty($gitInfo['semgrep_scan_time']) ? '' : $gitInfo['semgrep_scan_time'] ?></span>
                </div>
                <div class="col-4">
                    <span style="color:#aaa;font-weight: bold;font-size: 16px;">hema扫描时间:</span>
                    <span><?= empty($gitInfo['hema_scan_time']) ? '' : $gitInfo['hema_scan_time'] ?></span>
                </div>
                <div style="height:20px;"></div>
                <div class="col-4">
                    <div style="border-radius:10px;border: 1px solid #ccc;padding:10px;">
                        <h6 style="color:#ccc;">fortify</h6>
                        <h4>{$fortifyCount}</h4>
                    </div>
                    <div style="height:20px;"></div>
                    <div style="border-radius:10px;border: 1px solid #ccc;padding:10px;">
                        <table class="table table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th style="color:#aaa;">缺陷文件</th>
                                <th style="color:#aaa;">漏洞类型</th>
                                <th style="color:#aaa;">漏洞等级</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($fortifyList as $item) {
                                $item['primary_info'] = json_decode($item['Primary'], true);

                                ?>
                                <tr>
                                    <td>{$item['primary_info']['FileName']}</td>
                                    <td>{$item['Category']}</td>
                                    <td>{$item['Folder']}</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="d-grid gap-2">
                            <a class="btn btn-outline-secondary" target="_blank" href="{:URL('scan/report',['project_id'=>$gitInfo['project_id']])}" >More</a>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-radius:10px;border: 1px solid #ccc;padding:10px;"><h6 style="color:#ccc;">
                            SemGrep</h6>
                        <h4>{$semgrepCount}</h4>
                    </div>
                    <div style="height:20px;"></div>
                    <div style="border-radius:10px;border: 1px solid #ccc;padding:10px;">
                        <table class="table table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th style="color:#aaa;">缺陷文件</th>
                                <th style="color:#aaa;">漏洞类型</th>
                                <th style="color:#aaa;">漏洞等级</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($semgrepList as $item) {
                                $item['extra'] = json_decode($item['extra'], true);
                                ?>
                                <tr>
                                    <td><span title="{$item['path']}">{:basename($item['path'])}</span></td>
                                    <td>{$item['check_id']}</td>
                                    <td>{$item['extra']['metadata']['confidence']}</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="d-grid gap-2">
                            <a class="btn btn-outline-secondary" target="_blank" href="{:URL('semgrep/index',['project_id'=>$gitInfo['project_id']])}" >More</a>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-radius:10px;border: 1px solid #ccc;padding:10px;">
                        <h6 style="color:#ccc;">Hema</h6>
                        <h4>{$hemaCount}</h4>
                    </div>
                    <div style="height:20px;"></div>
                    <div style="border-radius:10px;border: 1px solid #ccc;padding:10px;">
                        <table class="table table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th style="color:#aaa;">漏洞类型</th>
                                <th style="color:#aaa;">文件名</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($hemaList as $item) { ?>
                                <tr>
                                    <td>{$item['type']}</td>
                                    <td><span title="{$item['filename']}">{:basename($item['filename'])}</span></td>

                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="d-grid gap-2">
                            <a class="btn btn-outline-secondary" target="_blank" href="{:URL('hema/index',['project_id'=>$gitInfo['project_id']])}" >More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="height:20px;"></div>

    </div>
</div>
