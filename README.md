# Roshni Lite

*This block is written by DualCube \<admin@dualcube.com\>.*

This Moodle theme is a 100% responsive, feature-heavy Moodle theme. It is compatible with Moodle 4.5 LTS through Moodle 5.3 LTS. It provides customizable sections on the front page which the user may customize through a backend setting panel, and can add/update/delete content.

## Compatibility

| Moodle | Bootstrap | Status |
|---|---|---|
| 4.5 LTS | 4.6 | Supported |
| 5.0, 5.1, 5.2 | 5 | Supported |
| 5.3 LTS | 5 | Supported |

One build works on every supported release:

- Inner pages use Boost's own drawer layout (`drawers.php`, inherited from the parent theme), so the primary navigation, secondary navigation, course index and block drawer are exactly what each Moodle release ships.
- The site home and login page use Boost's `head`, `navbar` and `footer` templates around the theme's own sections.
- The theme's own templates carry both the Bootstrap 4 and Bootstrap 5 forms of carousel data attributes and helper classes (`data-ride` with `data-bs-ride`, `sr-only` with `visually-hidden`).

## Code layout

| Path | What it does |
|---|---|
| `config.php` | Layouts. Everything except `frontpage` resolves to Boost's layout files, so the login page follows each Moodle release's own design. |
| `classes/output/core_renderer.php` | Feeds the theme's logo and favicon settings into Boost's navbar and page head. |
| `classes/output/frontpage.php` | Builds the site home data (hero, features, courses, categories, faculty, contact). No HTML is built in PHP. |
| `templates/frontpage.mustache`, `templates/frontpage/*.mustache` | Site home page and one partial per section. |
| `classes/output/dashboard.php`, `templates/dashboard.mustache` | The personal header above the Dashboard blocks. |
| `templates/core/login_panel.mustache` | The branded left panel of the Moodle 5.3+ split-screen login. |
| `lib.php` | SCSS callbacks, CSS post-processing for colour/font settings, and file serving for theme settings. |
| `style/styles.css` | The Roshni Lite design layer: tokens, navbar, site home, dashboard and blocks, login. `[[setting:...]]` placeholders are filled from the theme settings. |
| `tests/` | PHPUnit tests for the output class and `lib.php`, and a Behat feature for the site home and navigation. |

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

## Design

Roshni means *light*, and the design is built around it: a warm paper canvas,
a sunrise accent next to your brand colour, and a soft serif display face
(Fraunces, bundled in `fonts/` under the SIL Open Font License, so no font is
loaded from a third-party server). Set a heading font name in the theme's font
settings to replace it.

- **Site home**: a course search; the site's own courses fanned out as the
  hero visual (an image uploaded for the slide takes its place); subject tiles;
  a grid of features; faculty; a call to action; and Moodle's own course list
  styled as a numbered index. When that list is shown, the theme's course grid
  steps aside so courses are not listed twice. Put the highlighted phrase of a
  slide headline in `<em>` to give it the accent colour and brush underline.
- **Honest by default**: headline numbers appear only once they are big enough
  to be worth showing, and subjects without courses are left out.
- **Course covers**: a course without an uploaded image gets a typographic
  cover - its monogram on a gradient in its subject's colour.
- **Dashboard**: a greeting under a sky that follows the learner's time of day,
  their numbers, the course to jump back into and shortcuts, above the
  standard blocks (which keep the full course list and the deadlines).

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

This builds a Moodle 5.3 LTS container (MariaDB + Apache/PHP), installs Moodle non-interactively on first run, sets Roshni Lite as the active theme, seeds demo content, and live-mounts this repo into the container so edits show up without rebuilding. On 5.3 it also turns on the site home and turns off forced login, which new 5.3 sites disable by default.

| | |
|---|---|
| Site  | http://localhost:8080 |
| Admin | `admin` / `RoshniliteDev#123` *(change `MOODLE_ADMIN_PASS` in `docker-compose.yml` before exposing this beyond localhost)* |
| Database (web UI) | http://localhost:8081 (Adminer) — System: `MySQL`, Server: `db`, Username: `moodle`, Password: `moodle`, Database: `moodle`. Browse/edit tables straight from the browser, no client install needed. |
| Database (raw port) | `127.0.0.1:3307`, user/password `moodle` / `moodle`, database `moodle` (root password also `moodle`) — for connecting an external DB client (DBeaver, TablePlus, MySQL Workbench, ...) instead of Adminer. Host port is 3307, not 3306, since 3306 is commonly already taken by a local MySQL/MariaDB — change the host-side number in `docker-compose.yml`'s `db.ports` if 3307 also collides for you. |

To test another supported branch, wipe the current install and pass the branch:

```
docker compose down -v
MOODLE_BRANCH=MOODLE_405_STABLE docker compose up -d --build
```

Any of `MOODLE_405_STABLE`, `MOODLE_500_STABLE` … `MOODLE_503_STABLE` works, including 5.1+ which serve from `public/`.

`docker compose down` stops it; add `-v` to also wipe the database and Moodle install for a clean re-run.
