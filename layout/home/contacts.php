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
if (!empty($html->addressfontawesomeicon) || !empty($html->emailfontawesomeicon) || !empty($html->phonefontawesomeicon)) {
?>
<div class="contact-items">
	<div class="contact-items">
<?php
    if (!empty($html->addressfontawesomeicon)) {
?>
		<div class="contact-item">
			<?php echo $html->addressfontawesomeicon;?>
			<p><?php echo $html->address; ?></p>
		</div>
<?php 
    }
    if (!empty($html->emailfontawesomeicon)) {
?>
		<div class="contact-item">
			<?php echo $html->emailfontawesomeicon;?>
			<p><?php echo $html->email;?></p>
		</div>
<?php 
    }
    if (!empty($html->emailfontawesomeicon)) {
?>
		<div class="contact-item">
			<?php echo $html->phonefontawesomeicon;?>
			<p><?php echo $html->phone;?></p>
		</div>
<?php 
    }
?>
	</div>
</div>
</div><!-- END of .contacts -->
<?php
}