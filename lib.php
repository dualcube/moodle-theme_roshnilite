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
 * Roshnilite theme.
 *
 * @package    theme_roshnilite
 * @author DualCube <admin@dualcube.com>
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the SCSS to prepend to the main SCSS: setting-driven variables and the raw pre-SCSS setting.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_roshnilite_get_pre_scss($theme) {
    $scss = '';

    // The brand colour (or, when unset, the main theme colour) drives Bootstrap's
    // primary colour, so Moodle's own buttons, links and focus rings match.
    $primary = ($theme->settings->brandcolor ?? '') ?: ($theme->settings->maincolor ?? '');
    if (preg_match('/^#[0-9a-f]{3,8}$/i', $primary)) {
        $scss .= '$primary: ' . $primary . ";\n";
    }

    // The font size setting is in pixels; Bootstrap's base size is in rem.
    $fontsize = (float) ($theme->settings->fontsize ?? 0);
    if ($fontsize >= 10 && $fontsize <= 30) {
        $scss .= '$font-size-base: ' . round($fontsize / 16, 4) . "rem !default;\n";
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * theme_roshnilite_get_extra_scss function for load custom settings.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_roshnilite_get_extra_scss($theme) {
    $content = '';

    // Set the page background image.
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');
    if (!empty($imageurl)) {
        $content .= '$imageurl: "' . $imageurl . '";';
        $content .= file_get_contents(__DIR__ . '/scss/roshnilite/body-background.scss');
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
    return file_get_contents(__DIR__ . '/style/moodle.css');
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

    // The built-in presets are Boost's own, so the SCSS always matches the
    // running Moodle release (Bootstrap 4 on 4.5, Bootstrap 5 and the design
    // system tokens on 5.x). Roshni Lite's look is layered on in pre/post.
    $boostpresets = $CFG->dirroot . '/theme/boost/scss/preset/';
    if ($filename === 'plain.scss') {
        $scss .= file_get_contents($boostpresets . 'plain.scss');
    } else if (
        $filename && $filename !== 'default.scss' &&
        ($presetfile = $fs->get_file($context->id, 'theme_roshnilite', 'preset', 0, '/', $filename))
    ) {
        $scss .= $presetfile->get_content();
    } else {
        $scss .= file_get_contents($boostpresets . 'default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents(__DIR__ . '/scss/roshnilite/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents(__DIR__ . '/scss/roshnilite/post.scss');

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
    $settings = $theme->settings;

    $css = str_replace('[[setting:fontrules]]', theme_roshnilite_get_font_css($theme), $css);

    $css = theme_roshnilite_set_customcss($css, $settings->customcss ?? null);
    $css = theme_roshnilite_set_maincolor($css, $settings->maincolor ?? null);
    $css = theme_roshnilite_set_brandcolor($css, $settings->brandcolor ?? null);
    $css = theme_roshnilite_set_fontsize($css, $settings->fontsize ?? null);
    return $css;
}

/**
 * Adds any custom font size to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $themefontsize The custom font size to add.
 * @return string The CSS which now contains our custom font size.
 */
function theme_roshnilite_set_fontsize($css, $themefontsize) {
    $tag = '[[setting:fontsize]]';
    $replacement = $themefontsize;
    if (empty($replacement)) {
        $replacement = '15';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * theme_roshnilite_get_setting function for load custom settings.
 *
 * @param string $setting
 * @param string $format
 * @return string|bool
 */
function theme_roshnilite_get_setting($setting, $format = false) {
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
        return format_text($theme->settings->$setting, FORMAT_HTML, ['trusted' => true, 'noclean' => true]);
    } else {
        return format_string($theme->settings->$setting);
    }
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
    if (empty($replacement)) {
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
    if (empty($replacement)) {
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
    if (empty($replacement)) {
        $replacement = '#e74c3c';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Builds the CSS for the font settings: @font-face rules for uploaded font
 * files, and font-family rules for the body and heading font names. Empty when
 * no font is configured, so Moodle's own font stack applies.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_roshnilite_get_font_css($theme) {
    $fallback = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    $selectors = [
        'body' => 'body',
        'heading' => 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .rl-section__title',
    ];
    $custom = ($theme->settings->fontselect ?? '') === '2';
    $formats = [
        'eot' => 'embedded-opentype',
        'woff' => 'woff',
        'wofftwo' => 'woff2',
        'otf' => 'opentype',
        'ttf' => 'truetype',
        'svg' => 'svg',
    ];

    $css = '';
    foreach ($selectors as $type => $selector) {
        // Font names end up inside CSS strings; keep only safe characters.
        $name = trim(preg_replace('/[^\p{L}\p{N} _-]/u', '', $theme->settings->{'fontname' . $type} ?? ''));
        if ($name === '') {
            continue;
        }
        if ($custom) {
            $sources = [];
            foreach ($formats as $suffix => $format) {
                $area = 'fontfile' . $suffix . $type;
                $url = $theme->setting_file_url($area, $area);
                if (!empty($url)) {
                    $sources[] = "url('{$url}') format('{$format}')";
                }
            }
            if ($sources) {
                $css .= '@font-face { font-family: "' . $name . '"; font-display: swap; src: ' .
                    implode(', ', $sources) . "; }\n";
            }
        }
        $css .= $selector . ' { font-family: "' . $name . '", ' . $fallback . "; }\n";
    }
    return $css;
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
function theme_roshnilite_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    $allowed = '/^(logo|favicon|backgroundimage|preset|slideimage[1-6]|aboutsiteimage[1-4]|facultyimage[1-8]|' .
        'fontfile(eot|otf|svg|ttf|woff|wofftwo)(heading|body))$/';
    if (!preg_match($allowed, $filearea)) {
        send_file_not_found();
    }

    $theme = theme_config::load('roshnilite');
    return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
}
