FROM php:apache
RUN apt-get update && apt-get install -y msmtp && rm -rf /var/lib/apt/lists/*
COPY php.ini /usr/local/etc/php/conf.d/mail.ini
COPY html-as-php.conf /etc/apache2/conf-enabled/html-as-php.conf
