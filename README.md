# Roshni Lite

*This block is written by DualCube \<admin@dualcube.com\>.*

This Moodle theme is a 100% responsive, feature-heavy Moodle theme. It is compatible with Moodle 5.0 to 5.3 (dev). It provides customizable sections on the front page which the user may customize through a backend setting panel, and can add/update/delete content.

## Installation

Go to **Site administration > Plugins > Install plugins** and upload or drag & drop the downloaded ZIP file.

To install manually, place all downloaded files in `/theme/roshnilite` and visit `/admin/index.php` in your browser.

## Overview

1. Truckloads of customization options!
2. Exclusive frontpage with high-resolution graphics.
3. Beautifully crafted dynamic Masonry blocks.
4. Two graphical and one contextual information sections.
5. Automatic display of courses on the front page.
6. Set your own custom theme color with the color picker.
7. Customizable social icons and custom font support.
8. Full-fledged slider!
9. Unique login page.
10. Provision to display default "Main Page content" for Moodle.

## Setting panel

In **Site administration > Appearance > Themes > Roshni Lite** you get 4 panels to fully customize the theme:

1. **General Settings** — whole theme color (Brand colour, Main Theme Color), Background image, Favicon, Logo, Heading style, About site text, social handles and their icons.
2. **Advanced settings** — Raw SCSS, which can change the whole theme visualization (powerful — use with care).
3. **Font Settings** — your site's default font.
4. **Faculty Settings** — add faculty display settings shown on the site home page.

## Uninstall

Admins can uninstall this theme from **Administration > Site Administration > Plugins > Plugins overview > Roshni Lite > Uninstall**.

## Local development (Docker)

Run this from the repo root:

```
docker compose up -d
```

This builds a Moodle 5.0 container (MariaDB + Apache/PHP), installs Moodle non-interactively on first run, sets Roshni Lite as the active theme, and live-mounts this repo into the container so edits show up without rebuilding.

| | |
|---|---|
| Site  | http://localhost:8080 |
| Admin | `admin` / `RoshniliteDev#123` *(change `MOODLE_ADMIN_PASS` in `docker-compose.yml` before exposing this beyond localhost)* |
| Database | `127.0.0.1:3307`, user/password `moodle` / `moodle`, database `moodle` (root password also `moodle`) — connect any DB client (DBeaver, TablePlus, MySQL Workbench, ...) to inspect data directly. Host port is 3307, not 3306, since 3306 is commonly already taken by a local MySQL/MariaDB — change the host-side number in `docker-compose.yml`'s `db.ports` if 3307 also collides for you. |

To test against a different supported branch (5.1, 5.2, 5.3dev), change the `MOODLE_BRANCH` build arg in `docker-compose.yml` and run `docker compose up -d --build`. Branches 5.1 and later serve from a `public/` subdirectory that this setup does not account for.

`docker compose down` stops it; add `-v` to also wipe the database and Moodle install for a clean re-run.
