#!/bin/bash
# Entrypoint for the theme_roshnilite dev container.
#
# On first run: waits for the database, installs Moodle non-interactively,
# and activates theme_roshnilite as the site theme. On every run: starts
# Apache. Safe to re-run - install only happens once (config.php is the
# marker, and it lives on the persisted moodle_html volume).
#
# Never chown the bind-mounted theme/roshnilite tree here: it is the host's
# working copy of this repo (bind mounts apply ownership changes straight to
# the host filesystem), and www-data only needs to read it, not own it -
# normal file permissions from a git checkout already allow that.

set -euo pipefail

MOODLE_DIR=/var/www/html
DATA_DIR=/var/www/moodledata

echo "Waiting for database at ${MOODLE_DB_HOST}..."
until php -r "new mysqli('${MOODLE_DB_HOST}', '${MOODLE_DB_USER}', '${MOODLE_DB_PASS}', '${MOODLE_DB_NAME}');" 2>/dev/null; do
    sleep 2
done
echo "Database is up."

mkdir -p "${DATA_DIR}"
chown -R www-data:www-data "${DATA_DIR}"

# Runs its arguments as www-data, preserving each argument exactly as given
# (no re-parsing by an intermediate shell) - important since several values
# below (e.g. the site full name) contain spaces.
run_as_www() {
    su -s /bin/bash www-data -c 'exec "$0" "$@"' -- "$@"
}

if [ ! -f "${MOODLE_DIR}/config.php" ]; then
    echo "No config.php found - running the Moodle installer..."
    run_as_www php "${MOODLE_DIR}/admin/cli/install.php" \
        --non-interactive \
        --agree-license \
        --lang=en \
        --wwwroot="${MOODLE_WWWROOT}" \
        --dataroot="${DATA_DIR}" \
        --dbtype=mariadb \
        --dbhost="${MOODLE_DB_HOST}" \
        --dbname="${MOODLE_DB_NAME}" \
        --dbuser="${MOODLE_DB_USER}" \
        --dbpass="${MOODLE_DB_PASS}" \
        --fullname="${MOODLE_SITE_NAME}" \
        --shortname="${MOODLE_SITE_SHORTNAME}" \
        --adminuser="${MOODLE_ADMIN_USER}" \
        --adminpass="${MOODLE_ADMIN_PASS}" \
        --adminemail="${MOODLE_ADMIN_EMAIL}"
    echo "Moodle installed."

    echo "Activating theme_roshnilite..."
    run_as_www php "${MOODLE_DIR}/admin/cli/cfg.php" --name=theme --set=roshnilite

    echo "Seeding demo content (slide, categories, courses, faculty)..."
    run_as_www php /usr/local/bin/demo-seed.php

    run_as_www php "${MOODLE_DIR}/admin/cli/purge_caches.php"
    echo "theme_roshnilite is now the active theme."
else
    echo "Existing Moodle install found, skipping installer."
fi

exec "$@"
