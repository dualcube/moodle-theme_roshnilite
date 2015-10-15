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
$PAGE->requires->css('/theme/roshnilite/css/font-awesome.min.css');
$PAGE->requires->css('/theme/roshnilite/css/styles.css');
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="image/x-icon" rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>">
	<?php 
        $PAGE->requires->js('/theme/roshnilite/js/jquery-1.11.1.min.js');
        $PAGE->requires->js('/theme/roshnilite/js/bootstrap.min.js');
        $PAGE->requires->js('/theme/roshnilite/js/jquery.bxslider.min.js');
        $PAGE->requires->js('/theme/roshnilite/js/jquery.scroll.js');
        $PAGE->requires->js('/theme/roshnilite/js/engine.js');
    ?>
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">
    <div id="page-content" class="clearfix background-grey">
        <?php echo $OUTPUT->main_content(); ?>
    </div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
