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
    /** @var int Most courses shown for each subject in the catalogue. */
    const MAX_COURSES = 8;

    /** @var int Most subjects offered as catalogue tabs. */
    const MAX_SUBJECTS = 6;

    /** @var int Courses read for the catalogue, across all subjects. */
    const CATALOGUE_LIMIT = 60;

    /** @var int A course's learner count is shown from this number up. */
    const MIN_LEARNERS_SHOWN = 10;

    /** @var int Days for which a course is badged as new. */
    const NEW_DAYS = 30;

    /** @var int Number of "about the site" items. */
    const ABOUT_ITEMS = 4;

    /** @var int Number of social links in the footer band. */
    const SOCIAL_LINKS = 4;

    /** @var int Steps in "How it works". */
    const STEPS = 3;

    /** @var int Site events listed under "Upcoming events". */
    const EVENTS = 3;

    /** @var int Testimonials that can be set. */
    const TESTIMONIALS = 3;

    /** @var int Questions that can be set. */
    const QUESTIONS = 4;

    /** @var int Courses listed in the hero's course preview. */
    const PREVIEW_COURSES = 4;

    /** @var int Teachers shown as avatars in the hero. */
    const TEACHER_AVATARS = 4;

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
        $catalogue = $this->export_catalogue();
        // Moodle's own site home content: its full course list (and course search) would
        // repeat the catalogue and the hero search, so they give way; anything else stays.
        $coreitems = $this->core_frontpage_items();
        $othercore = array_diff($coreitems, [FRONTPAGEALLCOURSELIST, FRONTPAGECOURSESEARCH]);
        $showmaincontent = !empty($this->setting('moodlemaincontentinfrontpage')) && ($catalogue === null || $othercore);
        $events = $this->export_events();
        $preview = $this->pick_preview($catalogue);
        return [
            'hero' => $this->export_hero($output),
            'search' => ['action' => (new url('/course/search.php'))->out(false)],
            'featured' => $catalogue['subjects'][0]['courses'][0] ?? null,
            'announcement' => $this->pick_announcement($catalogue),
            'preview' => $preview ? [
                'courses' => $preview,
                'url' => (new url('/course/index.php'))->out(false),
                'event' => $events['items'][0] ?? null,
            ] : null,
            'teachers' => $this->export_teachers($catalogue),
            'stats' => $this->export_stats(),
            'cta' => $this->export_cta(),
            'slider' => $this->export_slider($output),
            'about' => $this->export_about($output),
            'catalogue' => $catalogue,
            'faculty' => $this->export_faculty($output),
            'steps' => $this->export_steps(),
            'events' => $events,
            'testimonials' => $this->export_testimonials(),
            'faq' => $this->export_faq(),
            'contact' => $this->export_contact(),
            'social' => $this->export_social(),
            'footer' => $this->export_footer(),
            'showmaincontent' => $showmaincontent,
            'hidecorecourselist' => $catalogue !== null && in_array(FRONTPAGEALLCOURSELIST, $coreitems, true),
        ];
    }

    /**
     * The courses listed in the hero's course preview: the first course of each
     * subject, so the preview shows the range on offer, topped up from the first
     * subject. Each row carries its subject's name and colour.
     *
     * @param array|null $catalogue
     * @return array
     */
    protected function pick_preview(?array $catalogue): array {
        $rows = [];
        $add = function (array $course, array $subject) use (&$rows): void {
            $rows[] = $course + ['subject' => $subject['name'], 'subjecttone' => $subject['tone']];
        };
        foreach ($catalogue['subjects'] ?? [] as $subject) {
            $add($subject['courses'][0], $subject);
        }
        if (!empty($catalogue['subjects'])) {
            foreach (array_slice($catalogue['subjects'][0]['courses'], 1) as $course) {
                $add($course, $catalogue['subjects'][0]);
            }
        }
        $rows = array_slice($rows, 0, self::PREVIEW_COURSES);
        foreach ($rows as $index => $row) {
            $rows[$index]['highlight'] = $index === 1;
        }
        return $rows;
    }

    /**
     * The newest course still badged as new, announced above the hero headline.
     *
     * @param array|null $catalogue
     * @return array|null
     */
    protected function pick_announcement(?array $catalogue): ?array {
        $newest = null;
        foreach ($catalogue['subjects'] ?? [] as $subject) {
            foreach ($subject['courses'] as $course) {
                if ($course['isnew'] && ($newest === null || $course['created'] > $newest['created'])) {
                    $newest = $course;
                }
            }
        }
        return $newest ? ['name' => $newest['name'], 'url' => $newest['url']] : null;
    }

    /**
     * The people teaching the catalogue's courses, shown as avatars in the hero;
     * null with fewer than two, since one name is not a faculty.
     *
     * @param array|null $catalogue
     * @return array|null
     */
    protected function export_teachers(?array $catalogue): ?array {
        $teachers = [];
        foreach ($catalogue['subjects'] ?? [] as $subject) {
            foreach ($subject['courses'] as $course) {
                if ($course['teacher']) {
                    $teachers[$course['teacher']['name']] = $course['teacher'];
                }
            }
        }
        if (count($teachers) < 2) {
            return null;
        }
        return [
            'avatars' => array_slice(array_values($teachers), 0, self::TEACHER_AVATARS),
            'label' => get_string('teacherscount', 'theme_roshnilite', count($teachers)),
        ];
    }

    /**
     * The items Moodle's own site home content shows to this user (Site home settings).
     *
     * @return string[]
     */
    protected function core_frontpage_items(): array {
        global $CFG;
        $items = isloggedin() && !isguestuser() ? ($CFG->frontpageloggedin ?? '') : ($CFG->frontpage ?? '');
        return array_values(array_diff(explode(',', (string) $items), ['', 'none']));
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
     * A web address from a setting, or null when it is empty or a placeholder
     * such as "#" or "javascript:void(0);" that leads nowhere.
     *
     * @param string $name
     * @return string|null
     */
    protected function link_setting(string $name): ?string {
        $url = trim($this->setting($name));
        return preg_match('~^(https?:)?//~i', $url) ? $url : null;
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
            4 => new url('/message/index.php'),
        ];
        $defaulttexts = [
            1 => 'aboutsitetext1default',
            2 => 'aboutsitetext2default',
            3 => 'aboutsitetext3default',
            4 => 'fraboutsitetextdescdefault',
        ];

        $icons = [1 => 'fa-pen-nib', 2 => 'fa-graduation-cap', 3 => 'fa-newspaper', 4 => 'fa-comments'];
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
     * The course catalogue: subjects with courses, each with its course cards.
     *
     * Subjects are the course categories that hold visible courses, in category
     * order; a site with courses in several subjects gets one tab per subject.
     *
     * @return array|null Null when there are no courses to show.
     */
    protected function export_catalogue(): ?array {
        $courses = core_course_category::top()->get_courses([
            'recursive' => true,
            'sort' => ['sortorder' => 1],
            'limit' => self::CATALOGUE_LIMIT,
            'summary' => true,
            'coursecontacts' => true,
        ]);
        if (empty($courses)) {
            return null;
        }

        $bysubject = [];
        foreach ($courses as $course) {
            $bysubject[$course->category][] = $course;
        }
        $learners = $this->count_learners(array_keys($courses));
        $access = $this->course_access(array_keys($courses));

        $subjects = [];
        foreach (array_keys(core_course_category::make_categories_list()) as $id) {
            if (empty($bysubject[$id]) || count($subjects) >= self::MAX_SUBJECTS) {
                continue;
            }
            $category = core_course_category::get($id, IGNORE_MISSING);
            if (!$category) {
                continue;
            }
            $total = $category->get_courses_count();
            $cards = [];
            foreach (array_slice($bysubject[$id], 0, self::MAX_COURSES) as $course) {
                $cards[] = $this->export_course_card($course, $learners[$course->id] ?? 0, $access[$course->id] ?? null);
            }
            $description = format_text(
                $category->description ?? '',
                $category->descriptionformat ?? FORMAT_HTML,
                ['context' => $category->get_context()]
            );
            $subjects[] = [
                'id' => (int) $id,
                'active' => !$subjects,
                'name' => $category->get_formatted_name(['escape' => false]),
                'tone' => course_cover::tone((int) $id),
                'description' => self::plain_text($description, 180),
                'coursecount' => $this->course_count($total),
                'url' => (new url('/course/index.php', ['categoryid' => $id]))->out(false),
                'courses' => $cards,
            ];
        }
        if (!$subjects) {
            return null;
        }

        return [
            'heading' => $this->format_html($this->setting('masonryheading')
                ?: get_string('masonryheadingdefault', 'theme_roshnilite')),
            'subheading' => $this->format_html($this->setting('masonrysubheading')
                ?: get_string('masonrysubheadingdefault', 'theme_roshnilite')),
            'subjects' => $subjects,
            'hastabs' => count($subjects) > 1,
            'allcoursesurl' => (new url('/course/index.php'))->out(false),
        ];
    }

    /**
     * A course card: cover, title, teacher, and the facts that are true for it.
     *
     * @param \core_course_list_element $course
     * @param int $learners Number of learners enrolled.
     * @param string|null $price How the course can be joined: a price, "Free", or null when unknown.
     * @return array
     */
    protected function export_course_card(\core_course_list_element $course, int $learners, ?string $price): array {
        $context = \context_course::instance($course->id);
        $name = format_string($course->fullname, true, ['context' => $context, 'escape' => false]);
        $summary = $course->has_summary() ? format_text(
            file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null),
            $course->summaryformat,
            ['context' => $context]
        ) : '';
        $contacts = $course->get_course_contacts();
        $teacher = $contacts ? reset($contacts) : null;
        return course_cover::export($course, $name) + [
            'name' => $name,
            'summary' => self::plain_text($summary, 160),
            'teacher' => $teacher ? [
                'name' => $teacher['username'],
                'initials' => $this->initials($teacher['username']),
            ] : null,
            'learners' => $learners >= self::MIN_LEARNERS_SHOWN
                ? get_string('learnerscount', 'theme_roshnilite', $learners) : null,
            'price' => $price,
            'isnew' => $course->timecreated > time() - self::NEW_DAYS * DAYSECS,
            'created' => (int) $course->timecreated,
            'url' => (new url('/course/view.php', ['id' => $course->id]))->out(false),
        ];
    }

    /**
     * Learners (users with a student role) per course, in one query.
     *
     * @param int[] $courseids
     * @return int[] Count per course id.
     */
    protected function count_learners(array $courseids): array {
        global $DB;
        $roleids = array_keys(get_archetype_roles('student'));
        if (!$courseids || !$roleids) {
            return [];
        }
        [$coursesql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'c');
        [$rolesql, $roleparams] = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED, 'r');
        $sql = "SELECT ctx.instanceid, COUNT(DISTINCT ra.userid)
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :level
                 WHERE ctx.instanceid $coursesql AND ra.roleid $rolesql
              GROUP BY ctx.instanceid";
        return array_map('intval', $DB->get_records_sql_menu($sql, ['level' => CONTEXT_COURSE] + $courseparams + $roleparams));
    }

    /**
     * How each course can be joined, from its enrolment methods: the fee of a
     * payment enrolment, or "Free" when learners can enrol themselves without
     * a key. Courses joined any other way get no label.
     *
     * @param int[] $courseids
     * @return string[] Label per course id.
     */
    protected function course_access(array $courseids): array {
        global $DB;
        $methods = array_values(array_filter(['fee', 'self'], 'enrol_is_enabled'));
        if (!$courseids || !$methods) {
            return [];
        }
        [$coursesql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'c');
        [$methodsql, $methodparams] = $DB->get_in_or_equal($methods, SQL_PARAMS_NAMED, 'm');
        $instances = $DB->get_records_select(
            'enrol',
            "courseid $coursesql AND enrol $methodsql AND status = :enabled",
            $courseparams + $methodparams + ['enabled' => ENROL_INSTANCE_ENABLED],
            'enrol ASC, sortorder ASC'
        );
        $access = [];
        foreach ($instances as $instance) {
            if ($instance->enrol === 'fee' && (float) $instance->cost > 0) {
                // A price wins over a free route only if there is no free route.
                $access[$instance->courseid] ??= \core_payment\helper::get_cost_as_string(
                    (float) $instance->cost,
                    (string) $instance->currency
                );
            } else if ($instance->enrol === 'self' && !empty($instance->customint6) && (string) $instance->password === '') {
                $access[$instance->courseid] = get_string('pricefree', 'theme_roshnilite');
            }
        }
        return $access;
    }

    /**
     * Whether a site home section's on/off setting is on (on unless switched off).
     *
     * @param string $name
     * @return bool
     */
    protected function section_enabled(string $name): bool {
        return ($this->theme->settings->{$name} ?? '1') !== '0';
    }

    /**
     * "How it works": the numbered steps.
     *
     * @return array|null
     */
    protected function export_steps(): ?array {
        if (!$this->section_enabled('showhowitworks')) {
            return null;
        }
        $icons = [1 => 'fa-magnifying-glass', 2 => 'fa-laptop', 3 => 'fa-chart-line'];
        $visuals = [1 => 'find', 2 => 'learn', 3 => 'track'];
        $steps = [];
        for ($i = 1; $i <= self::STEPS; $i++) {
            $title = $this->setting('steptitle' . $i) ?: get_string('steptitle' . $i . 'default', 'theme_roshnilite');
            $text = $this->setting('steptext' . $i) ?: get_string('steptext' . $i . 'default', 'theme_roshnilite');
            $steps[] = [
                'number' => $i,
                'icon' => $icons[$i],
                'visual' => [$visuals[$i] => true],
                'title' => format_string($title, true, ['context' => $this->context, 'escape' => false]),
                'text' => format_string($text, true, ['context' => $this->context, 'escape' => false]),
            ];
        }
        return ['items' => $steps];
    }

    /**
     * The next site events from the calendar; null when there are none.
     *
     * @return array|null
     */
    protected function export_events(): ?array {
        global $DB;
        if (!$this->section_enabled('showevents')) {
            return null;
        }
        $events = $DB->get_records_select(
            'event',
            "eventtype = :type AND visible = 1 AND timestart >= :now",
            ['type' => 'site', 'now' => time()],
            'timestart ASC',
            'id, name, timestart, timeduration, location',
            0,
            self::EVENTS
        );
        if (!$events) {
            return null;
        }
        $items = [];
        foreach ($events as $event) {
            $items[] = [
                'name' => format_string($event->name, true, ['context' => $this->context, 'escape' => false]),
                'day' => userdate($event->timestart, '%d'),
                'month' => userdate($event->timestart, '%b'),
                'when' => userdate($event->timestart, get_string('strftimedaytime', 'langconfig')),
                'location' => format_string((string) $event->location, true, ['context' => $this->context, 'escape' => false]),
                'url' => (new url('/calendar/view.php', ['view' => 'day', 'time' => $event->timestart]))->out(false)
                    . '#event_' . $event->id,
            ];
        }
        return [
            'items' => $items,
            'calendarurl' => (new url('/calendar/view.php', ['view' => 'upcoming']))->out(false),
        ];
    }

    /**
     * Testimonials the admin has entered; null when there are none.
     *
     * @return array|null
     */
    protected function export_testimonials(): ?array {
        $items = [];
        for ($i = 1; $i <= self::TESTIMONIALS; $i++) {
            $quote = trim($this->setting('testimonialquote' . $i));
            if ($quote === '') {
                continue;
            }
            $name = format_string($this->setting('testimonialname' . $i), true, ['context' => $this->context, 'escape' => false]);
            $items[] = [
                'quote' => format_string($quote, true, ['context' => $this->context, 'escape' => false]),
                'name' => $name,
                'initials' => $this->initials($name),
                'role' => format_string(
                    $this->setting('testimonialrole' . $i),
                    true,
                    ['context' => $this->context, 'escape' => false]
                ),
            ];
        }
        return $items ? ['items' => $items] : null;
    }

    /**
     * Questions and answers; null when switched off or all empty.
     *
     * @return array|null
     */
    protected function export_faq(): ?array {
        if (!$this->section_enabled('showfaq')) {
            return null;
        }
        $items = [];
        for ($i = 1; $i <= self::QUESTIONS; $i++) {
            // An unset setting takes its default; one an admin cleared stays empty and is skipped.
            $question = $this->theme->settings->{'faqquestion' . $i}
                ?? get_string('faqquestion' . $i . 'default', 'theme_roshnilite');
            $answer = $this->theme->settings->{'faqanswer' . $i}
                ?? get_string('faqanswer' . $i . 'default', 'theme_roshnilite');
            if (trim((string) $question) === '') {
                continue;
            }
            $items[] = [
                'question' => format_string($question, true, ['context' => $this->context, 'escape' => false]),
                'answer' => $this->format_html((string) $answer),
            ];
        }
        return $items ? ['items' => $items] : null;
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
                $url = $this->link_setting($setting . $i);
                if ($url === null) {
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
     * The contact details in the footer: only those the admin has filled in.
     *
     * @return array|null Null when none are set.
     */
    protected function export_contact(): ?array {
        $items = [];
        $fields = [
            'address' => '<i class="fa fa-location-dot" aria-hidden="true"></i>',
            'email' => '<i class="fa fa-envelope" aria-hidden="true"></i>',
            'phone' => '<i class="fa fa-phone" aria-hidden="true"></i>',
        ];
        foreach ($fields as $field => $defaulticon) {
            $value = $this->setting($field);
            if (trim(strip_tags($value)) === '') {
                continue;
            }
            $icon = $this->setting($field . 'fontawesomeicon') ?: $defaulticon;
            $items[] = [
                'icon' => $this->format_html($icon),
                'text' => $this->format_html($value),
            ];
        }
        return $items ? ['items' => $items] : null;
    }

    /**
     * The social links in the footer: only those with a real address.
     *
     * @return array|null Null when none are set.
     */
    protected function export_social(): ?array {
        $defaulticons = [
            1 => '<i class="fa fa-facebook" aria-hidden="true"></i>',
            2 => '<i class="fa fa-twitter" aria-hidden="true"></i>',
            3 => '<i class="fa fa-linkedin" aria-hidden="true"></i>',
            4 => '<i class="fa fa-youtube" aria-hidden="true"></i>',
        ];
        $links = [];
        for ($i = 1; $i <= self::SOCIAL_LINKS; $i++) {
            $url = $this->link_setting('socialicon' . $i);
            if ($url === null) {
                continue;
            }
            $links[] = [
                'url' => $url,
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

    /**
     * The footer: the site's name and summary, quick links, and the contact
     * details and social links that are set.
     *
     * @return array
     */
    protected function export_footer(): array {
        global $SITE;
        $summary = format_text($SITE->summary ?? '', $SITE->summaryformat ?? FORMAT_HTML, ['context' => $this->context]);
        $links = [
            ['url' => (new url('/course/index.php'))->out(false), 'text' => get_string('courses')],
            [
                'url' => (new url('/calendar/view.php', ['view' => 'upcoming']))->out(false),
                'text' => get_string('calendar', 'calendar'),
            ],
        ];
        if (isloggedin() && !isguestuser()) {
            $links[] = ['url' => (new url('/my/'))->out(false), 'text' => get_string('myhome')];
        } else {
            $links[] = ['url' => get_login_url(), 'text' => get_string('login')];
        }
        $sitename = format_string($SITE->fullname, true, ['context' => $this->context, 'escape' => false]);
        return [
            'sitename' => $sitename,
            'summary' => self::plain_text($summary, 160),
            'links' => $links,
            'copyright' => get_string('footercopyright', 'theme_roshnilite', [
                'year' => userdate(time(), '%Y'),
                'site' => $sitename,
            ]),
        ];
    }
}
