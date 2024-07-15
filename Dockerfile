FROM chialab/php-dev:8.3-apache

# install ffmpeg
RUN apt update && apt install -y ffmpeg

# install pcntl
RUN install-php-extensions pcntl
