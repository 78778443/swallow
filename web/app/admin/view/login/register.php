{include file='public/top' /}
<div class="container">

    <div style="height: 200px;"></div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <main class="form-signin w-100 m-auto">
                <form method="post" action="{:URL('_doRegister')}">
                    <h1 class="h3 mb-3 fw-normal">请填写注册信息</h1>

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
                    <div class="form-floating">
                        <input type="password" name="repassword" class="form-control" placeholder="Password">
                        <label for="floatingPassword">再次输入密码</label>
                    </div>

                    <br>
                    <button class="w-100 btn btn-lg btn-primary" type="submit">注册</button>
                    <br>
                    <br>
                    <a class="w-100 btn btn-lg btn-secondary" href="{:URL('index')}">登录</a>
                </form>
            </main>
        </div>
        <div class="col-md-4"></div>
    </div>


</div>
</body>
</html>