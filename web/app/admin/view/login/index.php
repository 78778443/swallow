<html>
<head>
    <title><?php echo $title ?? 'Swallow 代码审计系统' ?></title>
    <link rel="shortcut icon" href="/static/images/logo_blue.png" type="image/x-icon"/>
    <script crossorigin="anonymous" src="https://lib.baomitu.com/jquery/2.2.4/jquery.min.js"></script>
    <link crossorigin="anonymous" href="https://lib.baomitu.com/twitter-bootstrap/5.2.3/css/bootstrap.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/qingscan.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script crossorigin="anonymous" src="https://lib.baomitu.com/twitter-bootstrap/5.2.3/js/bootstrap.min.js"></script>
    <meta name="keywords" content="">
    <meta name="description"
          content="">
</head>
<body class="text-center">
<div class="container">

<div style="height: 200px;"></div>
<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
            <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
                    id="bd-theme"
                    type="button"
                    aria-expanded="false"
                    data-bs-toggle="dropdown"
                    aria-label="Toggle theme (auto)">
                <svg class="bi my-1 theme-icon-active" width="1em" height="1em">
                    <use href="#circle-half"></use>
                </svg>
                <span class="visually-hidden" id="bd-theme-text">切换主题</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
                <li>
                    <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light"
                            aria-pressed="false">
                        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em">
                            <use href="#sun-fill"></use>
                        </svg>
                        Light
                        <svg class="bi ms-auto d-none" width="1em" height="1em">
                            <use href="#check2"></use>
                        </svg>
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark"
                            aria-pressed="false">
                        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em">
                            <use href="#moon-stars-fill"></use>
                        </svg>
                        Dark
                        <svg class="bi ms-auto d-none" width="1em" height="1em">
                            <use href="#check2"></use>
                        </svg>
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto"
                            aria-pressed="true">
                        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em">
                            <use href="#circle-half"></use>
                        </svg>
                        Auto
                        <svg class="bi ms-auto d-none" width="1em" height="1em">
                            <use href="#check2"></use>
                        </svg>
                    </button>
                </li>
            </ul>
        </div>


        <main class="form-signin w-100 m-auto">
            <form method="post" action="{:URL('_dologin')}">
                <h1 class="h3 mb-3 fw-normal">请登录</h1>

                <div class="form-floating">
                    <input type="text" name="username" class="form-control"   placeholder="zhangsan">
                    <label for="floatingInput">用户名</label>
                </div>
                <br>
                <div class="form-floating">
                    <input type="password" name="password" class="form-control"   placeholder="Password">
                    <label for="floatingPassword">密码</label>
                </div>

                <br>
                <button class="w-100 btn btn-lg btn-primary" type="submit">登录</button>
            </form>
        </main>
    </div>
    <div class="col-md-4"></div>
</div>


</div>
</body>
</html>