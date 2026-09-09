# Moodle dev image for testing theme_roshnilite.
#
# Clones a stable Moodle branch at build time. The MOODLE_BRANCH build-arg
# defaults to MOODLE_500_STABLE (the theme's declared minimum, see
# $plugin->requires in version.php) because that branch still serves from
# the webroot directly - Moodle 5.1 onward moved the served files into a
# public/ subdirectory, which this simple setup does not account for.
FROM php:8.3-apache

ARG MOODLE_BRANCH=MOODLE_500_STABLE

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        libxslt1-dev \
        libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        intl \
        zip \
        xsl \
        soap \
        opcache \
        mysqli \
        pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

RUN git clone --branch "${MOODLE_BRANCH}" --depth 1 https://github.com/moodle/moodle.git /var/www/html \
    && rm -rf /var/www/html/.git

# Moodle's environment check requires max_input_vars >= 5000 (its forms can have
# more fields than PHP's 1000 default allows); the rest are the usual Moodle-
# recommended minimums so large file uploads and long-running CLI/cron jobs work.
RUN { \
        echo 'max_input_vars = 5000'; \
        echo 'memory_limit = 256M'; \
        echo 'upload_max_filesize = 100M'; \
        echo 'post_max_size = 100M'; \
        echo 'max_execution_time = 300'; \
    } > /usr/local/etc/php/conf.d/moodle.ini

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Outside the webroot on purpose: it must survive independently of the
# moodle_html volume (see entrypoint.sh and demo-seed.php's own docblock).
COPY demo-seed.php /usr/local/bin/demo-seed.php

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
