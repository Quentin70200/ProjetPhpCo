FROM php:8.1.23-apache-bullseye

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Installez le pilote pdo_mysql
RUN docker-php-ext-install pdo_mysql

#Installez Git#
RUN apt-get update && apt-get install -y git

#Installez MailHog
RUN apt-get update && apt-get install -y golang-go
RUN go get github.com/mailhog/MailHog
RUN go get github.com/mailhog/mhsendmail

#Configuration PHP pour utiliser mhsendmail comme programme d'envoie de courrier
RUN echo "sendmail_path = /go/bin/mhsendmail --smtp-addr mailhog:1025" > "$PHP_INI_DIR/php.ini"

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
