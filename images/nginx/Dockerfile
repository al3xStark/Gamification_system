FROM nginx:1.20.1

RUN apt-get -y update

RUN apt-get install -y  \
    nginx \
    sudo \
    iproute2 \
    telnet

RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN sed -i "0,/nginx/s/nginx/docker-nginx/i" /usr/share/nginx/html/index.html

COPY ./nginx.conf /etc/nginx/nginx.conf

RUN useradd -ms /bin/bash docker

CMD [ "nginx" ]
