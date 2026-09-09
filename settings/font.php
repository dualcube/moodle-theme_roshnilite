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
 * Font settings for theme_roshnilite.
 *
 * @package    theme_roshnilite
 * @author DualCube <admin@dualcube.com>
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_roshnilite_font', get_string('fontsettings', 'theme_roshnilite'));

$name = 'theme_roshnilite/fontselect';
$title = get_string('fontselect', 'theme_roshnilite');
$description = get_string('fontselectdesc', 'theme_roshnilite');
$default = 1;
$choices = [
        1 => get_string('fonttypestandard', 'theme_roshnilite'),
        2 => get_string('fonttypecustom', 'theme_roshnilite'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_roshnilite/fontsize';
$title = get_string('fontsize', 'theme_roshnilite');
$description = get_string('fontsize_desc', 'theme_roshnilite');
$default = '15';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Heading font name.
$name = 'theme_roshnilite/fontnameheading';
$title = get_string('fontnameheading', 'theme_roshnilite');
$description = get_string('fontnameheadingdesc', 'theme_roshnilite');
$default = get_string('fontnamedefault', 'theme_roshnilite');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Text font name.
$name = 'theme_roshnilite/fontnamebody';
$title = get_string('fontnamebody', 'theme_roshnilite');
$description = get_string('fontnamebodydesc', 'theme_roshnilite');
$default = get_string('fontnamedefault', 'theme_roshnilite');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
if (get_config('theme_roshnilite', 'fontselect') === "2") {
    if (floatval($CFG->version) >= 2014111005.01) {
        $woff2 = true;
    } else {
        $woff2 = false;
    }
    // This is the descriptor for the font files.
    $name = 'theme_roshnilite/fontfiles';
    $heading = get_string('fontfiles', 'theme_roshnilite');
    $information = get_string('fontfilesdesc', 'theme_roshnilite');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);
    // Heading Fonts.
    // TTF Font.
    $name = 'theme_roshnilite/fontfilettfheading';
    $title = get_string('fontfilettfheading', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfheading');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // OTF Font.
    $name = 'theme_roshnilite/fontfileotfheading';
    $title = get_string('fontfileotfheading', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfheading');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // WOFF Font.
    $name = 'theme_roshnilite/fontfilewoffheading';
    $title = get_string('fontfilewoffheading', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffheading');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    if ($woff2) {
            // WOFF2 Font.
            $name = 'theme_roshnilite/fontfilewofftwoheading';
            $title = get_string('fontfilewofftwoheading', 'theme_roshnilite');
            $description = '';
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwoheading');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
    }

    // EOT Font.
    $name = 'theme_roshnilite/fontfileeotheading';
    $title = get_string('fontfileeotheading', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileweotheading');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // SVG Font.
    $name = 'theme_roshnilite/fontfilesvgheading';
    $title = get_string('fontfilesvgheading', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgheading');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Body fonts.
    // TTF Font.
    $name = 'theme_roshnilite/fontfilettfbody';
    $title = get_string('fontfilettfbody', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfbody');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // OTF Font.
    $name = 'theme_roshnilite/fontfileotfbody';
    $title = get_string('fontfileotfbody', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfbody');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // WOFF Font.
    $name = 'theme_roshnilite/fontfilewoffbody';
    $title = get_string('fontfilewoffbody', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffbody');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    if ($woff2) {
        // WOFF2 Font.
        $name = 'theme_roshnilite/fontfilewofftwobody';
        $title = get_string('fontfilewofftwobody', 'theme_roshnilite');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwobody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    // EOT Font.
    $name = 'theme_roshnilite/fontfileeotbody';
    $title = get_string('fontfileeotbody', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileweotbody');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // SVG Font.
    $name = 'theme_roshnilite/fontfilesvgbody';
    $title = get_string('fontfilesvgbody', 'theme_roshnilite');
    $description = '';
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgbody');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
// Custom CSS file.
$name = 'theme_roshnilite/customcss';
$title = get_string('customcss', 'theme_roshnilite');
$description = get_string('customcssdesc', 'theme_roshnilite');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);
