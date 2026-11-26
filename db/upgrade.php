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
 * Upgrade steps for the Roshni Lite theme.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the theme.
 *
 * @param int $oldversion The version being upgraded from.
 * @return bool
 */
function xmldb_theme_roshnilite_upgrade($oldversion) {
    if ($oldversion < 2026112600) {
        theme_roshnilite_replace_placeholder_copy();
        upgrade_plugin_savepoint(true, 2026112600, 'theme', 'roshnilite');
    }

    if ($oldversion < 2026112601) {
        // The categories heading now introduces the course catalogue.
        theme_roshnilite_replace_placeholder_copy();
        upgrade_plugin_savepoint(true, 2026112601, 'theme', 'roshnilite');
    }

    if ($oldversion < 2026112602) {
        // The fourth site home feature is now "Community": events have their own section.
        theme_roshnilite_replace_placeholder_copy();
        upgrade_plugin_savepoint(true, 2026112602, 'theme', 'roshnilite');
    }
    if ($oldversion < 2026112700) {
        // Contact details and links that are still the theme's samples are cleared,
        // so a site never shows the theme author's address as its own.
        theme_roshnilite_clear_sample_contacts();
        upgrade_plugin_savepoint(true, 2026112700, 'theme', 'roshnilite');
    }
    return true;
}

/**
 * Clear contact details and links that still hold the theme's sample values.
 *
 * Older releases saved a sample address, email and phone number, links to
 * "javascript:void(0);" and a Google+ icon as defaults. Only exact matches
 * are changed.
 */
function theme_roshnilite_clear_sample_contacts(): void {
    $samples = [
        'address' => 'Kolkata, India',
        'email' => 'admin@dualcube.com',
        'phone' => '+91 33 64578322',
    ];
    foreach ($samples as $setting => $sample) {
        $current = get_config('theme_roshnilite', $setting);
        if ($current !== false && trim(html_entity_decode(strip_tags((string) $current))) === $sample) {
            set_config($setting, '', 'theme_roshnilite');
        }
    }

    $links = ['sliderurl1', 'sliderurl2', 'sliderurl3', 'sliderurl4', 'sliderurl5',
        'socialicon1', 'socialicon2', 'socialicon3', 'socialicon4'];
    foreach (['facultyfburl', 'facultytwitterurl', 'facultygoogleurl', 'facultylnkdnurl'] as $network) {
        for ($i = 1; $i <= 12; $i++) {
            $links[] = $network . $i;
        }
    }
    foreach ($links as $setting) {
        if (get_config('theme_roshnilite', $setting) === 'javascript:void(0);') {
            set_config($setting, '', 'theme_roshnilite');
        }
    }

    if (trim((string) get_config('theme_roshnilite', 'socialfontawesomeicon4')) === '<i class="fa fa-google-plus"></i>') {
        set_config('socialfontawesomeicon4', '<i class="fa fa-youtube"></i>', 'theme_roshnilite');
    }
}

/**
 * Replace the site home copy that is still an old placeholder with the current default.
 *
 * Settings are saved with their default text on install, so older sites still
 * show copy such as "Put In a Nice Little Piece Of Text That Describes Your
 * USP". Only a value that exactly matches a known placeholder is replaced;
 * anything an admin has written is kept.
 */
function theme_roshnilite_replace_placeholder_copy(): void {
    $placeholders = [
        'aboutsiteheading' => ['aboutsiteheadingdefault', ['NOBODY DOES IT LIKE US', 'Nobody does it like us']],
        'aboutsitesubheading' => ['aboutsitesubheadingdefault', [
            'Put In a Nice Little Piece Of Text That Describes Your USP',
            'A short line about what makes learning here different.',
        ]],
        'aboutsitename1' => ['aboutsitename1default', ['Our Blog', 'Our blog']],
        'aboutsitetext1' => ['aboutsitetext1default', [
            "There's only one way to find out what life can be like at University of Utopia: " .
                "dip into some of our students' uncut and uncensored blogs.",
        ]],
        'aboutsitetext2' => ['aboutsitetext2default', [
            'You can rename the content box names from the admin panel, and then add nifty descriptions for all the content boxes.',
        ]],
        'aboutsitename3' => ['aboutsitename3default', ['Latest News']],
        'aboutsitetext3' => ['aboutsitetext3default', [
            'Wondering what is happening at you? A lot. And reading through this section will keep you updated ' .
                'about all the cutting edge research we are doing here!',
        ]],
        'aboutsitename4' => ['aboutsitename4default', ['Upcoming Events', 'Upcoming events']],
        'aboutsitetext4' => ['fraboutsitetextdescdefault', [
            "All these content boxes are completely editable. You can change the hover colors, icons, names and " .
                "the description text. Cool, isn't it?",
            'Live sessions, deadlines and events, so you never miss what matters.',
        ]],
        'masonryheading' => ['masonryheadingdefault', ['OUR CATEGORY', 'Our categories']],
        'masonrysubheading' => ['masonrysubheadingdefault', [
            'You Can Showcase All Your Categories In This Beautiful Masonry Block',
            'Find the subject that is right for you.',
        ]],
    ];

    foreach ($placeholders as $setting => [$stringid, $oldvalues]) {
        $current = get_config('theme_roshnilite', $setting);
        if ($current === false) {
            continue;
        }
        // Rich text settings may have been saved wrapped in a paragraph.
        $plain = trim(html_entity_decode(strip_tags((string) $current), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if (in_array($plain, $oldvalues, true)) {
            set_config($setting, get_string($stringid, 'theme_roshnilite'), 'theme_roshnilite');
        }
    }
}
