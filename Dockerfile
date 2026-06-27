FROM php:8.2-apache AS ospos
LABEL maintainer="jekkos"

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libgd-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    gnupg \
    default-mysql-client \
    && mkdir -p /etc/mysql/conf.d && printf "[client]\nhost=mysql\nprotocol=tcp\nssl=0\n" > /etc/mysql/conf.d/default-host.cnf \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && docker-php-ext-install mysqli bcmath intl gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

RUN echo "date.timezone = \"\${PHP_TIMEZONE}\"" > /usr/local/etc/php/conf.d/timezone.ini

WORKDIR /app
ENV ALLOWED_HOSTNAMES=localhost
ENV FORCE_HTTPS=false
ENV APP_BASE_URL=http://localhost:8080/
COPY --chown=www-data:www-data . /app

# Copy composer and install dependencies (PHP & Node)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN npm install
RUN npm run build
RUN chown -R www-data:www-data /app

RUN chmod 750 /app/writable/logs /app/writable/uploads /app/writable/cache /app/public/uploads /app/public/uploads/item_pics \
    && chmod 640 /app/writable/uploads/importCustomers.csv \
    && ln -s /app/*[^public] /var/www \
    && rm -rf /var/www/html \
    && ln -nsf /app/public /var/www/html

FROM ospos AS ospos_dev

ARG USERID
ARG GROUPID

RUN echo "Adding user uid $USERID with gid $GROUPID"
RUN ( addgroup --gid ${GROUPID:-1000} ospos || true ) && ( adduser --disabled-password --gecos "" --uid ${USERID:-1000} --gid ${GROUPID:-1000} ospos )

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini
