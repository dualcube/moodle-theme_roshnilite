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
 * theme_roshnilite_core_renderer file.
 * @package    theme_roshnilite
 * @copyright  2015 dualcube {@link http://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * This file overrides some important core funcntions.
 */
/** 
 * overridding theme_bootstrapbase_core_renderer class for modify some core functions.
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_roshnilite_core_renderer extends theme_bootstrapbase_core_renderer {
    /** 
     * overridding context_header function.
     * @param bool $headerinfo
     * @param integer $headinglevel
     */
    public function context_header($headerinfo = null, $headinglevel = 1) {
        if ($headinglevel == 1 && !empty($this->page->theme->settings->logo)) {
            return html_writer::tag('div', '', array('class' => 'logo'));
        }
        return parent::context_header($headerinfo, $headinglevel);
    }
    /** 
     * overridding user_profile_picture function.
     */
    public function user_profile_picture() {
        GLOBAL $USER;
        $userpic = parent::user_picture($USER, array('link' => false, 'size' => 28));
        return $userpic;
    }
    /** 
     * overridding favicon function.
     */
    public function favicon() {
        GLOBAL $PAGE, $CFG;
        if (!empty($PAGE->theme->setting_file_url('faviconurl', 'faviconurl'))) {
            return $PAGE->theme->setting_file_url('faviconurl', 'faviconurl');
        } else {
            return $CFG->wwwroot.'/theme/'.$CFG->theme.'/favicon.ico';
        }
    }
}
