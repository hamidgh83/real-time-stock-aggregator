#!/usr/bin/env bash

${INSTALL_DIR:=/var/www/html}

# Check composer
if [[ ! -d ${INSTALL_DIR}/vendor ]]; then
    printf "\033[0;32m > Installing Composer dependencies... \n"

    # No vendor folder, run composer install
    composer install --no-progress --no-suggest -d ${INSTALL_DIR}
else
    printf "\033[0;32m > Updating Composer packages... \n"

    # Run composer update
    composer update --no-progress --no-suggest -d ${INSTALL_DIR}
fi

# Check the database connection and wait for it to be ready
MAX_RETRIES=5
RETRY_COUNT=0

until php ${INSTALL_DIR}/artisan migrate --force; do
    RETRY_COUNT=$((RETRY_COUNT + 1))

    if [[ $RETRY_COUNT -ge $MAX_RETRIES ]]; then
        printf "\033[0;31m > Could not connect to the database after $MAX_RETRIES attempts, exiting...\n"
        exit 1
    fi

    printf "\033[0;33m > Waiting for the database container to be ready... (Attempt: $RETRY_COUNT)\n"
    sleep 5
done

exec php-fpm
