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

namespace theme_roshnilite\output;

use core\url;

/**
 * Core renderer for the Roshni Lite theme.
 *
 * Feeds the theme's own logo and favicon settings into the standard Boost
 * navbar and page head, so every layout inherited from Boost picks them up.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    /**
     * Returns the URL of a file stored in one of this theme's file settings.
     *
     * @param string $setting The setting name, which is also its file area.
     * @return url|null
     */
    protected function get_theme_setting_file_url(string $setting): ?url {
        $filename = $this->page->theme->settings->{$setting} ?? '';
        if ($filename === '' || $filename === '/') {
            return null;
        }
        return url::make_pluginfile_url(
            \context_system::instance()->id,
            'theme_roshnilite',
            $setting,
            theme_get_revision(),
            '/',
            ltrim($filename, '/')
        );
    }

    /**
     * The theme logo, falling back to the site logo and then the bundled one.
     *
     * @param int|null $maxwidth The maximum width, or null when the width does not matter.
     * @param int $maxheight The maximum height, or null when the height does not matter.
     * @return url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        return $this->get_theme_setting_file_url('logo')
            ?? parent::get_logo_url($maxwidth, $maxheight)
            ?: $this->image_url('img/logo', 'theme_roshnilite');
    }

    /**
     * The compact logo shown in the navbar.
     *
     * @param int $maxwidth The maximum width, or null when the width does not matter.
     * @param int $maxheight The maximum height, or null when the height does not matter.
     * @return url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        return $this->get_theme_setting_file_url('logo')
            ?? parent::get_compact_logo_url($maxwidth, $maxheight)
            ?: $this->image_url('img/logo', 'theme_roshnilite');
    }

    /**
     * The page header. On the Dashboard, the personal Roshni Lite header
     * (greeting, learning stats, quick actions, courses to continue) comes first.
     *
     * @return string HTML
     */
    public function full_header() {
        global $USER;
        $header = parent::full_header();
        // Only the Dashboard itself: other pages (Messages, for one) share its layout.
        if ($this->page->pagetype !== 'my-index' || !isloggedin() || isguestuser()) {
            return $header;
        }
        $dashboard = new dashboard($USER);
        return $this->render_from_template('theme_roshnilite/dashboard', $dashboard->export_for_template($this)) . $header;
    }

    /**
     * The favicon, from the theme setting when one is uploaded.
     *
     * @return url
     */
    public function favicon() {
        return $this->get_theme_setting_file_url('favicon') ?? parent::favicon();
    }
}
