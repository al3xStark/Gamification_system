FROM webdevops/php-nginx:8.2-alpine as base-php
ENV WEB_DOCUMENT_ROOT=/app/public
ENV fpm.pool.clear_env=no
ENV php.variables_order="EGPCS"
ENV PHP_DISMOD=bz2,calendar,exiif,ffi,intl,çgettext,ldap,mysqli,imap,pdo_pgsql,pgsql,soap,sockets,sysvmsg,sysvsm,sysvshm,shmop,xsl,zip,gd,apcu,vips,yaml,imagick,mongodb,amqp
RUN apk add mc
WORKDIR /app
COPY . /app/
COPY Helm/10-storage.conf /opt/docker/etc/nginx/vhost.common.d/10-storage.conf
RUN ln -sfn /app/storage/app/public public/storage
RUN chown -R application:application .
