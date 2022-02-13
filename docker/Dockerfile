FROM php:7.4-cli-alpine

ENV PHPIZE_DEPS \
    autoconf \
    cmake \
    file \
    g++ \
    gcc \
    libc-dev \
    pcre-dev \
    make \
    git \
    pkgconf \
    re2c \
    # for intl extension
    icu-dev \
    # for zip extension
    libzip-dev

RUN apk add --update --no-cache --virtual .persistent-deps \
    # for intl extension
    icu-libs \
    # for mbstring
    oniguruma-dev \
    # for zip
    libzip \
    libgcrypt

# Compile and install extensions
RUN set -xe \
        && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
        && docker-php-ext-configure intl --enable-intl \
        && docker-php-ext-configure mbstring --enable-mbstring \
        && docker-php-ext-configure opcache --enable-opcache \
        && docker-php-ext-install -j$(nproc) \
            intl \
            mbstring \
            opcache \
            zip \
        && pecl install xdebug \
        && apk del .build-deps


# Install Blackfire PHP probe
RUN set -xe \
        && version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
        && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/alpine/amd64/$version \
        && mkdir -p /tmp/blackfire \
        && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
        && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
        && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8307\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
        && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

# Install Blackfire client
RUN set -xe \
        && mkdir -p /tmp/blackfire \
        && curl -A "Docker" -o /tmp/blackfire/blackfire -D - -L -s https://packages.blackfire.io/binaries/blackfire/2.4.3/blackfire-linux_amd64 \
        && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
        && chmod +x /usr/bin/blackfire \
        && rm -Rf /tmp/blackfire

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Install other needed binaries
RUN apk add --no-cache --update patch git

# Configure PHP
COPY config/php.ini     /usr/local/etc/php/conf.d/
COPY config/opcache.ini /usr/local/etc/php/conf.d/
COPY config/xdebug.ini  /usr/local/etc/php/conf.d/

VOLUME ["/app"]
WORKDIR /app
