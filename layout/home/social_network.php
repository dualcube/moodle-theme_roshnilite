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
 * Moodle's roshnilite theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package    theme_roshnilite
 * @copyright  2015 dualcube {@link http://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($html->socialfontawesomeicon1) || !empty($html->socialfontawesomeicon2) ||
!empty($html->socialfontawesomeicon3) || !empty($html->socialfontawesomeicon4)) {
?>
<div class="stay-connected">
<div class="container">
<h2 class="header-b-2"><?php echo $html->socialheading; ?></h2>
<div class="social-links">
<?php
    if ( !empty($html->socialfontawesomeicon1) ) {
?>
<a href="<?php echo $html->socialicon1;?>"><?php echo $html->socialfontawesomeicon1;?></a>
<?php
    }
?>
<?php
    if ( !empty($html->socialfontawesomeicon2)) {
?>
<a href="<?php echo $html->socialicon2;?>"><?php echo $html->socialfontawesomeicon2;?></a>
<?php
    }
?>
<?php
    if ( !empty($html->socialfontawesomeicon3)) {
?>
<a href="<?php echo $html->socialicon3;?>"><?php echo $html->socialfontawesomeicon3;?></a>
<?php
    }
?>
<?php
    if ( !empty($html->socialfontawesomeicon4)) {
?>
<a href="<?php echo $html->socialicon4;?>"><?php echo $html->socialfontawesomeicon4;?></a>
<?php
    }
?>
</div>
</div><!-- END of .container -->
</div><!-- END of .stay-connected -->
<?php 
}
