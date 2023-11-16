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
global $CFG;

$THEME->name = 'roshnilite';

$THEME->sheets = ['styles'];

$THEME->editor_sheets = [];
$THEME->parents = ['boost'];
$THEME->haseditswitch = true;
$THEME->enable_dock = false;
$THEME->extrascsscallback = 'theme_roshnilite_get_extra_scss';
$THEME->prescsscallback = 'theme_roshnilite_get_pre_scss';
$THEME->precompiledcsscallback = 'theme_roshnilite_get_precompiled_css';
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_roshnilite_process_css';
$THEME->scss = function ($theme) {
    return theme_roshnilite_get_main_scss_content($theme);
};
$THEME->layouts = [
    // Most backwards compatible layout without the blocks - this is the layout used by default.
    'base' => [
        'file' => 'columns.php',
        'regions' => [],
    ],
    // Standard layout with blocks, this is recommended for most pages with general information.
    'standard' => [
        'file' => 'columns.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // Main course page.
    'course' => [
        'file' => 'columns.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ],
    'coursecategory' => [
        'file' => 'columns.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => [
        'file' => 'columns.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // The site home page.
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['nofullheader' => true, 'nonavbar' => true],
    ],
    // Server administration scripts.
    'admin' => [
        'file' => 'columns.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // My dashboard page.
    'mydashboard' => [
        'file' => 'columns.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true, 'footer' => true, 'langmenu' => true, 'nocontextheader' => true],
    ],
    // My public page.
    'mypublic' => [
        'file' => 'columns.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true],
    ],
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => [
        'file' => 'contentonly.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => true],
    ],
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => [
        'file' => 'contentonly.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nocoursefooter' => true],
    ],
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, links, or API calls that would lead to database or cache interaction.
    // Please be extremely careful if you are modifying this layout.
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
    ],
    // Should display the content and basic headers only.
    'print' => [
        'file' => 'contentonly.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false],
    ],
    // The pagelayout used when a redirection is occuring.
    'redirect' => [
        'file' => 'frontpage.php',
        'regions' => [],
    ],
    // The pagelayout used for reports.
    'report' => [
        'file' => 'columns.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // The pagelayout used for safebrowser and securewindow.
    'secure' => [
        'file' => 'secure.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        ],
];
