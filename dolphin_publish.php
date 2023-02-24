<?php
publishDocker();
publishGit();

function publishDocker()
{

    //开始发布docker镜像
    $md5 = md5_file("./docker/swallow.sql");
    exec("cd ./docker && php daochuSql.php");
    $newMd5 = md5_file("./docker/swallow.sql");

    if ($md5 != $newMd5) {
        $cmd = "cd ./docker && sh ./docker_build.sh";
        print_r("数据库结构有改变,开始构建数据库镜像: $cmd");
        exec($cmd);
    }
}


function publishGit()
{
    $cmd = "rsync -avz  --delete --exclude-from '/mnt/d/mycode/information-gathering/exclude.txt'  /mnt/d/mycode/information-gathering/web/*  /mnt/d/mycode/dolphin/";
    exec($cmd);
    print_r($cmd.PHP_EOL);

    $cmd = "cp  /mnt/d/mycode/information-gathering/web/.example.env  /mnt/d/mycode/dolphin/.example.env";
    exec($cmd);

    $cmd = "cd /mnt/d/mycode/dolphin && git add . && git commit . -m 'update' && git push";
    exec($cmd.PHP_EOL);
    print_r($cmd);

}