<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace theme_roshnilite\output;

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use core\url;
use core_course_category;

/**
 * Data for the sections of the Roshni Lite site home.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontpage implements renderable, templatable {
    /** @var int Most courses shown in the course grid. */
    const MAX_COURSES = 8;

    /** @var int Most categories shown in the category grid. */
    const MAX_CATEGORIES = 8;

    /** @var int Number of "about the site" items. */
    const ABOUT_ITEMS = 4;

    /** @var int Number of social links in the footer band. */
    const SOCIAL_LINKS = 4;

    /** @var int Courses fanned out as the hero visual. */
    const DECK_COURSES = 3;

    /**
     * @var int[] The smallest number worth showing for each headline stat. Small
     * numbers make a new site look empty, so a stat below its minimum is left out.
     */
    const STAT_MINIMUMS = ['courses' => 6, 'categories' => 3, 'learners' => 50];

    /** @var \theme_config The theme whose settings are shown. */
    protected \theme_config $theme;

    /** @var \context_system The system context, used to format admin-entered text. */
    protected \context_system $context;

    /**
     * Constructor.
     *
     * @param \theme_config $theme The theme whose settings are shown.
     */
    public function __construct(\theme_config $theme) {
        $this->theme = $theme;
        $this->context = \context_system::instance();
    }

    /**
     * Export the data for the frontpage template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $courses = $this->export_courses($output);
        $categories = $this->export_categories();
        $showmaincontent = !empty($this->setting('moodlemaincontentinfrontpage'));
        return [
            'hero' => $this->export_hero($output),
            'search' => ['action' => (new url('/course/search.php'))->out(false)],
            'featured' => $courses['items'][0] ?? null,
            'deck' => $this->pick_deck($courses['items'] ?? []),
            'stats' => $this->export_stats(),
            'cta' => $this->export_cta(),
            'slider' => $this->export_slider($output),
            'about' => $this->export_about($output),
            // Moodle's own course list, when the site home shows it, replaces the grid.
            'courses' => $showmaincontent && $this->core_lists_all_courses() ? null : $courses,
            'categories' => $categories,
            'faculty' => $this->export_faculty($output),
            'contact' => $this->export_contact(),
            'social' => $this->export_social(),
            'showmaincontent' => $showmaincontent,
        ];
    }

    /**
     * The courses fanned out in the hero: the first course, then courses from
     * other subjects where there are any, so the deck shows the range on offer.
     *
     * @param array $items Exported course items, in catalogue order.
     * @return array
     */
    protected function pick_deck(array $items): array {
        $deck = [];
        $subjects = [];
        foreach ($items as $item) {
            if (count($deck) < self::DECK_COURSES && !isset($subjects[$item['category']])) {
                $deck[] = $item;
                $subjects[$item['category']] = true;
            }
        }
        foreach ($items as $item) {
            if (count($deck) < self::DECK_COURSES && !in_array($item, $deck, true)) {
                $deck[] = $item;
            }
        }
        return $deck;
    }

    /**
     * Whether Moodle's own site home content lists every course for this user.
     *
     * @return bool
     */
    protected function core_lists_all_courses(): bool {
        global $CFG;
        $items = isloggedin() && !isguestuser() ? ($CFG->frontpageloggedin ?? '') : ($CFG->frontpage ?? '');
        return in_array(FRONTPAGEALLCOURSELIST, explode(',', (string) $items), true);
    }

    /**
     * Formatted HTML as a short single line of plain text for cards.
     *
     * Unlike html_to_text(), emphasis is not turned into capitals.
     *
     * @param string $html
     * @param int $length The maximum length.
     * @return string
     */
    public static function plain_text(string $html, int $length): string {
        // Keep words apart where blocks and line breaks end.
        $html = preg_replace('~<(br|/p|/div|/li|/h[1-6]|/td)\b[^>]*>~i', '$0 ', $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return shorten_text(trim(preg_replace('/\s+/u', ' ', $text)), $length);
    }

    /**
     * Read a raw theme setting.
     *
     * @param string $name
     * @return string
     */
    protected function setting(string $name): string {
        return (string) ($this->theme->settings->{$name} ?? '');
    }

    /**
     * Format admin-entered rich text from a setting.
     *
     * @param string $text
     * @return string
     */
    protected function format_html(string $text): string {
        return format_text($text, FORMAT_HTML, ['context' => $this->context]);
    }

    /**
     * URL of a file uploaded to a theme file setting, or null when none is uploaded.
     *
     * @param string $setting
     * @return string|null
     */
    protected function file_url(string $setting): ?string {
        $url = $this->theme->setting_file_url($setting, $setting);
        return empty($url) ? null : $url;
    }

    /**
     * "1 course" / "5 courses".
     *
     * @param int $count
     * @return string
     */
    protected function course_count(int $count): string {
        return $count === 1
            ? get_string('coursecountone', 'theme_roshnilite')
            : get_string('coursecount', 'theme_roshnilite', $count);
    }

    /**
     * Headline numbers for the hero: courses, categories and learners, each
     * only once it is big enough to be worth showing.
     *
     * @return array
     */
    protected function export_stats(): array {
        global $DB, $CFG;
        $courses = $DB->count_records_select('course', 'id <> :siteid AND visible = 1', ['siteid' => SITEID]);
        $categories = $DB->count_records('course_categories', ['visible' => 1]);
        $learners = $DB->count_records_select(
            'user',
            'deleted = 0 AND suspended = 0 AND confirmed = 1 AND id <> :guestid',
            ['guestid' => $CFG->siteguest]
        );
        $stats = [
            'courses' => ['value' => $courses, 'label' => get_string('courses')],
            'categories' => ['value' => $categories, 'label' => get_string('categories')],
            'learners' => ['value' => $learners, 'label' => get_string('statlearners', 'theme_roshnilite')],
        ];
        $stats = array_filter($stats, function (array $stat, string $key): bool {
            return $stat['value'] >= self::STAT_MINIMUMS[$key];
        }, ARRAY_FILTER_USE_BOTH);
        // One number on its own reads as a boast; show the strip only with two or more.
        return count($stats) >= 2 ? array_values($stats) : [];
    }

    /**
     * The closing call to action: browse courses, and sign up when self-registration is on.
     *
     * @return array
     */
    protected function export_cta(): array {
        global $CFG;
        require_once($CFG->libdir . '/authlib.php');
        $signup = (!isloggedin() || isguestuser()) && signup_is_enabled();
        return [
            'coursesurl' => (new url('/course/index.php'))->out(false),
            'signupurl' => $signup ? (new url('/login/signup.php'))->out(false) : null,
        ];
    }

    /**
     * The hero shown when no slides are configured: the site name and summary.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function export_hero(renderer_base $output): array {
        global $SITE;
        $summary = format_text($SITE->summary ?? '', $SITE->summaryformat ?? FORMAT_HTML, ['context' => $this->context]);
        return [
            'title' => format_string($SITE->fullname, true, ['context' => $this->context, 'escape' => false]),
            'summary' => self::plain_text($summary, 220),
            'coursesurl' => (new url('/course/index.php'))->out(false),
            // Without an uploaded slide image the hero shows real courses instead of a stock photo.
            'image' => null,
            'defaultimage' => $output->image_url('sl-1', 'theme_roshnilite')->out(false),
        ];
    }

    /**
     * The hero slider.
     *
     * @param renderer_base $output
     * @return array|null
     */
    protected function export_slider(renderer_base $output): ?array {
        $count = (int) $this->setting('slidercount');
        if ($count < 1) {
            return null;
        }
        $hasfirst = $this->file_url('slideimage1') || $this->setting('slidertext1') !== ''
            || $this->setting('sliderbuttontext1') !== '' || $this->setting('sliderurl1') !== '';
        if (!$hasfirst) {
            return null;
        }

        $slides = [];
        for ($i = 1; $i <= $count; $i++) {
            $buttontext = $this->setting('sliderbuttontext' . $i);
            $slides[] = [
                'index' => $i - 1,
                'number' => $i,
                'active' => $i === 1,
                'image' => $this->file_url('slideimage' . $i),
                'text' => $this->format_html($this->setting('slidertext' . $i)),
                'buttontext' => $buttontext === '' ? null
                    : format_string($buttontext, true, ['context' => $this->context, 'escape' => false]),
                'url' => $this->setting('sliderurl' . $i),
            ];
        }
        return ['slides' => $slides, 'multiple' => count($slides) > 1];
    }

    /**
     * The "about the site" feature items.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function export_about(renderer_base $output): array {
        global $USER;

        $defaulturls = [
            1 => new url('/mod/forum/user.php', ['id' => $USER->id]),
            2 => new url('/course/index.php'),
            3 => new url('/blog/index.php', ['userid' => $USER->id]),
            4 => new url('/calendar/view.php'),
        ];
        $defaulttexts = [
            1 => 'aboutsitetext1default',
            2 => 'aboutsitetext2default',
            3 => 'aboutsitetext3default',
            4 => 'fraboutsitetextdescdefault',
        ];

        $icons = [1 => 'fa-pen-nib', 2 => 'fa-graduation-cap', 3 => 'fa-newspaper', 4 => 'fa-calendar-days'];
        $items = [];
        for ($i = 1; $i <= self::ABOUT_ITEMS; $i++) {
            $name = $this->setting('aboutsitename' . $i) ?: get_string('aboutsitename' . $i . 'default', 'theme_roshnilite');
            $text = $this->setting('aboutsitetext' . $i) ?: get_string($defaulttexts[$i], 'theme_roshnilite');
            $url = $this->setting('aboutsiteurl' . $i) ?: $defaulturls[$i]->out(false);
            $items[] = [
                'name' => format_string($name, true, ['context' => $this->context, 'escape' => false]),
                'text' => $this->format_html($text),
                'url' => $url,
                // An uploaded image wins; otherwise a Font Awesome icon.
                'image' => $this->file_url('aboutsiteimage' . $i),
                'icon' => $icons[$i],
            ];
        }

        return [
            'heading' => $this->format_html($this->setting('aboutsiteheading')
                ?: get_string('aboutsiteheadingdefault', 'theme_roshnilite')),
            'subheading' => $this->format_html($this->setting('aboutsitesubheading')
                ?: get_string('aboutsitesubheadingdefault', 'theme_roshnilite')),
            'items' => $items,
        ];
    }

    /**
     * The course grid. Only courses the current user can see are listed.
     *
     * @param renderer_base $output
     * @return array|null
     */
    protected function export_courses(renderer_base $output): ?array {
        $courses = core_course_category::top()->get_courses([
            'recursive' => true,
            'sort' => ['sortorder' => 1],
            'limit' => self::MAX_COURSES,
            'summary' => true,
            'coursecontacts' => true,
        ]);
        if (empty($courses)) {
            return null;
        }

        $items = [];
        foreach (array_values($courses) as $index => $course) {
            $context = \context_course::instance($course->id);
            $name = format_string($course->fullname, true, ['context' => $context, 'escape' => false]);
            $category = core_course_category::get($course->category, IGNORE_MISSING);
            $summary = $course->has_summary() ? format_text(
                file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null),
                $course->summaryformat,
                ['context' => $context]
            ) : '';
            $contacts = $course->get_course_contacts();
            $teacher = $contacts ? reset($contacts) : null;
            $items[] = course_cover::export($course, $name) + [
                'active' => $index === 0,
                'category' => $category ? $category->get_formatted_name(['escape' => false]) : '',
                'name' => $name,
                'summary' => self::plain_text($summary, 120),
                'teacher' => $teacher ? [
                    'name' => $teacher['username'],
                    'initials' => $this->initials($teacher['username']),
                ] : null,
                'url' => (new url('/course/view.php', ['id' => $course->id]))->out(false),
            ];
        }

        return [
            'items' => $items,
            'allcoursesurl' => (new url('/course/index.php'))->out(false),
        ];
    }

    /**
     * The course category grid.
     *
     * @return array|null
     */
    protected function export_categories(): ?array {
        $list = core_course_category::make_categories_list();
        if (count($list) < 2) {
            return null;
        }

        $items = [];
        $index = 0;
        foreach (array_keys($list) as $id) {
            if (count($items) >= self::MAX_CATEGORIES) {
                break;
            }
            $category = core_course_category::get($id, IGNORE_MISSING);
            // Empty subjects are left out: "0 courses" only makes a catalogue look thin.
            $count = $category ? $category->get_courses_count(['recursive' => true]) : 0;
            if (!$count) {
                continue;
            }
            $description = format_text(
                $category->description ?? '',
                $category->descriptionformat ?? FORMAT_HTML,
                ['context' => $category->get_context()]
            );
            $items[] = [
                'tone' => course_cover::tone((int) $id),
                'active' => $index++ === 0,
                'name' => $category->get_formatted_name(['escape' => false]),
                'coursecount' => $this->course_count($count),
                'description' => self::plain_text($description, 160),
                'url' => (new url('/course/index.php', ['categoryid' => $id]))->out(false),
            ];
        }

        if (count($items) < 2) {
            return null;
        }

        return [
            'heading' => $this->format_html($this->setting('masonryheading')
                ?: get_string('masonryheadingdefault', 'theme_roshnilite')),
            'subheading' => $this->format_html($this->setting('masonrysubheading')
                ?: get_string('masonrysubheadingdefault', 'theme_roshnilite')),
            'items' => $items,
            'allcategoriesurl' => (new url('/course/index.php'))->out(false),
        ];
    }

    /**
     * The faculty section.
     *
     * @param renderer_base $output
     * @return array|null
     */
    protected function export_faculty(renderer_base $output): ?array {
        $count = (int) $this->setting('facultycount');
        if ($count < 1) {
            return null;
        }
        $hasfirst = $this->file_url('facultyimage1') || $this->setting('facultyname1') !== ''
            || $this->setting('facultysubtext1') !== '';
        if (!$hasfirst) {
            return null;
        }

        $networks = [
            'facultyfburl' => ['icon' => 'fa-brands fa-facebook-f', 'label' => 'socialfacebook', 'class' => 'icoFacebook'],
            'facultytwitterurl' => ['icon' => 'fa-brands fa-x-twitter', 'label' => 'socialtwitter', 'class' => 'icoTwitter'],
            'facultygoogleurl' => ['icon' => 'fa-brands fa-google', 'label' => 'socialgoogle', 'class' => 'icoGoogle'],
            'facultylnkdnurl' => ['icon' => 'fa-brands fa-linkedin-in', 'label' => 'sociallinkedin', 'class' => 'icoLinkedin'],
        ];

        $members = [];
        for ($i = 1; $i <= $count; $i++) {
            $links = [];
            foreach ($networks as $setting => $network) {
                $url = $this->setting($setting . $i);
                if ($url === '') {
                    continue;
                }
                $links[] = [
                    'url' => $url,
                    'icon' => $network['icon'],
                    'class' => $network['class'],
                    'label' => get_string($network['label'], 'theme_roshnilite'),
                ];
            }
            $name = format_string($this->setting('facultyname' . $i), true, ['context' => $this->context, 'escape' => false]);
            $members[] = [
                'active' => $i === 1,
                'name' => $name,
                'initials' => $this->initials($name),
                'text' => $this->format_html($this->setting('facultysubtext' . $i)),
                'image' => $this->file_url('facultyimage' . $i),
                'links' => $links,
                'haslinks' => !empty($links),
            ];
        }

        return [
            'heading' => get_string('facultyheading', 'theme_roshnilite'),
            'members' => $members,
        ];
    }

    /**
     * Up to two initials for an avatar, e.g. "JD" for "Jane Doe".
     *
     * @param string $name
     * @return string
     */
    protected function initials(string $name): string {
        $words = preg_split('/\s+/u', trim(html_entity_decode(strip_tags($name))), -1, PREG_SPLIT_NO_EMPTY);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= \core_text::substr($word, 0, 1);
        }
        return \core_text::strtoupper($initials);
    }

    /**
     * The contact details band.
     *
     * @return array
     */
    protected function export_contact(): array {
        $items = [];
        $fields = [
            'address' => '<i class="fa fa-map-marker" aria-hidden="true"></i>',
            'email' => '<i class="fa fa-envelope" aria-hidden="true"></i>',
            'phone' => '<i class="fa fa-phone" aria-hidden="true"></i>',
        ];
        foreach ($fields as $field => $defaulticon) {
            $value = $this->setting($field) ?: get_string($field . 'default', 'theme_roshnilite');
            $icon = $this->setting($field . 'fontawesomeicon') ?: $defaulticon;
            $items[] = [
                'icon' => $this->format_html($icon),
                'text' => $this->format_html($value),
            ];
        }
        return ['items' => $items];
    }

    /**
     * The "stay connected" social links band.
     *
     * @return array
     */
    protected function export_social(): array {
        $defaulticons = [
            1 => '<i class="fa fa-facebook" aria-hidden="true"></i>',
            2 => '<i class="fa fa-twitter" aria-hidden="true"></i>',
            3 => '<i class="fa fa-linkedin" aria-hidden="true"></i>',
            4 => '<i class="fa fa-youtube" aria-hidden="true"></i>',
        ];
        $links = [];
        for ($i = 1; $i <= self::SOCIAL_LINKS; $i++) {
            $links[] = [
                'url' => $this->setting('socialicon' . $i) ?: '#',
                'icon' => $this->format_html($this->setting('socialfontawesomeicon' . $i) ?: $defaulticons[$i]),
                'label' => get_string('socialiconlabel', 'theme_roshnilite', $i),
            ];
        }
        return [
            'heading' => $this->format_html($this->setting('socialheading')
                ?: get_string('socialheadingdefault', 'theme_roshnilite')),
            'links' => $links,
        ];
    }
}
