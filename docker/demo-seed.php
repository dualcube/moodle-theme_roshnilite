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

/**
 * Populates the dev container with just enough demo content (a slide,
 * course categories, courses, and faculty entries) for the theme_roshnilite
 * frontpage to render every section - the slider, "our category" and
 * faculty carousels are otherwise empty on a stock Moodle install.
 *
 * Run once from entrypoint.sh right after install, via Moodle's CLI
 * bootstrap (not meant to be run standalone or shipped with the theme
 * itself - it lives under docker/, not the plugin root). Copied into the
 * image outside the webroot (see Dockerfile), so the Moodle path below is
 * absolute rather than __DIR__-relative.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');

// Slide 1: turn the slider on. theme_roshnilite's admin_setting defaults for
// slidertext1/sliderbuttontext1/sliderurl1 only get written to config once
// the settings form is saved through the admin UI, so they're set explicitly
// here rather than assumed. No slideimage1 is set - the theme falls back to
// its own bundled pix/sl-1.jpg.
set_config('slidercount', 1, 'theme_roshnilite');
set_config('slidertext1', get_string('slidertextdefault', 'theme_roshnilite'), 'theme_roshnilite');
set_config('sliderbuttontext1', get_string('sliderbuttontextdefault', 'theme_roshnilite'), 'theme_roshnilite');
set_config('sliderurl1', get_string('sliderurldefault', 'theme_roshnilite'), 'theme_roshnilite');

// Faculty carousel. The social link fields fall back to the same
// admin_setting default ('javascript:void(0);') that never gets persisted
// without a form save, and an unset href would otherwise render as href="" -
// invalid HTML - so they're set explicitly too.
set_config('facultycount', 2, 'theme_roshnilite');
$sociallinkdefault = get_string('sliderurldefault', 'theme_roshnilite');
for ($i = 1; $i <= 2; $i++) {
    set_config('facultyfburl' . $i, $sociallinkdefault, 'theme_roshnilite');
    set_config('facultylnkdnurl' . $i, $sociallinkdefault, 'theme_roshnilite');
    set_config('facultygoogleurl' . $i, $sociallinkdefault, 'theme_roshnilite');
    set_config('facultytwitterurl' . $i, $sociallinkdefault, 'theme_roshnilite');
}
set_config('facultyname1', 'Faculty Member One', 'theme_roshnilite');
set_config('facultysubtext1', 'Passionate educator with years of classroom experience.', 'theme_roshnilite');
set_config('facultyname2', 'Faculty Member Two', 'theme_roshnilite');
set_config('facultysubtext2', 'Dedicated to helping every student reach their goals.', 'theme_roshnilite');

// The "our category" carousel needs more than one category to show.
$categories = [
    'History' => 'The study of past events, particularly in human affairs.',
    'Geography' => 'The study of places and the relationships between people and their environments.',
    'Science' => 'The systematic study of the structure and behaviour of the physical and natural world.',
    'Mathematics' => 'The study of numbers, quantity, structure, and space.',
];
foreach ($categories as $name => $description) {
    if (!$DB->record_exists('course_categories', ['name' => $name])) {
        core_course_category::create([
            'name' => $name,
            'description' => $description,
        ]);
    }
}

// Courses carousel needs at least one visible course other than the site course.
$courses = ['Introduction to Architecture', 'Modern Design Principles', 'World Heritage Sites'];
foreach ($courses as $name) {
    if (!$DB->record_exists('course', ['fullname' => $name])) {
        $data = new stdClass();
        $data->fullname = $name;
        $data->shortname = $name;
        $data->category = 1;
        $data->visible = 1;
        create_course($data);
    }
}

echo "Demo content seeded.\n";
