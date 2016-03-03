################################################################################
# Dockerfile for appserver.io message-queue service
################################################################################

# base image
FROM debian:jessie

# author
MAINTAINER Tim Wagner <tw@appserver.io>

################################################################################

# define versions
ENV APPSERVER_RUNTIME_BUILD_VERSION 1.1.1-39
ENV APPSERVER_SOURCE_VERSION 1.1.1-alpha7

# define service to build
ENV APPSERVER_SERVICE message-queue

# install packages
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install supervisor wget git -y

################################################################################

# download runtime in specific version
RUN wget -O /tmp/appserver-runtime.deb \
    http://builds.appserver.io/linux/debian/8/appserver-runtime_${APPSERVER_RUNTIME_BUILD_VERSION}~deb8_amd64.deb

# install runtime
RUN dpkg -i /tmp/appserver-runtime.deb; exit 0
RUN apt-get install -yf
RUN rm -f /tmp/appserver-runtime.deb
RUN ln -s /opt/appserver/bin/php /usr/local/bin/php

################################################################################

# install composer
RUN cd /usr/local/bin && php -r "readfile('https://getcomposer.org/installer');" | php
RUN ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

################################################################################

COPY * /opt/appserver
RUN cd /opt/appserver
RUN composer install --prefer-dist --no-dev -no

################################################################################

# add configuration files
COPY docker/message-queue/etc/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/

# override generic appserver.io service configuration
COPY docker/message-queue/opt/appserver/etc/appserver/appserver.xml /opt/appserver/etc/appserver

################################################################################

# forward request and error logs to docker log collector
RUN ln -sf /dev/stderr /opt/appserver/var/log/php_errors.log
RUN ln -sf /dev/stdout /opt/appserver/var/log/appserver-access.log
RUN ln -sf /dev/stderr /opt/appserver/var/log/appserver-errors.log

# define working directory
WORKDIR /opt/appserver

# expose ports
EXPOSE 9080 9443

# supervisord needs this
CMD []

# define default command
ENTRYPOINT ["/usr/bin/supervisord"]