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
    <div class="col-11 tuchu" style="border-radius: 5px;">
        <div class="row">
            <?php foreach ($countList as $item) { ?>
                <div class="col-3">
                    <div style="height: 155px;margin-bottom:20px;border-radius: 10px;padding:10px;border: 1px solid #eee;">
                        <p style="color: #ccc;font-weight:bold;">{$item['name']}</p>
                        <h4>{$item['num']}</h4>
                        <?php foreach ($item['lists'] as $tag => $num) { ?>
                            <span style="color: #ccc;">{$tag}</span> <span>{$num}</span>&nbsp;
                        <?php } ?>
                    </div>

                </div>
            <?php } ?>

        </div>
        <div class="row">
            <div class="col-12">


                &nbsp;
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    &nbsp;
                    <input type="text" style="width:200px;" class="form-control" placeholder="按信息进行查询">
                </div>


            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div style="margin-top:20px;">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="color:#aaa;">id</th>
                            <th style="color:#aaa;">漏洞类型</th>
                            <th style="color:#aaa;">文件名</th>
                            <th style="color:#aaa;">所属项目</th>
                            <th style="color:#aaa;">所属仓库</th>
                            <th style="color:#aaa;">发现时间</th>
                            <th style="color:#aaa;">是否漏洞</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bugList['list'] as $item) { ?>
                            <tr>
                                <td>{$item['id']}</td>
                                <td>{$item['type']}</td>
                                <td><span title="{$item['filename']}">{:basename($item['filename'])}</span></td>
                                <td>{$item['project_id']}</td>
                                <td><a href="{$item['git_addr']}" title="{$item['git_addr']}" target="_blank">{:parse_url($item['git_addr'],PHP_URL_PATH)}</a></td>
                                <td>{$item['create_time']}</td>
                                <td>{$item['is_repair']}</td>
                             </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-12">
                {include file='public/fenye' /}
            </div>
        </div>

    </div>
</div>