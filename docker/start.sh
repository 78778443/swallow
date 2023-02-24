cd /root/code && git clone --depth=1 https://gitee.com/songboy/dolphin.git
cd /root/code/dolphin && git fetch && git reset --hard origin/master
chmod -R 777 /root/code/dolphin
cd /root/code/dolphin && cp .example.env .env && composer install
/usr/sbin/php-fpm7.4 -R
/usr/sbin/nginx

sleep 10000000000