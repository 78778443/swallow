php ./daochuSql.php

docker build -t daxia/swallow:latest .
docker push daxia/swallow:latest

docker build -f DockerfileMysql -t daxia/swallow:mysql57 .
docker push daxia/swallow:mysql57

