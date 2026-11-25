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
 * @covers     ::theme_roshnilite_get_extra_scss
 */
#[\PHPUnit\Framework\Attributes\CoversFunction('theme_roshnilite_process_css')]
#[\PHPUnit\Framework\Attributes\CoversFunction('theme_roshnilite_get_extra_scss')]
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
     * Unset settings are replaced with valid defaults, never left empty.
     */
    public function test_process_css_defaults(): void {
        $this->resetAfterTest();
        $theme = \theme_config::load('roshnilite');

        $css = theme_roshnilite_process_css(
            'a{color:[[setting:maincolor]]}b{color:[[setting:brandcolor]]}c{font-size:[[setting:fontsize]]px}[[setting:customcss]]',
            $theme
        );

        $this->assertSame('a{color:#e74c3c}b{color:#e74c3c}c{font-size:15px}', $css);
    }

    /**
     * Configured settings are written into the CSS.
     */
    public function test_process_css_settings(): void {
        $this->resetAfterTest();
        set_config('maincolor', '#123456', 'theme_roshnilite');
        set_config('customcss', '.x{}', 'theme_roshnilite');
        $theme = \theme_config::load('roshnilite');

        $css = theme_roshnilite_process_css('a{color:[[setting:maincolor]]}[[setting:customcss]]', $theme);

        $this->assertSame('a{color:#123456}.x{}', $css);
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
}
