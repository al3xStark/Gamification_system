mkdir /etc/nginx/sites-enabled \;
find /etc/nginx/sites-available/ -name '*.local' -exec cp -f "{}" /etc/nginx/sites-enabled/  \;

nginx
