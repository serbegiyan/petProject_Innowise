FROM jenkins/jenkins:lts

USER root

RUN apt-get update && apt-get install -y \
    php php-xml php-mbstring php-zip php-curl php-cli php-common php-tokenizer php-json php-mysql \
    git unzip curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER jenkins
