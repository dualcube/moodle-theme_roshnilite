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
 * Faculty settings for theme_roshnilite.
 *
 * @package    theme_roshnilite
 * @author DualCube <admin@dualcube.com>
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_roshnilite_faculty', get_string('facultysettings', 'theme_roshnilite'));

$name = 'theme_roshnilite/facultycount';
$title = get_string('facultycount', 'theme_roshnilite');
$description = get_string('facultycountdesc', 'theme_roshnilite');
$setting = new admin_setting_configselect(
    $name,
    $title,
    $description,
    0,
    [
        1 => get_string('one', 'theme_roshnilite'),
        2 => get_string('two', 'theme_roshnilite'),
        3 => get_string('three', 'theme_roshnilite'),
        4 => get_string('four', 'theme_roshnilite'),
        5 => get_string('five', 'theme_roshnilite'),
        6 => get_string('six', 'theme_roshnilite'),
        7 => get_string('seven', 'theme_roshnilite'),
        8 => get_string('eight', 'theme_roshnilite'),
    ]
);
$page->add($setting);

for ($facultycounts = 1; $facultycounts <= get_config('theme_roshnilite', 'facultycount'); $facultycounts++) {
    $name = 'theme_roshnilite/facultyimage' . $facultycounts;
    $title = get_string('facultyimage', 'theme_roshnilite') . $facultycounts;
    $description = get_string('facultyimagedesc', 'theme_roshnilite') . $facultycounts;
    $default = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'facultyimage' . $facultycounts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/facultyname' . $facultycounts;
    $title = get_string('facultyname', 'theme_roshnilite') . $facultycounts;
    $description = get_string('facultynamedesc', 'theme_roshnilite') . $facultycounts;
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/facultysubtext' . $facultycounts;
    $title = get_string('facultysubtext', 'theme_roshnilite') . $facultycounts;
    $description = get_string('facultysubtextdesc', 'theme_roshnilite') . $facultycounts;
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/facultyfburl' . $facultycounts;
    $title = get_string('facultyfburl', 'theme_roshnilite') . $facultycounts;
    $description = get_string('facultyfburldesc', 'theme_roshnilite') . $facultycounts;
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/facultylnkdnurl' . $facultycounts;
    $title = get_string('facultylnkdnurl', 'theme_roshnilite') . $facultycounts;
    $description = get_string('facultylnkdnurldesc', 'theme_roshnilite') . $facultycounts;
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/facultygoogleurl' . $facultycounts;
    $title = get_string('facultygoogleurl', 'theme_roshnilite') . $facultycounts;
    $description = get_string('facultygoogleurldesc', 'theme_roshnilite') . $facultycounts;
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/facultytwitterurl' . $facultycounts;
    $title = get_string('facultytwitterurl', 'theme_roshnilite') . $facultycounts;
    $description = get_string('facultytwitterurldesc', 'theme_roshnilite') . $facultycounts;
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

$settings->add($page);
