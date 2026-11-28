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
 * Site home layout for the Roshni Lite theme.
 *
 * Uses Boost's navbar (primary navigation, user menu, mobile drawer) and
 * footer, so they match every supported Moodle release, around the theme's
 * own site home sections.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$renderer = $PAGE->get_renderer('core');
$primary = new \core\navigation\output\primary($PAGE);
$primarymenu = $primary->export_for_template($renderer);

$frontpage = new \theme_roshnilite\output\frontpage($PAGE->theme);
$frontpagedata = $frontpage->export_for_template($renderer);
// The standard site home content is always shown in edit mode, so it can be edited.
$frontpagedata['showmaincontent'] = $frontpagedata['showmaincontent'] || $PAGE->user_is_editing();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), 'escape' => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $OUTPUT->body_attributes(['roshnilite-frontpage']),
    'primarymoremenu' => $primarymenu['moremenu'],
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'frontpage' => $frontpagedata,
];

echo $OUTPUT->render_from_template('theme_roshnilite/frontpage', $templatecontext);
