FROM php:7.4.6-fpm as base

LABEL maintainer="Plamen Petrov <plamen.milchev.petrov@gmail.com>"

# Arguments defined in docker-compose.yml
ARG USER
ARG UID

# Create system user to run Composer and Artisan Commands
RUN adduser --system --no-create-home --group --uid $UID $USER

# Install system dependencies
RUN apt-get update \
    && apt-get install -y apt-transport-https lsb-release pkg-config \
    && curl https://packages.sury.org/php/apt.gpg --output /etc/apt/trusted.gpg.d/php.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list \
    && echo "deb https://deb.debian.org/debian stretch-backports main" |  tee /etc/apt/sources.list.d/backports.list \
    && apt-get clean \
    && apt-get update \
    && apt-get -t stretch-backports install -y libonig-dev libxml2-dev zip unzip librdkafka-dev libcurl4-openssl-dev

# PHP-FPM Configuration
COPY docker/app/php/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/user = .*/user = $USER/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = .*/group = $USER/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/listen.owner.*/listen.owner = $USER/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/listen.group.*/listen.group = $USER/g" /usr/local/etc/php-fpm.d/www.conf

RUN pecl install rdkafka
RUN docker-php-ext-enable rdkafka
RUN docker-php-ext-install pdo pdo_mysql bcmath ctype fileinfo json mbstring curl tokenizer xml

# Clear cache
RUN apt-get autoremove -y && \
    apt-get autoclean -y && \
    apt-get clean -y && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set working directory
WORKDIR /var/www/html

#####################
# Development image #
#####################
FROM base as development

ARG USER
ARG XDEBUG_REMOTE_HOST
ARG XDEBUG_REMOTE_PORT
ARG XDEBUG_IDE_KEY

# Install development system dependencies
RUN apt-get update \
    && apt-get install -y procps vim iputils-ping

# Install PHP extensions
RUN pecl install xdebug-2.8.1 \
    && docker-php-ext-enable xdebug

# xdebug configuration
RUN echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.profiler_enable = 0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host = $XDEBUG_REMOTE_HOST" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port = $XDEBUG_REMOTE_PORT" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey = $XDEBUG_IDE_KEY" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_handler = dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back = 0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_log = /var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.max_nesting_level = 1000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.profiler_output_dir = /tmp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.var_display_max_depth = 10" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.default_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN touch /var/log/xdebug.log \
    && chown $USER:$USER /var/log/xdebug.log

# Clear system cache
RUN apt-get autoremove -y && \
    apt-get autoclean -y && \
    apt-get clean -y && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Optional: Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
