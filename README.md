# Roshni Lite

*This theme is written by DualCube \<admin@dualcube.com\>.*

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

A clean product look: white surfaces, one geometric sans-serif (Figtree,
bundled in `fonts/` under the SIL Open Font License, so no font is loaded from
a third-party server), one accent from your brand colour, thin borders and soft
shadows. The site's own content takes the place of decoration. Set a heading
font name in the theme's font settings to replace Figtree in headings.

- **Site home**, top to bottom: the hero, with an announcement of the newest
  course, the slide headline, a course search and the people who teach here,
  beside a preview of the site's courses as a list with the next site event
  (an image uploaded for the slide takes their place); the course catalogue,
  one tab per subject, with course cards that show the teacher, learner count,
  price or "Free" and a "New" badge, and a summary on hover; what the site
  offers; how it works; upcoming site events; faculty; what learners say; the
  site's numbers; questions and answers; a call to action; and a footer with
  quick links and the contact details that are set. Moodle's own full course
  list and course search give way to the catalogue and hero search, so nothing
  is listed twice. In a slide headline, words in `<em>` or `<strong>` are set
  bold against the light headline, and a heading before the `<h1>` becomes
  its kicker. Sections are set under *Site home sections* in the theme
  settings.
- **Honest by default**: headline numbers and learner counts appear only once
  they are big enough to be worth showing, subjects without courses are left
  out, prices come from the courses' enrolment methods, events from the site
  calendar, testimonials only once an admin enters real ones, and contact
  details and social links only once they are set.
- **Course covers**: a course without an uploaded image gets a drawn cover - a
  pattern in its subject's colour, varied by course, with its monogram.
- **Course listings**: All courses, each category and course search show
  Moodle's own lists as the same cards.
- **Dashboard**: a greeting for the time of day, the learner's numbers, the
  course to jump back into and shortcuts, above the standard blocks (which
  keep the full course list and the deadlines).
- **Login**: on Moodle 5.3, a welcome on a tinted panel beside the form.

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
