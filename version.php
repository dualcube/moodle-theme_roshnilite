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

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

$plugin->version = 2024032100;
$plugin->requires = 2022041900; // Moodle 4.0.
$plugin->component = 'theme_roshnilite';
$plugin->dependencies = ['theme_boost' => 2022041900];
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '3.5.4 (Build: 2024032100)';
