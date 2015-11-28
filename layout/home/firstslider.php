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
$checkslideimage1 = $PAGE->theme->setting_file_url('slideimage1', 'slideimage1');
if (!empty($checkslideimage1)) {
    $sliderimage1 = $PAGE->theme->setting_file_url('slideimage1', 'slideimage1');
} else {
    $sliderimage1 = $CFG->wwwroot."/theme/roshnilite/data/sl-1.jpg";
}
$checkslideimage2 = $PAGE->theme->setting_file_url('slideimage2', 'slideimage2');
if (!empty($checkslideimage2)) {
    $sliderimage2 = $PAGE->theme->setting_file_url('slideimage2', 'slideimage2');
} else {
    $sliderimage2 = $CFG->wwwroot."/theme/roshnilite/data/sl-1.jpg";
}
$checkslideimage3 = $PAGE->theme->setting_file_url('slideimage3', 'slideimage3');
if (!empty($checkslideimage3)) {
    $sliderimage3 = $PAGE->theme->setting_file_url('slideimage3', 'slideimage3');
} else {
    $sliderimage3 = $CFG->wwwroot."/theme/roshnilite/data/sl-1.jpg";
}
$checkslideimage4 = $PAGE->theme->setting_file_url('slideimage4', 'slideimage4');
if (!empty($checkslideimage4)) {
    $sliderimage4 = $PAGE->theme->setting_file_url('slideimage4', 'slideimage4');
} else {
    $sliderimage4 = $CFG->wwwroot."/theme/roshnilite/data/sl-1.jpg";
}
$checkslideimage5 = $PAGE->theme->setting_file_url('slideimage5', 'slideimage5');
if (!empty($checkslideimage5)) {
    $sliderimage5 = $PAGE->theme->setting_file_url('slideimage5', 'slideimage5');
} else {
    $sliderimage5 = $CFG->wwwroot."/theme/roshnilite/data/sl-1.jpg";
}
$checkslideimage6 = $PAGE->theme->setting_file_url('slideimage6', 'slideimage6');
if (!empty($checkslideimage6)) {
    $sliderimage6 = $PAGE->theme->setting_file_url('slideimage6', 'slideimage6');
} else {
    $sliderimage6 = $CFG->wwwroot."/theme/roshnilite/data/sl-1.jpg";
}
?>
<?php if (!empty($html->slidertext1) || !empty($html->slidertext2) || !empty($html->slidertext3) ||
!empty($html->slidertext4) || !empty($html->slidertext5) || !empty($html->slidertext6)) { ?>
<ul class="top-slider">
<?php
    if (!empty($html->slidertext1)) {
?>
	<li class="content-wrap">
		<img src="<?php echo $sliderimage1;?>" alt="">
		<div class="top-slide-content"><div class="customslider">
			<?php echo $html->slidertext1;?>
<?php
        if (!empty($html->sliderbuttontext1)) {
?>
			<a href="<?php echo $html->sliderurl1;?>" class="btn-1"><?php echo $html->sliderbuttontext1;?></a>
<?php
        }
?>
		</div></div>
	</li>
<?php
    }
    if (!empty($html->slidertext2)) {
?>
	<li class="content-wrap">
		<img src="<?php echo $sliderimage2;?>" alt="">
		<div class="top-slide-content"><div class="customslider">
			<?php echo $html->slidertext2;?>
<?php
        if (!empty($html->sliderbuttontext2)) {
?>
			<a href="<?php echo $html->sliderurl2;?>" class="btn-1"><?php echo $html->sliderbuttontext2;?></a>
<?php
        }
?>
		</div></div>
	</li>
<?php
    }
    if (!empty($html->slidertext3)) {
?>
	<li class="content-wrap">
		<img src="<?php echo $sliderimage3;?>" alt="">
		<div class="top-slide-content">
			<?php echo $html->slidertext3;?>
<?php
        if (!empty($html->sliderbuttontext3)) {
?>
			<a href="<?php echo $html->sliderurl3;?>" class="btn-1"><?php echo $html->sliderbuttontext3;?></a>
<?php
        }
?>
		</div>
	</li>
<?php
    }
    if (!empty($html->slidertext4)) {
?>
	<li class="content-wra    ">
		<img src="<?php echo $sliderimage4;?>" alt="">
		<div class="top-slide-content">
			<?php echo $html->slidertext4;?>
<?php
        if (!empty($html->sliderbuttontext4)) {
?>
			<a href="<?php echo $html->sliderurl4;?>" class="btn-1"><?php echo $html->sliderbuttontext4;?></a>
<?php
        }
?>
		</div>
	</li>
<?php
    }
    if (!empty($html->slidertext5)) {
?>
	<li class="content-wrap">
		<img src="<?php echo $sliderimage5;?>" alt="">
		<div class="top-slide-content">
			<?php echo $html->slidertext5;?>
<?php
        if (!empty($html->sliderbuttontext5)) {
?>
			<a href="<?php echo $html->sliderurl6;?>" class="btn-1"><?php echo $html->sliderbuttontext5;?></a>
<?php
        }
?>
        </div>
	</li>
<?php
    }
    if (!empty($html->slidertext6)) {
?>
	<li class="content-wrap">
		<img src="<?php echo $sliderimage6;?>" alt="">
		<div class="top-slide-content">
			<?php echo $html->slidertext6;?>
<?php
        if (!empty($html->sliderbuttontext6)) {
?>
			<a href="<?php echo $html->sliderurl6;?>" class="btn-1"><?php echo $html->sliderbuttontext6;?></a>
<?php
        }
?>
		</div>
	</li>
<?php
    }
?>
</ul>
<?php
}