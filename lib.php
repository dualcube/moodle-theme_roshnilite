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
 * theme_roshnilite lib file.
 * @package    theme_roshnilite
 * @copyright  2020 DualCube {@link https://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.
// Function to return the SCSS to prepend to our main SCSS for this theme.
// Note the function name starts with the component name because this is a global function and we don't want namespace clashes.
/**
 * theme_roshnilite_get_pre_scss function for load custom settings.
 *
 * @param string $theme
 * @return $theme->settings->$setting
 */
function theme_roshnilite_get_pre_scss($theme) {
    global $CFG;

    $scss = '';
    $configurable = [
        'brandcolor' => ['secondary', 'black']
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    if (!empty($theme->settings->fontsize)) {
        $scss .= '$font-size-base: ' . (1 / 100 * $theme->settings->fontsize) . "rem !default;\n";
    }

    return $scss;
}
// Function to return the SCSS to append to our main SCSS for this theme.
// Note the function name starts with the component name because this is a global function and we don't want namespace clashes.
/**
 * theme_roshnilite_get_extra_scss function for load custom settings.
 *
 * @param string $theme
 * @return $theme->settings->$setting
 */
function theme_roshnilite_get_extra_scss($theme) {
    global $CFG;
    $content = '';

    // Set the page background image.
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');
    if (!empty($imageurl)) {
        $content .= '$imageurl: "' . $imageurl . '";';
        $content .= file_get_contents($CFG->dirroot .
            '/theme/roshnilite/scss/roshnilite/body-background.scss');
    }

    if (!empty($theme->settings->navbardark)) {
        $content .= file_get_contents($CFG->dirroot .
            '/theme/roshnilite/scss/roshnilite/navbar-dark.scss');
    } else {
        $content .= file_get_contents($CFG->dirroot .
            '/theme/roshnilite/scss/roshnilite/navbar-light.scss');
    }
    if (!empty($theme->settings->scss)) {
        $content .= $theme->settings->scss;
    }
    return $content;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_roshnilite_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/roshnilite/style/moodle.css');
}

/**
 * theme_roshnilite_get_main_scss_content function for load custom theme css settings.
 *
 * @param string $theme
 * @return string $pre,$scss and $post
 */
function theme_roshnilite_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/roshnilite/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/roshnilite/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_roshnilite', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/roshnilite/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/roshnilite/scss/roshnilite/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/roshnilite/scss/roshnilite/post.scss');

    return $pre . "\n" . $scss . "\n" . $post;

}

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_roshnilite_process_css($css, $theme) {

    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = theme_roshnilite_set_logo($css, $logo);
    if (!empty($theme->settings->fontnamebody)) {
        $font = $theme->settings->fontnamebody;
    } else {
        $font = 'Raleway';
    }
    $headingfont = theme_roshnilite_get_setting('fontnameheading');
    $bodyfont = theme_roshnilite_get_setting('fontnamebody');

    $css = theme_roshnilite_set_headingfont($css, $headingfont);
    $css = theme_roshnilite_set_bodyfont($css, $bodyfont);
    $css = theme_roshnilite_set_fontfiles($css, 'heading', $headingfont);
    $css = theme_roshnilite_set_fontfiles($css, 'body', $bodyfont);
    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_roshnilite_set_customcss($css, $customcss);
    $thememaincolor = theme_roshnilite_get_setting('maincolor');
    $css = theme_roshnilite_set_maincolor($css, $thememaincolor);
    $themebrandcolor = theme_roshnilite_get_setting('brandcolor');
    $css = theme_roshnilite_set_brandcolor($css, $themebrandcolor);
    $themefontsize = theme_roshnilite_get_setting('fontsize');
    $css = theme_roshnilite_set_fontsize($css, $themefontsize);
    return $css;
}

