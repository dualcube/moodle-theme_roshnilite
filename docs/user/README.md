# Roshni Lite: guide for site administrators

This guide shows how to set up and edit a Moodle site that uses the Roshni Lite
theme, with most of it on the **site home** (the page visitors see first). No
coding is needed: everything here is done from Moodle's admin pages.

Works with Moodle 4.5 LTS to 5.3 LTS. Dark mode needs Moodle 5.3 or later.

## Contents

1. [Install and turn on the theme](#1-install-and-turn-on-the-theme)
2. [Where the settings are](#2-where-the-settings-are)
3. [How the site home is built](#3-how-the-site-home-is-built)
4. [Editing the site home, section by section](#4-editing-the-site-home-section-by-section)
5. [Brand: colours, logo, favicon, fonts](#5-brand-colours-logo-favicon-fonts)
6. [Dark mode](#6-dark-mode)
7. [Dashboard, login page and course pages](#7-dashboard-login-page-and-course-pages)
8. [Changing fixed wording](#8-changing-fixed-wording)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Install and turn on the theme

1. Go to **Site administration > Plugins > Install plugins** and upload the theme's ZIP file.
   (Or copy the theme folder to `theme/roshnilite` on the server, then open
   **Site administration** to finish the install.)
2. Go to **Site administration > Appearance > Themes** and choose **Roshni Lite**.
3. Open the site home. It already shows your courses, categories and site name.
   Every section has sensible default text, so nothing looks empty while you set it up.

## 2. Where the settings are

**Site administration > Appearance > Themes > Roshni Lite** has five tabs:

| Tab | What you set there |
|---|---|
| **General Settings** | Colours, preset, logo, favicon, background image, hero slides, the "about the site" items, the course catalogue heading, contact details, social links |
| **Advanced settings** | Raw SCSS for designers and developers (use with care) |
| **Font Settings** | Font size, heading and body fonts, custom font files, Custom CSS |
| **Faculty Settings** | The teachers shown in the Faculty section |
| **Site home sections** | How it works, Upcoming events, What learners say, Questions and answers |

Some of the site home's content comes from Moodle itself, not from the theme:

| Moodle setting | Used for |
|---|---|
| **Site administration > General > Site home > Site home settings** > *Full site name* | The site name in the hero and footer |
| Same page > *Site home summary* | The hero text when there are no slides, and the footer summary |
| Your **course categories** and **courses** | The course catalogue, hero course preview, footer Subjects list |
| **Site calendar** events | The Upcoming events section and the hero's "Next event" card |
| **Enrolment methods** in each course | The price or "Free" label on course cards |

> Changes to theme settings show up on the next page load; Moodle clears the theme
> cache for you. If a change doesn't appear, see [Troubleshooting](#9-troubleshooting).

## 3. How the site home is built

Sections appear in this order, top to bottom. Sections built from your site's
own data hide themselves when there's nothing real to show, so a new site
never shows empty boxes or made-up numbers.

| # | Section | Edited in | Shown when |
|---|---|---|---|
| 1 | **Hero** (headline, search, course preview) | General Settings > slides, or Site home summary | Always |
| 2 | **Course catalogue** ("Find your next course") | Your categories and courses; heading in General Settings | At least one visible course |
| 3 | **About the site** (four feature cards) | General Settings > About site | Always (defaults fill any field left empty) |
| 4 | **How it works** (three steps) | Site home sections | "Show How it works" is ticked |
| 5 | **Upcoming events** | Site calendar | Ticked, and future site events exist |
| 6 | **Faculty** | Faculty Settings | Faculty count is 1 or more and the first person is filled in |
| 7 | **What learners say** | Site home sections | At least one quote is filled in |
| 8 | **Questions and answers** | Site home sections | Ticked, and at least one question is filled in |
| 9 | **Call to action** ("Start learning today") | Fixed wording ([how to change it](#8-changing-fixed-wording)) | Always |
| 10 | **Moodle's own site home content** | Moodle's Site home settings | "Enable default moodle homepage content" is ticked |
| 11 | **Footer** | Several places, see [Footer](#411-footer) | Always |

## 4. Editing the site home, section by section

### 4.1 Hero

The hero is the first thing visitors see. It works in one of two ways.

**A. Without slides (the default).** The headline is your *Full site name*
and the text under it is your *Site home summary*. Next to it is a live preview
of your courses, plus the next site event if there is one. Around the headline,
the theme adds these on its own:

- **"New" announcement**: a pill linking to your newest course, shown when a
  course was created in the last 30 days.
- **Course search box**: searches your course catalogue.
- **Teachers**: avatars of the people teaching your courses, with "Learn from N
  teachers". Shown when the courses have at least two different teachers.
- **Numbers**: course, category and learner counts. Each count only appears
  once it reaches a meaningful level (6 courses, 3 categories, 50 learners),
  and the row only appears when at least two counts qualify.

**B. With slides.** In **General Settings**:

1. Set **Slidercount** to the number of slides (1 to 6) and **Save changes**.
   The fields for each slide appear after saving.
2. For each slide fill in:
   - **Upload Your Image For Slide N**: optional. With an image, the image
     takes the place of the course preview.
   - **Text for slide N**: the headline. Use a *Heading 1* for the main line.
     A smaller heading placed *before* it becomes the small coloured line above
     the headline (for example "Welcome to your learning space"). Words in
     *italic* or **bold** are set in a heavier weight, to highlight them.
   - **Enter your text for button on slide N**: leave it empty for no button.
   - **Link for slide N**: where the button goes. Enter a page path such as
     `my` (the Dashboard) or `course/index.php` (all courses).

**To go back to no slides:** the slide count can't be set back to 0. Instead,
clear slide 1's image, text, button text and link. The hero then switches back
to option A.

### 4.2 Course catalogue

Built from your courses. Nothing to fill in, but here's how it decides what to show:

- **One tab per subject.** A subject is a course category that holds visible
  courses. Up to 6 subjects, in your category order, with up to 8 courses each.
  To reorder them, reorder your categories (**Site administration > Courses >
  Manage courses and categories**).
- **Course cards** show:
  - **Cover**: the course image if the teacher uploaded one. Otherwise the theme
    draws a cover in the subject's colour with the course's initials.
  - **Teacher**: the first *course contact* (normally the Teacher role).
  - **Learner count**: shown from 10 learners.
  - **Price**: from a *Enrolment on payment* method with a cost. **"Free"** is
    shown when *Self enrolment* is enabled, allows new enrolments and has no
    enrolment key.
  - **"New" badge**: for courses created in the last 30 days.
- **Heading and subheading**: **General Settings > "Enter heading" / "Enter sub
  heading"** in the *Masonry Block* group. Defaults: "Find your next course" /
  "Pick a subject to see what you can learn."

### 4.3 About the site

Four feature cards under a heading. In **General Settings**:

- **Enter heading** / **Enter sub heading** (About Site): the section title.
- For each of the four blocks:
  - **Upload image**: optional. Without one, a matching icon is used.
  - **Enter block name**: the card title.
  - **Enter block sub text**: the card text.
  - **Enter url**: where the card links. Left empty, it uses the default below.

| Block | Default title | Default link |
|---|---|---|
| 1 | Learner stories | The visitor's forum posts |
| 2 | Courses | All courses |
| 3 | Latest news | Blog |
| 4 | Community | Messages |

### 4.4 How it works

**Site home sections > How it works.** Tick **Show "How it works"** to show it,
then edit the title and text of each of the three steps. Any field left empty
uses its default ("Find a course", "Learn at your own pace", "Track your progress").

### 4.5 Upcoming events

**Site home sections > Upcoming events > Show upcoming events.** Shows the next
three **site events** from the calendar, with a link to the full calendar.

To add one: open the **Calendar**, choose **New event**, and set **Type of event**
to **Site** (only site administrators can). Course and user events aren't shown.
The section hides itself when no future site events exist.

### 4.6 Faculty

**Faculty Settings.**

1. Set **Faculty Count** (1 to 8) and **Save changes**. The fields for each person appear.
2. For each person: photo, name, description, and optionally Facebook,
   LinkedIn, Google and Twitter/X links. Links must be full web addresses
   (`https://...`); anything else is ignored, so you never get a dead link.
   Without a photo, the person's initials are shown.

The section only appears once the first person has a photo, name or description.

### 4.7 What learners say

**Site home sections > What learners say.** Up to three testimonials, each with
a **quote**, **name** and **role or course**. The section appears as soon as one
quote is filled in.

Please use real words from real learners, with their permission.

### 4.8 Questions and answers

**Site home sections > Questions and answers.** Tick **Show questions and
answers**, then edit up to four questions and answers. Four common questions
are filled in for you. **To remove one, clear its question.** Questions left
empty aren't shown.

### 4.9 Call to action

The "Start learning today" band with a **Browse courses** button. A **sign-up**
button is added for logged-out visitors when self-registration is turned on
(**Site administration > Plugins > Authentication > Manage authentication >
Self registration**). To change the wording, see [Changing fixed wording](#8-changing-fixed-wording).

### 4.10 Moodle's own site home content

**General Settings > Enable default moodle homepage content** (on by default)
also shows what Moodle itself puts on the site home: news, the combo list, and
anything else picked in **Site administration > General > Site home > Site home
settings > Site home items**.

Moodle's full course list and course search are left out when the catalogue is
showing, because the catalogue and the hero search already cover them. In
**editing mode** this area is always shown, so you can edit it.

### 4.11 Footer

A black band at the bottom of the site home, the same in light and dark mode.

| Part | Comes from |
|---|---|
| Site name and its first letter as a badge | *Full site name* |
| Short description | *Site home summary* (first 160 characters); a default tagline if empty |
| Numbers | Same counts and thresholds as the hero |
| **Find a course** search | Automatic |
| Social links | **General Settings > "Link of social icon" 1–4** |
| **Explore** links | Automatic: Courses, Calendar, and Log in, Dashboard or Create account |
| **Subjects** links | Your catalogue's subjects, plus **All subjects** |
| **Contact** column | **General Settings > Enter Address / Email Address / Phone Number** |
| Copyright line | "© *current year* *Full site name*" |

**Contact details.** Fill in any of address, email and phone. Only the filled
ones appear, and the column appears only when one is filled. Each has an
optional icon field. Leave it empty for the default icon, or paste a Font
Awesome tag such as `<i class="fa fa-envelope"></i>`.

**Social links.** Up to four. **"Link of social icon"** must be a full
`https://` address. Links that aren't (including placeholders such as `#`)
are left out. The icon field is optional, with Facebook, Twitter, LinkedIn and
YouTube icons as defaults for 1 to 4.

**Create account** appears in Explore only for logged-out visitors, and only
when self-registration is on.

Inner pages (courses, Dashboard and so on) have a slimmer footer with the
quick links and social links.

## 5. Brand: colours, logo, favicon, fonts

All in **General Settings** unless noted.

| Setting | Effect |
|---|---|
| **Choose Main Theme Color** | The theme's main colour. Default `#e74c3c` |
| **Brand colour** | The accent: buttons, links, highlights. Left empty, it follows the main colour |
| **Logo** | Shown in the navbar. About 65 px high; a transparent PNG or SVG works best in both light and dark mode |
| **Favicon** | The browser tab icon |
| **Background image** | An image behind inner pages; content then sits on white panels |
| **Theme preset** | Boost's *Default* or *Plain* look, or a preset file you uploaded under *Additional theme preset files* |

**Fonts** (Font Settings):

- **Font size reference**: the base text size in pixels (default 15). Keep it between 13 and 24.
- **Select Font > Font Type Standard**: give a **heading** and/or **body** font
  name that's already available to browsers (for example a system font). By
  default the theme uses its bundled font, *Figtree*.
- **Select Font > Font Type Custom**: upload your own font files (WOFF2 is
  enough for modern browsers; TTF, OTF, WOFF, EOT and SVG are also accepted)
  for headings and body, then give each font a name.
- **Custom CSS**: CSS added to every page.

## 6. Dark mode

Needs **Moodle 5.3 or later**. It uses Moodle's own light/dark mode, which is
still marked *experimental* by Moodle.

**Turn it on:**

1. Go to **Site administration > Appearance > Themes > Boost**. (Roshni Lite
   builds on Boost, so this setting lives there.)
2. Open the **Experimental settings** tab.
3. Tick **Enable colour modes** and save.
4. Optionally set **Default colour mode** for people who haven't chosen one:
   *Light* (Moodle's recommendation while it's experimental), *Dark*, or
   *System* (follows each device's setting).

**How people use it:** a **sun / moon icon** appears in the top navbar. Its menu
offers **Light**, **Dark** and **System**.

- **Logged-in users**: Moodle saves the choice to their account.
- **Visitors who aren't logged in**: the theme shows the same menu, and the
  choice is remembered in their browser, including on the login page.

**What changes in dark mode:** pages, cards, the navbar and text switch to dark
colours; your brand colour is lightened for text so it stays readable. The
site home footer stays black in both modes. The bundled Roshni Lite logo is
recoloured automatically. **An uploaded logo is never recoloured**, so check it
reads well on a dark background.

Moodle notes that plugins which haven't been updated for dark mode may still
show light areas.

## 7. Dashboard, login page and course pages

- **Dashboard**: logged-in users get a personal header above the usual blocks:
  a greeting for the time of day, their learning numbers, the course to jump
  back into, upcoming deadlines and shortcuts. Nothing to set.
- **Login page**: follows Moodle's own login design for your version. On Moodle
  5.3 it adds a welcome panel in your brand colours next to the form.
- **Course pages, admin pages, navigation and drawers**: exactly as your Moodle
  version ships them, in the theme's colours and fonts.
- **All courses / category pages / course search**: Moodle's own lists, styled
  as the same course cards as the site home.

## 8. Changing fixed wording

Text that isn't a setting (for example "Start learning today", "Browse courses",
the footer's "Explore" and "Subjects" headings, "Back to top") can be changed
without touching code:

1. Go to **Site administration > Language > Language customisation**.
2. Choose your language and **Open language pack for editing**.
3. Filter by component **theme_roshnilite**, find the text, and enter your own.
4. **Save changes to the language pack**.

Useful string names: `ctatitle`, `ctatext`, `herocta`, `footerexplore`,
`footersubjects`, `footercontact`, `footersearch`, `footertotop`,
`footersummarydefault`.

## 9. Troubleshooting

| Problem | What to check |
|---|---|
| A change doesn't show | Reload the page. If it still doesn't show, go to **Site administration > Development > Purge caches** and choose **Purge all caches** |
| A section is missing | Check its "Shown when" condition in [the section table](#3-how-the-site-home-is-built). Most sections hide when there's nothing real to show |
| A social or faculty link is missing | It must be a full address starting with `https://` |
| No price or "Free" on a course card | The course needs *Enrolment on payment* with a cost, or open *Self enrolment* without a key |
| Learner count missing on a card | It appears from 10 learners |
| Numbers row missing in the hero or footer | At least two counts must reach 6 courses, 3 categories or 50 learners |
| Event not shown | It must be a **site** event, in the future |
| No sun/moon icon | Moodle 5.3+ only, and **Enable colour modes** must be ticked (see [Dark mode](#6-dark-mode)) |
| Logo hard to see in dark mode | Upload a version that works on dark backgrounds, or a transparent logo with light-friendly colours |
| Can't remove the slider | Clear slide 1's image, text, button text and link (see [Hero](#41-hero)) |
| A "Purge all caches" / "Reactive instances" bar under the footer | That's Moodle's developer debugging output. Turn off **Site administration > Development > Debugging > Display debug messages** on a live site |
