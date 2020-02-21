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
 * Moodle's roshnilite theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package    theme_roshnilite
 * @copyright  2020 DualCube {@link https://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $PAGE;

if (!empty($PAGE->theme->setting_file_url('logo', 'logo'))) {
    $imgpath = $PAGE->theme->setting_file_url('logo', 'logo');
} else {
    $imgpath = $CFG->wwwroot."/theme/roshnilite/style/img/logo.png";
}

if (!empty($PAGE->theme->setting_file_url('favicon', 'favicon'))) {
    $favicon = $PAGE->theme->setting_file_url('favicon', 'favicon');
} else {
    $favicon = $CFG->wwwroot."/theme/roshnilite/pix/favicon.ico";
}

$bodyattributes = $OUTPUT->body_attributes();
$blockspre = $OUTPUT->blocks('side-pre');
$blockspost = $OUTPUT->blocks('side-post');

$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);


if ($CFG->version >= 2018120300) {
    $version18 = $OUTPUT->standard_after_main_region_html();
} else {
    $version18 = '';
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockspre,
    'sidepostblocks' => $blockspost,
    'haspreblocks' => $hassidepre,
    'haspostblocks' => $hassidepost,
    'bodyattributes' => $bodyattributes,
    'version18' => $version18,
    'imgpath' => $imgpath,
    'favicon' => $favicon,
];

echo $OUTPUT->render_from_template('theme_roshnilite/columns', $templatecontext);