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
 * Site home section settings for theme_roshnilite: how it works, events, testimonials and questions.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_roshnilite_sections', get_string('sectionssettings', 'theme_roshnilite'));

// How it works.
$page->add(new admin_setting_heading(
    'theme_roshnilite/howitworksheading',
    get_string('howitworksheading', 'theme_roshnilite'),
    ''
));
$setting = new admin_setting_configcheckbox(
    'theme_roshnilite/showhowitworks',
    get_string('showhowitworks', 'theme_roshnilite'),
    get_string('showhowitworksdesc', 'theme_roshnilite'),
    1
);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
for ($i = 1; $i <= \theme_roshnilite\output\frontpage::STEPS; $i++) {
    $setting = new admin_setting_configtext(
        'theme_roshnilite/steptitle' . $i,
        get_string('steptitle', 'theme_roshnilite', $i),
        '',
        get_string('steptitle' . $i . 'default', 'theme_roshnilite')
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $setting = new admin_setting_configtextarea(
        'theme_roshnilite/steptext' . $i,
        get_string('steptext', 'theme_roshnilite', $i),
        '',
        get_string('steptext' . $i . 'default', 'theme_roshnilite')
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

// Upcoming events.
$page->add(new admin_setting_heading(
    'theme_roshnilite/eventsheading',
    get_string('eventsheading', 'theme_roshnilite'),
    ''
));
$setting = new admin_setting_configcheckbox(
    'theme_roshnilite/showevents',
    get_string('showevents', 'theme_roshnilite'),
    get_string('showeventsdesc', 'theme_roshnilite'),
    1
);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Testimonials.
$page->add(new admin_setting_heading(
    'theme_roshnilite/testimonialsheading',
    get_string('testimonialsheading', 'theme_roshnilite'),
    get_string('testimonialsdesc', 'theme_roshnilite')
));
for ($i = 1; $i <= \theme_roshnilite\output\frontpage::TESTIMONIALS; $i++) {
    $setting = new admin_setting_configtextarea(
        'theme_roshnilite/testimonialquote' . $i,
        get_string('testimonialquote', 'theme_roshnilite', $i),
        '',
        ''
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $setting = new admin_setting_configtext(
        'theme_roshnilite/testimonialname' . $i,
        get_string('testimonialname', 'theme_roshnilite', $i),
        '',
        ''
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $setting = new admin_setting_configtext(
        'theme_roshnilite/testimonialrole' . $i,
        get_string('testimonialrole', 'theme_roshnilite', $i),
        '',
        ''
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

// Questions and answers.
$page->add(new admin_setting_heading(
    'theme_roshnilite/faqheading',
    get_string('faqheading', 'theme_roshnilite'),
    ''
));
$setting = new admin_setting_configcheckbox(
    'theme_roshnilite/showfaq',
    get_string('showfaq', 'theme_roshnilite'),
    get_string('showfaqdesc', 'theme_roshnilite'),
    1
);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
for ($i = 1; $i <= \theme_roshnilite\output\frontpage::QUESTIONS; $i++) {
    $setting = new admin_setting_configtext(
        'theme_roshnilite/faqquestion' . $i,
        get_string('faqquestion', 'theme_roshnilite', $i),
        '',
        get_string('faqquestion' . $i . 'default', 'theme_roshnilite')
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $setting = new admin_setting_confightmleditor(
        'theme_roshnilite/faqanswer' . $i,
        get_string('faqanswer', 'theme_roshnilite', $i),
        '',
        get_string('faqanswer' . $i . 'default', 'theme_roshnilite')
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

$settings->add($page);