function theme_roshnilite_set_fontsize($css, $themefontsize) {
    $tag = '[[setting:fontsize]]';
    $replacement = $themefontsize;
	if (is_null($replacement)) {
        $replacement = '15';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Adds the logo to CSS.
 *
 * @param string $css The CSS.
 * @param string $logo The URL of the logo.
 * @return string The parsed CSS
 */
function theme_roshnilite_set_logo($css, $logo) {
    GLOBAL $CFG;
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = $CFG->wwwroot.'/theme/roshnilite/style/img/logo.png';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}
/**
 * theme_roshnilite_get_setting function for load custom settings.
 *
 * @param string $setting
 * @param string $format
 * @return $theme->settings->$setting
 */
function theme_roshnilite_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('roshnilite');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}
/**
 * Adds any custom headingfont to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $headingfont The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_roshnilite_set_headingfont($css, $headingfont) {
    $tag = '[[setting:headingfont]]';
    $replacement = $headingfont;
    $css = str_replace($tag, $replacement, $css);
    return $css;
}
/**
 * Adds any custom bodyfont to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $bodyfont The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_roshnilite_set_bodyfont($css, $bodyfont) {
    $tag = '[[setting:bodyfont]]';
    $replacement = $bodyfont;
    $css = str_replace($tag, $replacement, $css);
    return $css;
}
/**
 * Adds the font to CSS.
 *
 * @param string $css The CSS.
 * @param string $type The font file type.
 * @param string $fontname The font name.
 * @return string The parsed CSS
 */
function theme_roshnilite_set_fontfiles($css, $type, $fontname) {
    $tag = '[[setting:fontname]]';
    $replacement = $fontname;
    if (is_null($replacement)) {
        $replacement = '';
    }
    if (theme_roshnilite_get_setting('fontselect') === '2') {
        static $theme;
        if (empty($theme)) {
            $theme = theme_config::load('roshnilite');
        }

        $fontfiles = array();
        $fontfileeot = $theme->setting_file_url('fontfileeot' . $type, 'fontfileeot' . $type);
        if (!empty($fontfileeot)) {
            $fontfiles[] = "url('" . $fontfileeot . "?#iefix') format('embedded-opentype')";
        }
        $fontfilewoff = $theme->setting_file_url('fontfilewoff' . $type, 'fontfilewoff' . $type);
        if (!empty($fontfilewoff)) {
            $fontfiles[] = "url('" . $fontfilewoff . "') format('woff')";
        }
        $fontfilewofftwo = $theme->setting_file_url('fontfilewofftwo' . $type, 'fontfilewofftwo' . $type);
        if (!empty($fontfilewofftwo)) {
            $fontfiles[] = "url('" . $fontfilewofftwo . "') format('woff2')";
        }
        $fontfileotf = $theme->setting_file_url('fontfileotf' . $type, 'fontfileotf' . $type);
        if (!empty($fontfileotf)) {
            $fontfiles[] = "url('" . $fontfileotf . "') format('opentype')";
        }
        $fontfilettf = $theme->setting_file_url('fontfilettf' . $type, 'fontfilettf' . $type);
        if (!empty($fontfilettf)) {
            $fontfiles[] = "url('" . $fontfilettf . "') format('truetype')";
        }
        $fontfilesvg = $theme->setting_file_url('fontfilesvg' . $type, 'fontfilesvg' . $type);
        if (!empty($fontfilesvg)) {
            $fontfiles[] = "url('" . $fontfilesvg . "') format('svg')";
        }

        $replacement = '@font-face {' . PHP_EOL . 'font-family: "' . $fontname . '";' . PHP_EOL;
        $replacement .= !empty($fontfileeot) ? "src: url('" . $fontfileeot . "');" . PHP_EOL : '';
        if (!empty($fontfiles)) {
            $replacement .= "src: ";
            $replacement .= implode("," . PHP_EOL . " ", $fontfiles);
            $replacement .= ";";
        }
        $replacement .= '' . PHP_EOL . "}";
    }

    $css = str_replace($tag, $replacement, $css);
    return $css;
}
/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_roshnilite_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}
/**
 * Adds any custom color to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $themecolor The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_roshnilite_set_maincolor($css, $themecolor) {
    $tag = '[[setting:maincolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#e74c3c';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Adds any custom color to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $themecolor The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_roshnilite_set_brandcolor($css, $themecolor) {
    $tag = '[[setting:brandcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#e74c3c';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}


/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add roshnilite specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_roshnilite_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG, $USER;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    if (!empty($page->theme->settings->logo)) {
        $return->heading = html_writer::tag('div', '', array('class' => 'logo'));
    } else {
        $return->heading = $output->page_heading();
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">'.format_text($page->theme->settings->footnote).'</div>';
    }
    /*-----------------------for contact*--------------------------*/
    if (!empty($page->theme->settings->addressfontawesomeicon)) {
        $return->addressfontawesomeicon = $page->theme->settings->addressfontawesomeicon;
    }
    if (!empty($page->theme->settings->emailfontawesomeicon)) {
        $return->emailfontawesomeicon = $page->theme->settings->emailfontawesomeicon;
    }
    if (!empty($page->theme->settings->phonefontawesomeicon)) {
        $return->phonefontawesomeicon = $page->theme->settings->phonefontawesomeicon;
    }
    if (!empty($page->theme->settings->address)) {
        $return->address = $page->theme->settings->address;
    }
    if (!empty($page->theme->settings->phone)) {
        $return->phone = $page->theme->settings->phone;
    }
    if (!empty($page->theme->settings->email)) {
        $return->email = $page->theme->settings->email;
    }
    /*-----------------------for social contact*--------------------------*/
    if (!empty($page->theme->settings->socialheading)) {
        $return->socialheading = $page->theme->settings->socialheading;
    }
    if (!empty($page->theme->settings->masonrysubheading)) {
        $return->masonrysubheading = $page->theme->settings->masonrysubheading;
    }
    if (!empty($page->theme->settings->socialfontawesomeicon1)) {
        $return->socialfontawesomeicon1 = $page->theme->settings->socialfontawesomeicon1;
    }
    if (!empty($page->theme->settings->socialicon1)) {
        $return->socialicon1 = $page->theme->settings->socialicon1;
    }
    if (!empty($page->theme->settings->socialfontawesomeicon2)) {
        $return->socialfontawesomeicon2 = $page->theme->settings->socialfontawesomeicon2;
    }
    if (!empty($page->theme->settings->socialicon2)) {
        $return->socialicon2 = $page->theme->settings->socialicon2;
    }
    if (!empty($page->theme->settings->socialfontawesomeicon3)) {
        $return->socialfontawesomeicon3 = $page->theme->settings->socialfontawesomeicon3;
    }
    if (!empty($page->theme->settings->socialicon3)) {
        $return->socialicon3 = $page->theme->settings->socialicon3;
    }
    if (!empty($page->theme->settings->socialfontawesomeicon4)) {
        $return->socialfontawesomeicon4 = $page->theme->settings->socialfontawesomeicon4;
    }
    if (!empty($page->theme->settings->socialicon4)) {
        $return->socialicon4 = $page->theme->settings->socialicon4;
    }
    /*--------------------for masonry--------------------------------*/
    if (!empty($page->theme->settings->masonryheading)) {
        $return->masonryheading = $page->theme->settings->masonryheading;
    }
    if (!empty($page->theme->settings->masonrysubheading)) {
        $return->masonrysubheading = $page->theme->settings->masonrysubheading;
    }
    if (!empty($page->theme->settings->masonrytext1)) {
        $return->masonrytext1 = $page->theme->settings->masonrytext1;
    }
    if (!empty($page->theme->settings->masonrysubtext1)) {
        $return->masonrysubtext1 = $page->theme->settings->masonrysubtext1;
    }
    if (!empty($page->theme->settings->masonryrurl1)) {
        $return->masonryurl1 = $page->theme->settings->masonryrurl1;
    }

    if (!empty($page->theme->settings->masonrytext2)) {
        $return->masonrytext2 = $page->theme->settings->masonrytext2;
    }
    if (!empty($page->theme->settings->masonrysubtext2)) {
        $return->masonrysubtext2 = $page->theme->settings->masonrysubtext2;
    }
    if (!empty($page->theme->settings->masonryrurl2)) {
        $return->masonryurl2 = $page->theme->settings->masonryrurl2;
    }

    if (!empty($page->theme->settings->masonrytext3)) {
        $return->masonrytext3 = $page->theme->settings->masonrytext3;
    }
    if (!empty($page->theme->settings->masonrysubtext3)) {
        $return->masonrysubtext3 = $page->theme->settings->masonrysubtext3;
    }
    if (!empty($page->theme->settings->masonryrurl3)) {
        $return->masonryurl3 = $page->theme->settings->masonryrurl3;
    }

    if (!empty($page->theme->settings->masonrytext4)) {
        $return->masonrytext4 = $page->theme->settings->masonrytext4;
    }
    if (!empty($page->theme->settings->masonrysubtext4)) {
        $return->masonrysubtext4 = $page->theme->settings->masonrysubtext4;
    }
    if (!empty($page->theme->settings->masonryrurl4)) {
        $return->masonryurl4 = $page->theme->settings->masonryrurl4;
    }

    if (!empty($page->theme->settings->masonrytext5)) {
        $return->masonrytext5 = $page->theme->settings->masonrytext5;
    }
    if (!empty($page->theme->settings->masonrysubtext5)) {
        $return->masonrysubtext5 = $page->theme->settings->masonrysubtext5;
    }
    if (!empty($page->theme->settings->masonryrurl5)) {
        $return->masonryurl5 = $page->theme->settings->masonryrurl5;
    }

    if (!empty($page->theme->settings->masonrytext6)) {
        $return->masonrytext6 = $page->theme->settings->masonrytext6;
    }
    if (!empty($page->theme->settings->masonrysubtext6)) {
        $return->masonrysubtext6 = $page->theme->settings->masonrysubtext6;
    }
    if (!empty($page->theme->settings->masonryrurl6)) {
        $return->masonryurl6 = $page->theme->settings->masonryrurl6;
    }

    if (!empty($page->theme->settings->masonrytext7)) {
        $return->masonrytext7 = $page->theme->settings->masonrytext7;
    }
    if (!empty($page->theme->settings->masonrysubtext7)) {
        $return->masonrysubtext7 = $page->theme->settings->masonrysubtext7;
    }
    if (!empty($page->theme->settings->masonryrurl7)) {
        $return->masonryurl7 = $page->theme->settings->masonryrurl7;
    }

    if (!empty($page->theme->settings->masonrytext8)) {
        $return->masonrytext8 = $page->theme->settings->masonrytext8;
    }
    if (!empty($page->theme->settings->masonrysubtext8)) {
        $return->masonrysubtext8 = $page->theme->settings->masonrysubtext8;
    }
    if (!empty($page->theme->settings->masonryrurl8)) {
        $return->masonryurl8 = $page->theme->settings->masonryrurl8;
    }

    /*-------------------------for first slider-----------------------------*/
    if (!empty($page->theme->settings->slidertext1)) {
        $return->slidertext1 = $page->theme->settings->slidertext1;
    }
    if (!empty($page->theme->settings->sliderbuttontext1)) {
        $return->sliderbuttontext1 = $page->theme->settings->sliderbuttontext1;
    }
    if (!empty($page->theme->settings->sliderurl1)) {
        $return->sliderurl1 = $page->theme->settings->sliderurl1;
    }

    if (!empty($page->theme->settings->slidertext2)) {
        $return->slidertext2 = $page->theme->settings->slidertext2;
    }
    if (!empty($page->theme->settings->sliderbuttontext2)) {
        $return->sliderbuttontext2 = $page->theme->settings->sliderbuttontext2;
    }
    if (!empty($page->theme->settings->sliderurl2)) {
        $return->sliderurl2 = $page->theme->settings->sliderurl2;
    }

    if (!empty($page->theme->settings->slidertext3)) {
        $return->slidertext3 = $page->theme->settings->slidertext3;
    }
    if (!empty($page->theme->settings->sliderbuttontext3)) {
        $return->sliderbuttontext3 = $page->theme->settings->sliderbuttontext3;
    }
    if (!empty($page->theme->settings->sliderurl3)) {
        $return->sliderurl3 = $page->theme->settings->sliderurl3;
    }

    if (!empty($page->theme->settings->slidertext4)) {
        $return->slidertext4 = $page->theme->settings->slidertext4;
    }
    if (!empty($page->theme->settings->sliderbuttontext4)) {
        $return->sliderbuttontext4 = $page->theme->settings->sliderbuttontext4;
    }
    if (!empty($page->theme->settings->sliderurl4)) {
        $return->sliderurl4 = $page->theme->settings->sliderurl4;
    }

    if (!empty($page->theme->settings->slidertext5)) {
        $return->slidertext5 = $page->theme->settings->slidertext5;
    }
    if (!empty($page->theme->settings->sliderbuttontext5)) {
        $return->sliderbuttontext5 = $page->theme->settings->sliderbuttontext5;
    }
    if (!empty($page->theme->settings->sliderurl5)) {
        $return->sliderurl5 = $page->theme->settings->sliderurl5;
    }
    if (!empty($page->theme->settings->slidertext6)) {
        $return->slidertext6 = $page->theme->settings->slidertext6;
    }
    if (!empty($page->theme->settings->sliderbuttontext6)) {
        $return->sliderbuttontext6 = $page->theme->settings->sliderbuttontext6;
    }
    if (!empty($page->theme->settings->sliderurl6)) {
        $return->sliderurl6 = $page->theme->settings->sliderurl6;
    }
    /*--------------------------about site---------------------------------*/
    if (!empty($page->theme->settings->aboutsiteheading)) {
        $return->aboutsiteheading = $page->theme->settings->aboutsiteheading;
    }
    if (!empty($page->theme->settings->aboutsitesubheading)) {
        $return->aboutsitesubheading = $page->theme->settings->aboutsitesubheading;
    }

    if (!empty($page->theme->settings->aboutsitename1)) {
        $return->aboutsitename1 = $page->theme->settings->aboutsitename1;
    }
    if (!empty($page->theme->settings->aboutsitetext1)) {
        $return->aboutsitetext1 = $page->theme->settings->aboutsitetext1;
    }
    if (!empty($page->theme->settings->aboutsiteurl1)) {
        $return->aboutsiteurl1 = $page->theme->settings->aboutsiteurl1;
    } else {
        $return->aboutsiteurl1 = $CFG->wwwroot.'/mod/forum/user.php?id='.$USER->id;
    }

    if (!empty($page->theme->settings->aboutsitename2)) {
        $return->aboutsitename2 = $page->theme->settings->aboutsitename2;
    }
    if (!empty($page->theme->settings->aboutsitetext2)) {
        $return->aboutsitetext2 = $page->theme->settings->aboutsitetext2;
    }
    if (!empty($page->theme->settings->aboutsiteurl2)) {
        $return->aboutsiteurl2 = $page->theme->settings->aboutsiteurl2;
    } else {
        $return->aboutsiteurl2 = $CFG->wwwroot.'/course/index.php';
    }

    if (!empty($page->theme->settings->aboutsitename3)) {
        $return->aboutsitename3 = $page->theme->settings->aboutsitename3;
    }
    if (!empty($page->theme->settings->aboutsitetext3)) {
        $return->aboutsitetext3 = $page->theme->settings->aboutsitetext3;
    }
    if (!empty($page->theme->settings->aboutsiteurl3)) {
        $return->aboutsiteurl3 = $page->theme->settings->aboutsiteurl3;
    } else {
        $return->aboutsiteurl3 = $CFG->wwwroot.'/blog/index.php?userid='.$USER->id;
    }

    if (!empty($page->theme->settings->aboutsitename4)) {
        $return->aboutsitename4 = $page->theme->settings->aboutsitename4;
    }
    if (!empty($page->theme->settings->aboutsitetext4)) {
        $return->aboutsitetext4 = $page->theme->settings->aboutsitetext4;
    }
    if (!empty($page->theme->settings->aboutsiteurl4)) {
        $return->aboutsiteurl4 = $page->theme->settings->aboutsiteurl4;
    } else {
        $return->aboutsiteurl4 = $CFG->wwwroot.'/calendar/view.php';
    }
    return $return;
}
/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_roshnilite_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('roshnilite');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'backgroundimage') {
            return $theme->setting_file_serve('backgroundimage', $args, $forcedownload, $options);
        } else if (preg_match("/^fontfile(eot|otf|svg|ttf|woff|woff2)(heading|body)$/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (preg_match("/^(marketing|slide)[1-9][0-9]*image$/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'favicon') {
            return $theme->setting_file_serve('favicon', $args, $forcedownload, $options);
        } else if ($filearea === 'slideimage1') {
            return $theme->setting_file_serve('slideimage1', $args, $forcedownload, $options);
        } else if ($filearea === 'slideimage2') {
            return $theme->setting_file_serve('slideimage2', $args, $forcedownload, $options);
        } else if ($filearea === 'slideimage3') {
            return $theme->setting_file_serve('slideimage3', $args, $forcedownload, $options);
        } else if ($filearea === 'slideimage4') {
            return $theme->setting_file_serve('slideimage4', $args, $forcedownload, $options);
        } else if ($filearea === 'slideimage5') {
            return $theme->setting_file_serve('slideimage5', $args, $forcedownload, $options);
        } else if ($filearea === 'slideimage6') {
            return $theme->setting_file_serve('slideimage6', $args, $forcedownload, $options);
        } else if ($filearea === 'aboutsiteimage1') {
            return $theme->setting_file_serve('aboutsiteimage1', $args, $forcedownload, $options);
        } else if ($filearea === 'aboutsiteimage2') {
            return $theme->setting_file_serve('aboutsiteimage2', $args, $forcedownload, $options);
        } else if ($filearea === 'aboutsiteimage3') {
            return $theme->setting_file_serve('aboutsiteimage3', $args, $forcedownload, $options);
        } else if ($filearea === 'aboutsiteimage4') {
            return $theme->setting_file_serve('aboutsiteimage4', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage1') {
            return $theme->setting_file_serve('facultyimage1', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage2') {
            return $theme->setting_file_serve('facultyimage2', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage3') {
            return $theme->setting_file_serve('facultyimage3', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage4') {
            return $theme->setting_file_serve('facultyimage4', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage5') {
            return $theme->setting_file_serve('facultyimage5', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage6') {
            return $theme->setting_file_serve('facultyimage6', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage7') {
            return $theme->setting_file_serve('facultyimage7', $args, $forcedownload, $options);
        } else if ($filearea === 'facultyimage8') {
            return $theme->setting_file_serve('facultyimage8', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}