FROM php:apache
RUN apt-get update && apt-get install -y \
    msmtp \
    gettext-base \
    libwebp-dev \
    libjpeg-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-webp --with-jpeg \
    && docker-php-ext-install gd \
    && rm -rf /var/lib/apt/lists/*
COPY php.ini /usr/local/etc/php/conf.d/mail.ini
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
