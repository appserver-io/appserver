################################################################################
# Dockerfile for appserver.io main Docker distribution
################################################################################

# base image
FROM debian:jessie

# author
MAINTAINER Tim Wagner <tw@appserver.io>

################################################################################

# define versions
ENV APPSERVER_RUNTIME_BUILD_VERSION 1.1.1-39

# install packages
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install supervisor wget git -y

################################################################################

# download runtime in specific version
RUN wget -O /tmp/appserver-runtime.deb \
    http://builds.appserver.io/linux/debian/8/appserver-runtime_${APPSERVER_RUNTIME_BUILD_VERSION}~deb8_amd64.deb

# install runtime
RUN dpkg -i /tmp/appserver-runtime.deb; exit 0

# install missing runtime dependencies
RUN apt-get install -yf
    
# remove the unnecessary .deb file
RUN rm -f /tmp/appserver-runtime.deb
    
# create a symlink for the appserver.io PHP binary
RUN ln -s /opt/appserver/bin/php /usr/local/bin/php

################################################################################

# copy the appserver sources
ADD . /opt/appserver

################################################################################

# define working directory
WORKDIR /opt/appserver

################################################################################

# create a symlink for the appserver.io Composer binary
RUN ln -s /opt/appserver/bin/composer.phar /usr/local/bin/composer \

    # install composer dependencies
    &&  composer install --prefer-dist --no-dev --no-interaction --optimize-autoloader \

    # modify user-rights in configuration
    &&  sed -i "s/www-data/root/g" etc/appserver/appserver.xml \

    # create a symlink to the supervisord configuration file
    && ln -s /opt/appserver/etc/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

################################################################################

# forward request and error logs to docker log collector
RUN ln -sf /dev/stderr /opt/appserver/var/log/php_errors.log \
    && ln -sf /dev/stdout /opt/appserver/var/log/appserver-access.log \
    && ln -sf /dev/stderr /opt/appserver/var/log/appserver-errors.log

################################################################################

# expose ports
EXPOSE 9080 9443

# supervisord needs this
CMD []

# define default command
ENTRYPOINT ["/usr/bin/supervisord"]
