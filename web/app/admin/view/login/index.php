{include file='public/top' /}
<div class="container">

    <div style="height: 200px;"></div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <main class="form-signin w-100 m-auto">
                <form method="post" action="{:URL('_dologin')}">
                    <h1 class="h3 mb-3 fw-normal">请登录</h1>
                    <!-- 错误信息显示区域 -->
                    {if !empty(session('error'))}
                    <div class="alert alert-danger" role="alert">
                        {:session('error')}
                    </div>
                    {/if}

                    <div class="form-floating">
                        <input type="text" name="username" class="form-control" placeholder="zhangsan">
                        <label for="floatingInput">用户名</label>
                    </div>
                    <br>
                    <div class="form-floating">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <label for="floatingPassword">密码</label>
                    </div>

                    <br>
                    <button class="w-100 btn btn-lg btn-primary" type="submit">登录</button>
                    <br>
                    <br>
                    <a class="w-100 btn btn-lg btn-secondary" href="{:URL('register')}">注册</a>
                </form>
            </main>
        </div>
        <div class="col-md-4"></div>
    </div>


</div>
</body>
</html>