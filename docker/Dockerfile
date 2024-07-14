FROM yiisoftware/yii2-php:8.1-fpm

ARG GITHUB_TOKEN
ARG MY_USERNAME

RUN mkdir -p /app/public &&\
    mkdir /home/${MY_USERNAME} &&\
    chmod 777 /home/${MY_USERNAME} &&\
    # Change timezone
    rm -rf /etc/localtime &&\
    ln -s /usr/share/zoneinfo/Asia/Kuala_Lumpur /etc/localtime &&\
    # Replace default port 9000 with 3063.
    # Port 3063 will be used in frontend.conf - fastcgi_pass php:3063;
    sed -i 's/9000/3063/' /usr/local/etc/php-fpm.d/zz-docker.conf &&\
    composer config --global github-oauth.github.com ${GITHUB_TOKEN}

USER root

# Install symfony-cli
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
