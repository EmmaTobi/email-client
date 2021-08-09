FROM php:7.4-fpm-alpine
 
WORKDIR /var/www/html
 
COPY backend .

RUN set -xe && \
    apk add --update --no-cache \
        imap-dev \
        openssl-dev \
        krb5-dev && \
    (docker-php-ext-configure imap --with-kerberos --with-imap-ssl) && \
    (docker-php-ext-install imap > /dev/null) && \
    php -m | grep -F 'imap'
        
RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

USER laravel 
 
# RUN chown -R laravel:laravel .