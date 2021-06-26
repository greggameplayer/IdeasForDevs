FROM php:7.4-fpm-alpine

# Apk install
RUN apk --no-cache update && apk --no-cache add bash git

RUN apk --update add --virtual build-dependencies build-base openssl-dev autoco>
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && apk del build-dependencies build-base openssl-dev autoconf \
  && rm -rf /var/cache/apk/*

# Install pdo
RUN docker-php-ext-install pdo_mysql

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" &>

ENV COMPOSER_ALLOW_SUPERUSER 1

# Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony>

WORKDIR /var/www/html

COPY . /var/www/html/

RUN composer install

ENV DATABASE_URL mysql://IDF:gg123456@127.0.0.1:3306/IdeasToDev?serverVersion=5>

RUN touch .env.local

RUN (printenv | egrep 'DATABASE|APP') > .env.local

RUN symfony console doctrine:database:create -n --if-not-exists

RUN symfony console make:migration -n

RUN symfony console doctrine:migrations:migrate --no-interaction --allow-no-mig>

ENV APP_ENV prod

RUN (printenv | egrep 'DATABASE|APP') > .env.local

RUN symfony console cache:clear -n

EXPOSE 9000
