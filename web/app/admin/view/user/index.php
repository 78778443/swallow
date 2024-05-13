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
                    <h6 style="color:#ccc;">用户总数</h6>
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
                        <th scope="col" style="color: #aaa;">用户名</th>
                        <th scope="col" style="color: #aaa;">管理员</th>
                        <th scope="col" style="color: #aaa;">创建时间</th>
                        <th scope="col" style="color: #aaa;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mainList as $item) { ?>
                        <tr>
                            <td>{$item['id']}</td>
                            <td>{$item['username']}</td>
                            <td>{$item['is_admin']}</td>

                            <td>{$item['create_time']}</td>
                            <td>
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
                        <label class="form-label">用户名</label>
                        <input type="text" name="username" class="form-control" placeholder="zhangsan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">密码</label>
                        <input type="password" name="password" class="form-control" placeholder="zhangsan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">管理员</label>
                        <select name="is_admin" class="form-select" aria-label="Default select example">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">添加</button>
                </form>
            </div>

        </div>
    </div>
</div>