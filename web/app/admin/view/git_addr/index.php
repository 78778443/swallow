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
                    <h6 style="color:#ccc;">仓库总数</h6>
                    <h4>{$totalNum}</h4>
                </div>

                <div class="col-8">

                </div>
            </div>
        </div>
        <div style="height:20px;"></div>
        <div class="row">
            <div class="accordion" id="accordionExample">

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col" style="color: #aaa;">ID</th>
                        <th scope="col" style="color: #aaa;">仓库地址</th>
                        <th scope="col" style="color: #aaa;">创建时间</th>
                        <th scope="col" style="color: #aaa;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mainList as $item) { ?>
                        <tr>
                            <td>{$item['id']}</td>
                            <td>{$item['git_addr']}</td>
                            <td>{$item['create_time']}</td>
                            <td>
                                <a class="btn btn-sm btn-light"  target="_blank" >查看</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                {include file='public/fenye' /}
            </div>
        </div>
    </div>
</div>

</div>

