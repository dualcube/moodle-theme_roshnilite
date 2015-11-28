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
$checkaboutsiteimage1 = $PAGE->theme->setting_file_url('aboutsiteimage1', 'aboutsiteimage1');
if (!empty($checkaboutsiteimage1)) {
    $aboutsiteimage1 = $PAGE->theme->setting_file_url('aboutsiteimage1', 'aboutsiteimage1');
} else {
    $aboutsiteimage1 = $CFG->wwwroot."/theme/roshnilite/data/icon-conts-1.png";
}
$checkaboutsiteimage2 = $PAGE->theme->setting_file_url('aboutsiteimage2', 'aboutsiteimage2');
if (!empty($checkaboutsiteimage2)) {
    $aboutsiteimage2 = $PAGE->theme->setting_file_url('aboutsiteimage2', 'aboutsiteimage2');
} else {
    $aboutsiteimage2 = $CFG->wwwroot."/theme/roshnilite/data/icon-conts-2.png";
}
$checkaboutsiteimage3 = $PAGE->theme->setting_file_url('aboutsiteimage3', 'aboutsiteimage3');
if (!empty($checkaboutsiteimage3)) {
    $aboutsiteimage3 = $PAGE->theme->setting_file_url('aboutsiteimage3', 'aboutsiteimage3');
} else {
    $aboutsiteimage3 = $CFG->wwwroot."/theme/roshnilite/data/icon-conts-3.png";
}
$checkaboutsiteimage4 = $PAGE->theme->setting_file_url('aboutsiteimage4', 'aboutsiteimage4');
if (!empty($checkaboutsiteimage4)) {
    $aboutsiteimage4 = $PAGE->theme->setting_file_url('aboutsiteimage4', 'aboutsiteimage4');
} else {
    $aboutsiteimage4 = $CFG->wwwroot."/theme/roshnilite/data/icon-conts-4.png";
}
?>
<?php
if (!empty($html->aboutsiteheading)) {
?>
<div class="about">
<div class="container">
<h1 class="h-large"><?php echo $html->aboutsiteheading;?><span></span></h1>
<h3 class="header-b-2"><?php echo $html->aboutsitesubheading;?></h3>
<div class="clearfix"></div>
<div class="about-items">
<?php
    if (!empty($html->aboutsitetext1)) {
?>
<div class="about-item">
<div class="about-item-img-wr">
<div class="about-item-img">
<a href="<?php echo $html->aboutsiteurl1; ?>"><img alt="" src="<?php echo $aboutsiteimage1; ?>"></i></a>
</div>
</div>
<h5><a href="<?php echo $html->aboutsiteurl1; ?>"><?php echo $html->aboutsitename1; ?></a></h5>
<p><?php echo $html->aboutsitetext1; ?></p>
</div><!-- END of .about-item -->
<?php
    }
    if (!empty($html->aboutsitetext2)) {
?>
<div class="about-item">
<div class="about-item-img-wr">
<div class="about-item-img">
<a href="<?php echo $html->aboutsiteurl2; ?>"><img alt="" src="<?php echo $aboutsiteimage2; ?>"></a>
</div>
</div>
<h5><a href="<?php echo $html->aboutsiteurl2; ?>"><?php echo $html->aboutsitename2; ?></a></h5>
<p><?php echo $html->aboutsitetext2; ?></p>
</div><!-- END of .about-item -->
<?php
    }
    if (!empty($html->aboutsitetext3)) {
?>
<div class="about-item">
<div class="about-item-img-wr">
<div class="about-item-img">
<a href="<?php echo $html->aboutsiteurl3; ?>"><img alt="" src="<?php echo $aboutsiteimage3; ?>"></a>
</div>
</div>
<h5><a href="<?php echo $html->aboutsiteurl3; ?>"><?php echo $html->aboutsitename3; ?></a></h5>
<p><?php echo $html->aboutsitetext3; ?></p>
</div><!-- END of .about-item -->
<?php
    }
    if (!empty($html->aboutsitetext4)) {
?>
<div class="about-item">
<div class="about-item-img-wr">
<div class="about-item-img">
<a href="<?php echo $html->aboutsiteurl4; ?>"><img alt="" src="<?php echo $aboutsiteimage4; ?>"></a>
</div>
</div>
<h5><a href="<?php echo $html->aboutsiteurl4; ?>"><?php echo $html->aboutsitename4; ?></a></h5>
<p><?php echo $html->aboutsitetext4; ?></p>
</div><!-- END of .about-item -->
<?php 
    }
?>
</div><!-- END of .about-items -->
</div><!-- END of .container -->
</div><!-- END of .about -->
<?php 
}
