<?php
// 应用公共文件


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

function execLog($cmd){
    echo $cmd.PHP_EOL;
    return exec($cmd);
}
