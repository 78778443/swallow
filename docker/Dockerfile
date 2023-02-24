FROM daxia/qingting:base_ubuntu_php_python

COPY ./sources.list  /etc/apt/sources.list
COPY ./default.conf /etc/nginx/sites-enabled/default
RUN  apt update -y
RUN  apt install -y nginx php-fpm  nginx php-fpm vim curl  net-tools composer php-curl
RUN sed -i "s/www-data/root/g" /etc/nginx/nginx.conf && sed -i "s/www-data/root/g" /etc/php/7.4/fpm/pool.d/www.conf
COPY ./start.sh  /root/start.sh
RUN chmod 777 /root/start.sh

WORKDIR /root/code


CMD ["sh","/root/start.sh"]