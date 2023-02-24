php ./daochuSql.php

#docker build -t daxia/dolphin:latest .
#docker push daxia/dolphin:latest

docker build -f DockerfileMysql -t daxia/dolphin:mysql57 .
docker push daxia/dolphin:mysql57

