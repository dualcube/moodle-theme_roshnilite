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

namespace theme_roshnilite;

/**
 * Tests for the Roshni Lite library functions.
 *
 * @package    theme_roshnilite
 * @category   test
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::theme_roshnilite_process_css
 * @covers     ::theme_roshnilite_get_settings_css
 * @covers     ::theme_roshnilite_get_extra_scss
 * @covers     ::theme_roshnilite_get_font_css
 */
#[\PHPUnit\Framework\Attributes\CoversFunction('theme_roshnilite_process_css')]
#[\PHPUnit\Framework\Attributes\CoversFunction('theme_roshnilite_get_extra_scss')]
#[\PHPUnit\Framework\Attributes\CoversFunction('theme_roshnilite_get_font_css')]
final class lib_test extends \advanced_testcase {
    /**
     * Load the theme library.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        parent::setUpBeforeClass();
        require_once($CFG->dirroot . '/theme/roshnilite/lib.php');
    }

    /**
     * The settings marker gets the colour tokens; the custom CSS marker is
     * removed when no custom CSS is set.
     */
    public function test_process_css_defaults(): void {
        $this->resetAfterTest();
        $theme = \theme_config::load('roshnilite');

        $css = theme_roshnilite_process_css('a{}/* [[setting:settingsrules]] *//* [[setting:customcss]] */', $theme);

        $this->assertSame("a{}:root { --rl-primary: #e74c3c; --rl-accent: #e74c3c; }\n", $css);
    }

    /**
     * Configured settings are written in; the accent follows the main colour
     * when no brand colour is set.
     */
    public function test_process_css_settings(): void {
        $this->resetAfterTest();
        set_config('maincolor', '#123456', 'theme_roshnilite');
        set_config('brandcolor', '', 'theme_roshnilite');
        set_config('customcss', '.x{}', 'theme_roshnilite');
        $theme = \theme_config::load('roshnilite');

        $css = theme_roshnilite_process_css('/* [[setting:settingsrules]] */a{}/* [[setting:customcss]] */', $theme);

        $this->assertSame(":root { --rl-primary: #123456; --rl-accent: #123456; }\na{}.x{}", $css);
    }

    /**
     * A colour that is not a hex value is left out, so the stylesheet's
     * default stays and the CSS cannot be broken by a bad setting.
     */
    public function test_settings_css_ignores_invalid_colours(): void {
        $this->resetAfterTest();
        set_config('maincolor', 'red; } body { display: none', 'theme_roshnilite');
        set_config('brandcolor', '#abcdef', 'theme_roshnilite');
        $theme = \theme_config::load('roshnilite');

        $this->assertSame(":root { --rl-accent: #abcdef; }\n", theme_roshnilite_get_settings_css($theme));
    }

    /**
     * The extra SCSS only includes files that exist.
     */
    public function test_get_extra_scss(): void {
        $this->resetAfterTest();
        set_config('scss', '.y{color:red}', 'theme_roshnilite');
        $theme = \theme_config::load('roshnilite');

        $this->assertSame('.y{color:red}', theme_roshnilite_get_extra_scss($theme));
    }

    /**
     * An uploaded font is served from the file area its setting stores it in,
     * including the EOT files, which live in the "fontfileweot" areas.
     */
    public function test_get_font_css_uploaded_fonts(): void {
        $this->resetAfterTest();
        $fs = get_file_storage();
        $context = \context_system::instance();
        foreach (['fontfileweotheading' => 'brand.eot', 'fontfilewofftwoheading' => 'brand.woff2'] as $area => $filename) {
            $fs->create_file_from_string([
                'contextid' => $context->id,
                'component' => 'theme_roshnilite',
                'filearea' => $area,
                'itemid' => 0,
                'filepath' => '/',
                'filename' => $filename,
            ], 'font');
        }
        set_config('fontselect', '2', 'theme_roshnilite');
        set_config('fontnameheading', 'Brand', 'theme_roshnilite');
        set_config('fontfileeotheading', '/brand.eot', 'theme_roshnilite');
        set_config('fontfilewofftwoheading', '/brand.woff2', 'theme_roshnilite');
        $theme = \theme_config::load('roshnilite');

        $css = theme_roshnilite_get_font_css($theme);

        $this->assertMatchesRegularExpression(
            '#/theme_roshnilite/fontfileweotheading/[^)]*/brand\.eot\'\) format\(\'embedded-opentype\'\)#',
            $css
        );
        $this->assertMatchesRegularExpression(
            '#/theme_roshnilite/fontfilewofftwoheading/[^)]*/brand\.woff2\'\) format\(\'woff2\'\)#',
            $css
        );
        $this->assertStringContainsString(':root { --rl-font-display: "Brand"', $css);
    }
}
