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
use core_course\external\course_summary_exporter;

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
        return [
            'login' => $this->export_login(),
            'hero' => $this->export_hero(),
            'slider' => $this->export_slider($output),
            'about' => $this->export_about($output),
            'courses' => $this->export_courses($output),
            'categories' => $this->export_categories(),
            'faculty' => $this->export_faculty($output),
            'contact' => $this->export_contact(),
            'social' => $this->export_social(),
            'showmaincontent' => !empty($this->setting('moodlemaincontentinfrontpage')),
        ];
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
     * The quick login form shown to visitors who are not logged in.
     *
     * @return array|null
     */
    protected function export_login(): ?array {
        if (isloggedin() && !isguestuser()) {
            return null;
        }
        return [
            'action' => (new url('/login/index.php'))->out(false),
            'logintoken' => \core\session\manager::get_login_token(),
            'forgoturl' => (new url('/login/forgot_password.php'))->out(false),
        ];
    }

    /**
     * The hero shown when no slides are configured: the site name and summary.
     *
     * @return array
     */
    protected function export_hero(): array {
        global $SITE;
        $summary = format_text($SITE->summary ?? '', $SITE->summaryformat ?? FORMAT_HTML, ['context' => $this->context]);
        return [
            'title' => format_string($SITE->fullname, true, ['context' => $this->context]),
            'summary' => trim(shorten_text(html_to_text($summary, 0, false), 220)),
            'coursesurl' => (new url('/course/index.php'))->out(false),
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
                'image' => $this->file_url('slideimage' . $i) ?? $output->image_url('sl-1', 'theme_roshnilite')->out(false),
                'text' => $this->format_html($this->setting('slidertext' . $i)),
                'buttontext' => $buttontext === '' ? null : format_string($buttontext, true, ['context' => $this->context]),
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

        $items = [];
        for ($i = 1; $i <= self::ABOUT_ITEMS; $i++) {
            $name = $this->setting('aboutsitename' . $i) ?: get_string('aboutsitename' . $i . 'default', 'theme_roshnilite');
            $text = $this->setting('aboutsitetext' . $i) ?: get_string($defaulttexts[$i], 'theme_roshnilite');
            $url = $this->setting('aboutsiteurl' . $i) ?: $defaulturls[$i]->out(false);
            $items[] = [
                'name' => format_string($name, true, ['context' => $this->context]),
                'text' => $this->format_html($text),
                'url' => $url,
                'image' => $this->file_url('aboutsiteimage' . $i)
                    ?? $output->image_url('icon-conts-' . $i, 'theme_roshnilite')->out(false),
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
        ]);
        if (empty($courses)) {
            return null;
        }

        $fallbackimage = $output->image_url('nopic', 'theme_roshnilite')->out(false);
        $items = [];
        foreach (array_values($courses) as $index => $course) {
            $context = \context_course::instance($course->id);
            $image = course_summary_exporter::get_course_image($course);
            $category = core_course_category::get($course->category, IGNORE_MISSING);
            $items[] = [
                'active' => $index === 0,
                'category' => $category ? $category->get_formatted_name() : '',
                'name' => format_string($course->fullname, true, ['context' => $context]),
                'url' => (new url('/course/view.php', ['id' => $course->id]))->out(false),
                'image' => $image ?: $fallbackimage,
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
        foreach (array_slice(array_keys($list), 0, self::MAX_CATEGORIES) as $id) {
            $category = core_course_category::get($id, IGNORE_MISSING);
            if (!$category) {
                continue;
            }
            $description = format_text(
                $category->description ?? '',
                $category->descriptionformat ?? FORMAT_HTML,
                ['context' => $category->get_context()]
            );
            $items[] = [
                'active' => $index++ === 0,
                'name' => $category->get_formatted_name(),
                'coursecount' => $this->course_count($category->get_courses_count()),
                'description' => trim(shorten_text(html_to_text($description, 0, false), 160)),
                'url' => (new url('/course/index.php', ['categoryid' => $id]))->out(false),
            ];
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

        $fallbackimage = $output->image_url('nopic', 'theme_roshnilite')->out(false);
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
            $members[] = [
                'active' => $i === 1,
                'name' => format_string($this->setting('facultyname' . $i), true, ['context' => $this->context]),
                'text' => $this->format_html($this->setting('facultysubtext' . $i)),
                'image' => $this->file_url('facultyimage' . $i) ?? $fallbackimage,
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
