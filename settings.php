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
 * roshnilite theme settings file.
 *
 * @package    theme_roshnilite
 * @copyright  2020 DualCube
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings = new theme_boost_admin_settingspage_tabs('themesettingroshnilite',
        get_string('configtitle', 'theme_roshnilite'));
    $page = new admin_settingpage('theme_roshnilite_general', get_string('generalsettings', 'theme_roshnilite'));

    // Preset.
    $name = 'theme_roshnilite/preset';
    $title = get_string('preset', 'theme_roshnilite');
    $description = get_string('preset_desc', 'theme_roshnilite');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_roshnilite', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }

    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_roshnilite/presetfiles';
    $title = get_string('presetfiles', 'theme_roshnilite');
    $description = get_string('presetfiles_desc', 'theme_roshnilite');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_roshnilite/backgroundimage';
    $title = get_string('backgroundimage', 'theme_roshnilite');
    $description = get_string('backgroundimage_desc', 'theme_roshnilite');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_roshnilite/brandcolor';
    $title = get_string('brandcolor', 'theme_roshnilite');
    $description = get_string('brandcolor_desc', 'theme_roshnilite');
    $default = '#e74c3c';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/favicon';
    $title = get_string('favicon', 'theme_roshnilite');
    $description = get_string('favicondesc', 'theme_roshnilite');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/logo';
    $title = get_string('logo', 'theme_roshnilite');
    $description = get_string('logodesc', 'theme_roshnilite');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/moodlemaincontentinfrontpage';
    $title = get_string('moodlemaincontentinfrontpage', 'theme_roshnilite');
    $description = get_string('moodlemaincontentinfrontpagedesc', 'theme_roshnilite');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/slidercount';
    $title = get_string('slidercount', 'theme_roshnilite');
    $description = get_string('slidercountdesc', 'theme_roshnilite');
    $setting = new admin_setting_configselect($name, $title, $description, 0,
    array(
            1 => get_string('one', 'theme_roshnilite'),
            2 => get_string('two', 'theme_roshnilite'),
            3 => get_string('three', 'theme_roshnilite'),
            4 => get_string('four', 'theme_roshnilite'),
            5 => get_string('five', 'theme_roshnilite'),
            6 => get_string('six', 'theme_roshnilite'),
        ));
    $page->add($setting);

    for ($slidecounts = 1; $slidecounts <= get_config('theme_roshnilite', 'slidercount'); $slidecounts = $slidecounts + 1) {
        $name = 'theme_roshnilite/slideimage'.$slidecounts;
        $title = get_string('slideimage', 'theme_roshnilite').$slidecounts;
        $description = get_string('slideimagedesc', 'theme_roshnilite').$slidecounts;
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'slideimage'.$slidecounts);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/slidertext'.$slidecounts;
        $title = get_string('slidertext', 'theme_roshnilite').$slidecounts;
        $description = get_string('slidertextdesc', 'theme_roshnilite').$slidecounts;
        $default = get_string('slidertextdefault', 'theme_roshnilite');
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/sliderbuttontext'.$slidecounts;
        $title = get_string('sliderbuttontext', 'theme_roshnilite').$slidecounts;
        $description = get_string('sliderbuttontextdesc', 'theme_roshnilite');
        $default = get_string('sliderbuttontextdefault', 'theme_roshnilite');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/sliderurl'.$slidecounts;
        $title = get_string('sliderurl', 'theme_roshnilite').$slidecounts;
        $description = get_string('sliderurldesc', 'theme_roshnilite').$slidecounts;
        $default = get_string('sliderurldefault', 'theme_roshnilite');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $name = 'theme_roshnilite/aboutsiteheading';
    $title = get_string('aboutsiteheading', 'theme_roshnilite');
    $description = get_string('aboutsiteheadingdesc', 'theme_roshnilite');
    $default = get_string('aboutsiteheadingdefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitesubheading';
    $title = get_string('aboutsitesubheading', 'theme_roshnilite');
    $description = get_string('aboutsitesubheadingdesc', 'theme_roshnilite');
    $default = get_string('aboutsitesubheadingdefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsiteimage1';
    $title = get_string('aboutsiteimage', 'theme_roshnilite');
    $description = get_string('faboutsiteimagedesc', 'theme_roshnilite');;
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'aboutsiteimage1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitename1';
    $title = get_string('aboutsitename', 'theme_roshnilite');
    $description = get_string('faboutsitenamedesc', 'theme_roshnilite');
    $default = get_string('aboutsitename1default', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitetext1';
    $title = get_string('aboutsitetext', 'theme_roshnilite');
    $description = get_string('faboutsitetextdesc', 'theme_roshnilite');
    $default = get_string('aboutsitetext1default', 'theme_roshnilite');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsiteurl1';
    $title = get_string('aboutsiteurl', 'theme_roshnilite');
    $description = get_string('faboutsiteurldesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsiteimage2';
    $title = get_string('aboutsiteimage', 'theme_roshnilite');
    $description = get_string('saboutsiteimagedesc', 'theme_roshnilite');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'aboutsiteimage2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitename2';
    $title = get_string('aboutsitename', 'theme_roshnilite');
    $description = get_string('saboutsitenamedesc', 'theme_roshnilite');
    $default = get_string('aboutsitename2default', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitetext2';
    $title = get_string('aboutsitetext', 'theme_roshnilite');
    $description = get_string('saboutsitetextdesc', 'theme_roshnilite');
    $default = get_string('aboutsitetext2default', 'theme_roshnilite');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsiteurl2';
    $title = get_string('aboutsiteurl', 'theme_roshnilite');
    $description = get_string('saboutsiteurldesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    /*-------------*/

    $name = 'theme_roshnilite/aboutsiteimage3';
    $title = get_string('aboutsiteimage', 'theme_roshnilite');
    $description = get_string('taboutsiteimagedesc', 'theme_roshnilite');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'aboutsiteimage3');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitename3';
    $title = get_string('aboutsitename', 'theme_roshnilite');
    $description = get_string('taboutsitenamedesc', 'theme_roshnilite');
    $default = get_string('aboutsitename3default', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitetext3';
    $title = get_string('aboutsitetext', 'theme_roshnilite');
    $description = get_string('taboutsitetextdesc', 'theme_roshnilite');
    $default = get_string('aboutsitetext3default', 'theme_roshnilite');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsiteurl3';
    $title = get_string('aboutsiteurl', 'theme_roshnilite');
    $description = get_string('taboutsiteurldesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsiteimage4';
    $title = get_string('aboutsiteimage', 'theme_roshnilite');
    $description = get_string('fraboutsiteimagedesc', 'theme_roshnilite');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'aboutsiteimage4');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitename4';
    $title = get_string('aboutsitename', 'theme_roshnilite');
    $description = get_string('fraboutsitenamedesc', 'theme_roshnilite');
    $default = get_string('aboutsitename4default', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsitetext4';
    $title = get_string('aboutsitetext', 'theme_roshnilite');
    $description = get_string('fraboutsitetextdesc', 'theme_roshnilite');
    $default = get_string('fraboutsitetextdescdefault', 'theme_roshnilite');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/aboutsiteurl4';
    $title = get_string('aboutsiteurl', 'theme_roshnilite');
    $description = get_string('fraboutsiteurldesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/maincolor';
    $title = get_string('maincolor', 'theme_roshnilite');
    $description = get_string('maincolordesc', 'theme_roshnilite');
    $default = '#e74c3c';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_roshnilite/masonryheading';
    $title = get_string('masonryheading', 'theme_roshnilite');
    $description = get_string('masonryheadingdesc', 'theme_roshnilite');
    $default = get_string('masonryheadingdefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/masonrysubheading';
    $title = get_string('masonrysubheading', 'theme_roshnilite');
    $description = get_string('masonrysubheadingdesc', 'theme_roshnilite');
    $default = get_string('masonrysubheadingdefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/addressfontawesomeicon';
    $title = get_string('addressfontawesomeicon', 'theme_roshnilite');
    $description = get_string('addressfontawesomeicondesc', 'theme_roshnilite');
    $default = '<i class="fa fa-map-marker"></i>';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/address';
    $title = get_string('address', 'theme_roshnilite');
    $description = get_string('addressdesc', 'theme_roshnilite');
    $default = get_string('addressdefault', 'theme_roshnilite');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/emailfontawesomeicon';
    $title = get_string('emailfontawesomeicon', 'theme_roshnilite');
    $description = get_string('emailfontawesomeicondesc', 'theme_roshnilite');
    $default = '<i class="fa fa-envelope"></i>';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/email';
    $title = get_string('email', 'theme_roshnilite');
    $description = get_string('emaildesc', 'theme_roshnilite');
    $default = get_string('emaildefault', 'theme_roshnilite');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $name = 'theme_roshnilite/phonefontawesomeicon';
    $title = get_string('phonefontawesomeicon', 'theme_roshnilite');
    $description = get_string('phonefontawesomeicondesc', 'theme_roshnilite');
    $default = '<i class="fa fa-phone"></i>';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/phone';
    $title = get_string('phone', 'theme_roshnilite');
    $description = get_string('phonedesc', 'theme_roshnilite');
    $default = get_string('phonedefault', 'theme_roshnilite');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialheading';
    $title = get_string('socialheading', 'theme_roshnilite');
    $description = get_string('socialheadingdesc', 'theme_roshnilite');
    $default = get_string('socialheadingdefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialfontawesomeicon1';
    $title = get_string('socialfontawesomeicon1', 'theme_roshnilite');
    $description = get_string('socialfontawesomeicondesc1', 'theme_roshnilite');
    $default = '<i class="fa fa-facebook"></i>';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialicon1';
    $title = get_string('socialicon', 'theme_roshnilite');
    $description = get_string('socialicondesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialfontawesomeicon2';
    $title = get_string('socialfontawesomeicon2', 'theme_roshnilite');
    $description = get_string('socialfontawesomeicondesc2', 'theme_roshnilite');
    $default = '<i class="fa fa-twitter"></i>';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialicon2';
    $title = get_string('socialicon', 'theme_roshnilite');
    $description = get_string('socialicondesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialfontawesomeicon3';
    $title = get_string('socialfontawesomeicon3', 'theme_roshnilite');
    $description = get_string('socialfontawesomeicondesc3', 'theme_roshnilite');
    $default = '<i class="fa fa-linkedin"></i>';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialicon3';
    $title = get_string('socialicon', 'theme_roshnilite');
    $description = get_string('socialicondesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialfontawesomeicon4';
    $title = get_string('socialfontawesomeicon4', 'theme_roshnilite');
    $description = get_string('socialfontawesomeicondesc4', 'theme_roshnilite');
    $default = '<i class="fa fa-google-plus"></i>';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_roshnilite/socialicon4';
    $title = get_string('socialicon', 'theme_roshnilite');
    $description = get_string('socialicondesc', 'theme_roshnilite');
    $default = get_string('sliderurldefault', 'theme_roshnilite');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_roshnilite_advanced', get_string('advancedsettings', 'theme_roshnilite'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_roshnilite/scsspre',
        get_string('rawscsspre', 'theme_roshnilite'), get_string('rawscsspre_desc', 'theme_roshnilite'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_roshnilite/scss', get_string('rawscss', 'theme_roshnilite'),
        get_string('rawscss_desc', 'theme_roshnilite'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Font settings.
    $page = new admin_settingpage('theme_roshnilite_font',  get_string('fontsettings', 'theme_roshnilite'));

    $name = 'theme_roshnilite/fontselect';
    $title = get_string('fontselect', 'theme_roshnilite');
    $description = get_string('fontselectdesc', 'theme_roshnilite');
    $default = 1;
    $choices = array(
            1 => get_string('fonttypestandard', 'theme_roshnilite'),
            2 => get_string('fonttypecustom', 'theme_roshnilite'),
    );
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

    $page = new admin_settingpage('theme_roshnilite_faculty',  get_string('facultysettings', 'theme_roshnilite'));

    $name = 'theme_roshnilite/facultycount';
    $title = get_string('facultycount', 'theme_roshnilite');
    $description = get_string('facultycountdesc', 'theme_roshnilite');
    $setting = new admin_setting_configselect($name, $title, $description, 0,
    array(
            1 => get_string('one', 'theme_roshnilite'),
            2 => get_string('two', 'theme_roshnilite'),
            3 => get_string('three', 'theme_roshnilite'),
            4 => get_string('four', 'theme_roshnilite'),
            5 => get_string('five', 'theme_roshnilite'),
            6 => get_string('six', 'theme_roshnilite'),
            7 => get_string('seven', 'theme_roshnilite'),
            8 => get_string('eight', 'theme_roshnilite'),
        ));
    $page->add($setting);

    for ($facultycounts = 1; $facultycounts <= get_config('theme_roshnilite', 'facultycount'); $facultycounts++) {

        $name = 'theme_roshnilite/facultyimage'.$facultycounts;
        $title = get_string('facultyimage', 'theme_roshnilite').$facultycounts;
        $description = get_string('facultyimagedesc', 'theme_roshnilite').$facultycounts;
        $default = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'facultyimage'.$facultycounts);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);


        $name = 'theme_roshnilite/facultyname'.$facultycounts;
        $title = get_string('facultyname', 'theme_roshnilite').$facultycounts;
        $description = get_string('facultynamedesc', 'theme_roshnilite').$facultycounts;
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/facultysubtext'.$facultycounts;
        $title = get_string('facultysubtext', 'theme_roshnilite').$facultycounts;
        $description = get_string('facultysubtextdesc', 'theme_roshnilite').$facultycounts;
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/facultyfburl'.$facultycounts;
        $title = get_string('facultyfburl', 'theme_roshnilite').$facultycounts;
        $description = get_string('facultyfburldesc', 'theme_roshnilite').$facultycounts;
        $default = get_string('sliderurldefault', 'theme_roshnilite');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/facultylnkdnurl'.$facultycounts;
        $title = get_string('facultylnkdnurl', 'theme_roshnilite').$facultycounts;
        $description = get_string('facultylnkdnurldesc', 'theme_roshnilite').$facultycounts;
        $default = get_string('sliderurldefault', 'theme_roshnilite');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/facultygoogleurl'.$facultycounts;
        $title = get_string('facultygoogleurl', 'theme_roshnilite').$facultycounts;
        $description = get_string('facultygoogleurldesc', 'theme_roshnilite').$facultycounts;
        $default = get_string('sliderurldefault', 'theme_roshnilite');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_roshnilite/facultytwitterurl'.$facultycounts;
        $title = get_string('facultytwitterurl', 'theme_roshnilite').$facultycounts;
        $description = get_string('facultytwitterurldesc', 'theme_roshnilite').$facultycounts;
        $default = get_string('sliderurldefault', 'theme_roshnilite');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);
}
