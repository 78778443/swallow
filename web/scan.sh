#!/bin/bash

while true; do
    php think scan git -vvv
    php think scan fortify -vvv
    php think scan semgrep -vvv
    php think scan hema -vvv
    sleep 1  # 可选：在每次循环之间暂停1秒，以减少对系统的负载
done