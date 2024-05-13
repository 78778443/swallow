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
                    <h6 style="color:#ccc;">项目总数</h6>
                    <h4>{$totalNum}</h4>
                </div>

                <div class="col-6"></div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">添加
                    </button>
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
                        <th scope="col" style="color: #aaa;">项目名词</th>
                        <th scope="col" style="color: #aaa;">创建时间</th>
                        <th scope="col" style="color: #aaa;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mainList as $item) { ?>
                        <tr>
                            <td>{$item['id']}</td>
                            <td>{$item['name']}</td>
                            <td>{$item['create_time']}</td>
                            <td>
                                <!--                                <a class="btn btn-sm btn-light" target="_blank">查看</a>-->
                                <a href="{:URL('_del',['id'=>$item['id']])}" class="btn btn-sm btn-secondary"
                                   target="_blank">删除</a>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                <form action="{:URL('_add')}" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">项目名称</label>
                        <input class="form-control " name="name" type="text" placeholder="name"  >

                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">项目描述</label>
                        <textarea class="form-control" name="desc"
                                  placeholder="项目描述"
                                  rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">仓库地址</label>
                        <textarea class="form-control" name="git_addr"
                                  placeholder="仓库地址"
                                  rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">添加</button>
                </form>
            </div>

        </div>
    </div>
</div>