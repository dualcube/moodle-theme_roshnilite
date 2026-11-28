# Roshni Lite: developer guide

How the theme is put together, how to run it locally, and how to change or
extend it, the site home above all. For what each admin setting does, see the
[site administrator guide](../user/README.md).

Supports Moodle 4.5 LTS (Bootstrap 4.6) to 5.3 LTS (Bootstrap 5) from a single
build. Parent theme: Boost.

## Contents

1. [Architecture](#1-architecture)
2. [Code map](#2-code-map)
3. [Local environment](#3-local-environment)
4. [How the site home renders](#4-how-the-site-home-renders)
5. [Recipes](#5-recipes)
6. [Styling: tokens, dark mode, conventions](#6-styling-tokens-dark-mode-conventions)
7. [Templates: rules that keep CI green](#7-templates-rules-that-keep-ci-green)
8. [Testing and CI](#8-testing-and-ci)
9. [Releasing](#9-releasing)

---

## 1. Architecture

- **Boost does the structural work.** Every layout except `frontpage` resolves
  to Boost's own layout files (`config.php`), so navigation, drawers, the
  course index and the login form are exactly what each Moodle release ships.
  The theme adds a design layer on top; it doesn't fork Boost's layouts.
- **The site home is the theme's own page**: `layout/frontpage.php` renders
  `templates/frontpage.mustache`, which wraps the theme's sections in Boost's
  `head`, `navbar` and `footer` partials.
- **Data in PHP, markup in Mustache.** `classes/output/frontpage.php` exports a
  plain array for the template; no HTML is built in PHP. Each section is
  `null` when it has nothing real to show, and the template wraps each
  partial in `{{#section}}`, so empty sections disappear.
- **Honest by default.** Numbers, learner counts, prices, events, testimonials,
  contact details and social links only appear when they're real and
  meaningful. Thresholds are class constants (see [section 4](#4-how-the-site-home-renders)).
- **Styling** is one hand-written stylesheet, `style/styles.css`, built on CSS
  custom properties (`--rl-*`), loaded after Boost's compiled SCSS. The SCSS
  pipeline (`lib.php`, `scss/roshnilite/`) only feeds Bootstrap variables and
  admin-supplied SCSS.

## 2. Code map

| Path | Purpose |
|---|---|
| `config.php` | Theme config: parent `boost`, layouts, SCSS callbacks, `csspostprocess` |
| `version.php` | Version, `requires` (4.5), `supported` `[405, 503]` |
| `lib.php` | Pre-SCSS (brand colour → `$primary`, font size), extra SCSS (background image, raw SCSS), CSS post-processing that fills `[[setting:...]]` placeholders, font `@font-face` builder, `pluginfile` serving for setting files |
| `layout/frontpage.php` | The site home layout: primary navigation data plus `frontpage` export |
| `classes/output/frontpage.php` | **All site home data.** One `export_*()` method per section |
| `classes/output/course_cover.php` | Course covers: uploaded image, or a drawn pattern in the subject's tone with a monogram |
| `classes/output/dashboard.php` | The Dashboard header data (greeting, stats, course to continue, deadlines, shortcuts) |
| `classes/output/core_renderer.php` | Logo and favicon from settings, Dashboard header injection in `full_header()`, the inner-page footer (`roshnilite_footer()`), and the colour mode menu for logged-out visitors |
| `classes/privacy/provider.php` | Null privacy provider |
| `settings.php`, `settings/*.php` | Admin settings, one file per tab |
| `templates/frontpage.mustache` | Site home page shell and section order |
| `templates/frontpage/*.mustache` | One partial per section (`hero`, `catalogue`, `about`, `steps`, `events`, `faculty`, `testimonials`, `faq`, `cta`, `contact` = footer) and their sub-parts |
| `templates/dashboard.mustache` | Dashboard header |
| `templates/site_footer.mustache` | Slim footer on inner pages |
| `templates/colour_mode_menu_visitor.mustache` | Light/dark menu for logged-out visitors (5.3+) |
| `templates/theme_boost/footer.mustache` | Override of Boost's footer, to add the theme footer above it |
| `templates/core/login_panel.mustache` | Branded panel on the 5.3 split-screen login |
| `style/styles.css` | The design layer (tokens, components, dark mode) |
| `style/moodle.css` | Precompiled CSS fallback (`precompiledcsscallback`) |
| `scss/roshnilite/` | `pre.scss` (Bootstrap variables), `post.scss` (block tweaks), `body-background.scss` |
| `fonts/` | Bundled Figtree (SIL OFL), referenced as `[[font:theme\|...]]` |
| `pix/` | Bundled images, including `img/logo.png` |
| `lang/en/theme_roshnilite.php` | Strings, **kept in alphabetical order** |
| `tests/` | PHPUnit (`lib_test.php`, `output/*_test.php`) and Behat (`behat/frontpage.feature`) |
| `docker/`, `docker-compose.yml` | Local Moodle environment |
| `.github/workflows/moodle-plugin-ci.yml` | CI matrix: 4.5 to 5.3, MariaDB and PostgreSQL |

## 3. Local environment

Requirements: Docker Desktop, Node.js (for scripts), and optionally PHP +
Composer for the coding-standards checker.

```bash
npm run env:up        # start (first run builds and installs Moodle)
npm run env:down      # stop
npm run env:reset     # wipe database and install, then start fresh
npm run env:logs      # follow the Moodle container's log
```

| | |
|---|---|
| Site | http://localhost:8080, `admin` / `RoshniliteDev#123` (set by `MOODLE_ADMIN_PASS` at install time) |
| Adminer | http://localhost:8081: server `db`, user/password `moodle` / `moodle` |
| Database port | `127.0.0.1:3307` |

The repo is live-mounted at `/opt/theme_roshnilite` and symlinked into the
Moodle install, so edits show up without rebuilding. On first install
`docker/demo-seed.php` adds categories, courses, a slide and faculty so every
section renders.

**Purge caches after changing CSS, templates or language strings:**

```bash
docker compose exec moodle php /var/www/html/admin/cli/purge_caches.php
```

**Another Moodle branch** (wipes the current install):

```bash
docker compose down -v
MOODLE_BRANCH=MOODLE_405_STABLE docker compose up -d --build
```

Any of `MOODLE_405_STABLE`, `MOODLE_500_STABLE` to `MOODLE_503_STABLE`. On 5.1
and later the web root is `/var/www/html/public`.

**Dark mode locally (5.3+):** turn on `theme_boost | enablecolourmodes`:

```bash
docker compose exec moodle php /var/www/html/admin/cli/cfg.php \
  --component=theme_boost --name=enablecolourmodes --set=1
```

**Useful checks:**

```bash
npm run lint           # php -l on every file + phpcs (Moodle standard)
npm run fix:phpcs      # auto-fix what phpcbf can
npm run check:html     # renders live pages and flags empty src/href, unquoted attributes
```

`npm run check:html` needs Chrome and `puppeteer-core` (`npm install`). It's a
quick local stand-in for CI's Mustache validator, not a replacement.

The theme ships no AMD modules (there's no `amd/` folder). Its only JavaScript
is the inline script in `colour_mode_menu_visitor.mustache`. If you add a
module, put it in `amd/src/` and build it with Moodle's Grunt from the root of
a Moodle checkout.

## 4. How the site home renders

```
layout/frontpage.php
  └─ new frontpage($PAGE->theme)->export_for_template($OUTPUT)
       └─ templates/frontpage.mustache            (context: { frontpage: {...}, output, ... })
            ├─ frontpage/hero        ← hero, slider, search, announcement, preview, teachers, stats
            ├─ frontpage/catalogue   ← catalogue
            ├─ frontpage/about       ← about
            ├─ frontpage/steps       ← steps
            ├─ frontpage/events      ← events
            ├─ frontpage/faculty     ← faculty
            ├─ frontpage/testimonials← testimonials
            ├─ frontpage/faq         ← faq
            ├─ frontpage/cta         ← cta
            ├─ Moodle's main_content (hidden unless showmaincontent)
            └─ frontpage/contact     ← footer, contact, social (+ search, stats, catalogue.subjects)
```

Partials are included inside `{{#frontpage}}`, so they can read any key of
the export, not only their own. The footer uses `search`, `stats` and
`catalogue.subjects` this way.

**Export keys and their rules** (`classes/output/frontpage.php`):

| Key | Method | `null` / hidden when | Notes |
|---|---|---|---|
| `hero` | `export_hero()` | never | Site full name and summary |
| `slider` | `export_slider()` | `slidercount` < 1, or slide 1 empty | Replaces the default hero text |
| `search` | inline | never | Action `/course/search.php` |
| `announcement` | `pick_announcement()` | no course newer than `NEW_DAYS` (30) | Newest "new" course |
| `preview` | `pick_preview()` | no catalogue | First course of each subject, up to `PREVIEW_COURSES` (4), plus next event |
| `teachers` | `export_teachers()` | fewer than 2 distinct course contacts | Up to `TEACHER_AVATARS` (4) |
| `stats` | `export_stats()` | fewer than 2 counts over `STAT_MINIMUMS` | courses 6, categories 3, learners 50 |
| `catalogue` | `export_catalogue()` | no visible courses | Up to `MAX_SUBJECTS` (6) × `MAX_COURSES` (8); query capped at `CATALOGUE_LIMIT` (60) |
| `about` | `export_about()` | never | `ABOUT_ITEMS` (4), defaults per field |
| `steps` | `export_steps()` | `showhowitworks` = 0 | `STEPS` (3) |
| `events` | `export_events()` | `showevents` = 0 or no future site events | `EVENTS` (3) |
| `faculty` | `export_faculty()` | `facultycount` < 1 or person 1 empty | Social links must be `http(s)://` |
| `testimonials` | `export_testimonials()` | no quote set | `TESTIMONIALS` (3) |
| `faq` | `export_faq()` | `showfaq` = 0 or no questions | Unset field → default; cleared field → skipped |
| `cta` | `export_cta()` | never | `signupurl` only if self-registration is on and the user isn't logged in |
| `contact` | `export_contact()` | no address/email/phone | |
| `social` | `export_social()` | (links empty) | `SOCIAL_LINKS` (4), `http(s)://` only |
| `footer` | `export_footer()` | never | Also used by the inner-page footer |
| `showmaincontent` | inline | | `moodlemaincontentinfrontpage`, and always while editing |

Course cards (`export_course_card()`): learners shown from
`MIN_LEARNERS_SHOWN` (10); price from enabled `fee` enrolment with a cost, or
"Free" from an open `self` enrolment without a key (`course_access()`).

**Helpers:** `setting()` (raw string), `link_setting()` (URL or `null`; rejects
`#`, `javascript:` and relative paths), `format_html()` (admin rich text),
`file_url()` (setting file URL or `null`), `plain_text()` (HTML → one trimmed line),
`section_enabled()` (checkbox, defaulting to on when unset).

## 5. Recipes

### Add a site home section

1. **Settings**: add fields to `settings/sections.php` (or the right tab),
   following the existing pattern, with `$setting->set_updatedcallback('theme_reset_all_caches')`.
   Give text fields defaults from language strings named `<setting>default`.
2. **Strings**: add them to `lang/en/theme_roshnilite.php` **in alphabetical order**.
3. **Data**: add `protected function export_mysection(): ?array` to
   `classes/output/frontpage.php`. Return `null` when there's nothing real to
   show. Format admin text with `format_string()` / `format_html()`, never
   echo raw settings. Add the key to `export_for_template()`.
4. **Template**: create `templates/frontpage/mysection.mustache` with the GPL
   header, a `@template` docblock and an **Example context** that renders every
   branch (see [section 7](#7-templates-rules-that-keep-ci-green)).
5. **Placement**: include it in `templates/frontpage.mustache` inside
   `{{#mysection}} ... {{/mysection}}`.
6. **Styles**: add a block to `style/styles.css` using `--rl-*` tokens only
   (no hard-coded colours), so dark mode works with no extra rules.
7. **Tests**: extend `tests/output/frontpage_test.php` (hidden when empty,
   shown with data), and the Behat feature if visitors interact with it.
8. **Version**: bump `version.php` if you added settings or strings.

### Add a setting to an existing section

Add the field in `settings/*.php`, the strings, then read it in the section's
`export_*()` method with `$this->setting('name')` (or `link_setting` /
`file_url`). File settings also need their file area added to the regular
expression in `theme_roshnilite_pluginfile()` (`lib.php`).

### Change section order

Reorder the `{{#...}}` blocks in `templates/frontpage.mustache`. Update the
order table in the user guide and README.

### Change fixed wording

Edit the string in `lang/en/theme_roshnilite.php`. Admins can override it per
site with Language customisation, so prefer strings over hard-coded text.

## 6. Styling: tokens, dark mode, conventions

**Tokens** (top of `style/styles.css`, `:root`):

| Group | Tokens |
|---|---|
| Brand | `--rl-primary` (main colour setting), `--rl-accent` (brand colour), `-strong`, `-tint`, `-tint-2` variants |
| Text | `--rl-ink`, `--rl-ink-2`, `--rl-muted`, plus `--rl-accent-text` / `--rl-primary-text` for text in brand colour |
| Surfaces and lines | `--rl-surface`, `--rl-canvas`, `--rl-canvas-2`, `--rl-line`, `--rl-line-2` |
| Mode-aware roles | `--rl-mix-base` / `--rl-mix-ink` (what tints mix with), `--rl-inverse` / `--rl-on-inverse` (selected chips), `--rl-glass` (navbar), `--rl-foot-bg` |
| Shape and motion | `--rl-radius-*`, `--rl-shadow-*`, `--rl-glow`, `--rl-ease`, `--rl-speed` |
| Layout | `--rl-container` (1200px), `--rl-gutter`, `--rl-navbar-height` |

**Theme settings in the CSS.** `style/styles.css` is plain, valid CSS with
default values, so it passes Moodle's stylelint as it is. Two comment markers
are replaced by `theme_roshnilite_process_css()`:

- `/* [[setting:settingsrules]] */` (after the token block) → the output of
  `theme_roshnilite_get_settings_css()`: a `:root` rule with `--rl-primary` /
  `--rl-accent` from the colour settings (hex values only, otherwise the
  defaults stay), plus the font rules.
- `/* [[setting:customcss]] */` (last line) → the Custom CSS setting.

Don't put bare `[[setting:...]]` placeholders in the CSS: they aren't valid
CSS and fail the lint. Fonts use `url([[font:theme|file]])` and images
`url([[pix:theme|name]])` (for example the contour patterns in
`pix/contours.svg` and `pix/contours-dark.svg`). Moodle forbids `data:` URLs.
The base font size comes from the SCSS pipeline (`$font-size-base` in
`theme_roshnilite_get_pre_scss()`).

`style/moodle.css` is compiled Boost CSS, used only as the precompiled
fallback. It's listed in `.stylelintignore`, the same way core excludes Boost's own copy.

**Dark mode.** Moodle 5.3+ sets `data-bs-theme="dark"` on `<html>`
(`theme_boost\colour_mode`). The theme's single `[data-bs-theme="dark"] { ... }`
block re-points the tokens, so components need no dark rules of their own.
Rules:

- Use tokens, not literal colours. For brand-coloured **text** use
  `--rl-accent-text`; for brand **fills** use `--rl-accent` / `-strong`.
- Mix tints with `var(--rl-mix-base)`, not `#fff`.
- Text on a solid "selected" fill: `--rl-inverse` / `--rl-on-inverse`.
- A component that stays dark in both modes (the site home footer `.rl-foot`)
  re-declares the text and line tokens locally. Copy that pattern.
- The few fixed pastels have explicit dark overrides next to the token block.
- On 4.5 to 5.2 the attribute is never set, so the theme stays light.

**Colour mode menu.** Core renders it only for logged-in users
(`colour_mode::can_choose_mode()`), and its JavaScript saves a user preference.
`core_renderer::colour_mode_menu()` returns core's menu when there is one;
otherwise (logged out, modes enabled, 5.3+) it renders
`colour_mode_menu_visitor.mustache`. That menu's inline script only sets
`data-bs-theme` / `data-colourmode` and the `theme_boost_colourmode` cookie,
which core reads for logged-out pages. If core changes its menu markup or
cookie, update this template to match.

**Conventions:**

- Prefix every theme class with `rl-` so it can't collide with Moodle or Bootstrap.
- Templates carry both Bootstrap 4 and 5 forms where they differ
  (`data-toggle` + `data-bs-toggle`, `sr-only` + `visually-hidden`), so one
  build works on 4.5 and 5.x.
- Respect `prefers-reduced-motion` for anything that moves.
- Moodle's stylelint wants multi-line rules, one selector per line, and each
  item of a multi-line value list (`transition`, `unicode-range`) on its own line.
- `!important` is only for overriding Bootstrap or Moodle utilities that are
  `!important` themselves, and each one carries
  `/* stylelint-disable-line declaration-no-important */`.
- To lint the CSS the way CI does, run stylelint 15 with Moodle's `.stylelintrc`
  (in the root of a Moodle checkout) on `style/styles.css` and `scss/`.

## 7. Templates: rules that keep CI green

`moodle-plugin-ci mustache` renders each template **with its Example context**
and validates the HTML. A warning fails the build.

- Every template needs the GPL header, `@template` and an `Example context (json)`
  that renders **every** element, including ones only shown with data.
- No empty `src`, `href` or `id` in the rendered example (for example, give
  each `image` in the example a URL).
- Quote all attribute values.
- Use `{{uniqid}}` in ids that a page might render twice (the hero and footer
  search forms use different prefixes for this reason).
- `INFO` lines (missing `lang`, "section lacks heading", trailing slash on
  Boost's `<link>`) don't fail the build.

## 8. Testing and CI

**CI** (`.github/workflows/moodle-plugin-ci.yml`) runs on every push across
Moodle 4.5, 5.0, 5.1, 5.2 and 5.3, on MariaDB and PostgreSQL:

`phplint` → `phpcs` (0 warnings) → `phpdoc` (0 warnings) → `phpmd` →
`mustache` → `grunt` (0 lint warnings) → `validate` → `savepoints` →
`phpunit` → `behat` (Chrome).

**Tests in the repo:**

| File | Covers |
|---|---|
| `tests/lib_test.php` | SCSS callbacks, CSS post-processing, font CSS |
| `tests/output/frontpage_test.php` | Site home export: sections shown or hidden, thresholds, defaults |
| `tests/output/dashboard_test.php` | Dashboard header export |
| `tests/behat/frontpage.feature` | Browsing courses by subject and search, How it works and FAQ, navigation on site home and inner pages |

PHPUnit and Behat aren't set up in the Docker image. Run them in CI, or
initialise them in a Moodle checkout with `moodle-plugin-ci` or Moodle's own
`admin/tool/phpunit/cli/init.php` and `admin/tool/behat/cli/init.php`.

**Before pushing**, at minimum: `npm run lint`, purge caches and click through
the site home in light and dark mode, and check that new templates' Example
contexts cover every branch.

## 9. Releasing

1. Bump `$plugin->version` (YYYYMMDDXX) and `$plugin->release` in `version.php`.
   Bump it for any change to settings, strings, templates or CSS that sites must pick up.
2. Keep `$plugin->supported` in step with the CI matrix.
3. Update the README's Compatibility table and the guides in `docs/` when
   behaviour changes.
4. Make sure CI is green on every branch in the matrix.
