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
// Get the HTML for the settings bits.
$html = theme_roshnilite_get_html_for_settings($OUTPUT, $PAGE);
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
        $PAGE->requires->js('/theme/roshnilite/js/jquery-1.11.1.min.js', true);
        $PAGE->requires->js('/theme/roshnilite/js/bootstrap.min.js', true);
        $PAGE->requires->js('/theme/roshnilite/js/jquery.bxslider.min.js', true);
        $PAGE->requires->js('/theme/roshnilite/js/jquery.scroll.js', true);
        $PAGE->requires->js('/theme/roshnilite/js/engine.js', true);
    ?>
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header class="navbar navbar-fixed-top<?php echo $html->navbarclass ?> moodle-has-zindex">
    <div class="inner-header">
        <nav class="navbar-inner">
            <div class="container">
                <a class="inner-logo logo-text" href="<?php echo $CFG->wwwroot;?>"></a>
                <?php echo $OUTPUT->lang_menu(); 
                 echo $OUTPUT->user_menu(); ?>
                 <?php if ($CFG->version >= 2016120500) { ?>
                    <div class="messagesnotifications">
                        <?php echo $OUTPUT->navbar_plugin_output(); ?>
                    </div>
                 <?php } ?>
            </div>
        </nav>
    </div>
</header>
<div id="page">
    <?php if ($CFG->version >= 2015051100) {
        echo $OUTPUT->full_header();
} else { ?>
    <header id="page-header" class="clearfix head-columnone">
        <div id="page-navbar" class="clearfix">
            <div class = "container">
                <div class="row">
                    <nav class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
                    <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
                </div>
            </div>
        </div>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>
    <?php 
}
    ?>
    <div id="page-content" class="row-fluid">
        <div class = "container">
            <section id="region-main" class="span12">
                <?php
                echo $OUTPUT->course_content_header();
                echo $OUTPUT->main_content();
                echo $OUTPUT->course_content_footer();
                ?>
            </section>
        </div>
    </div>
    <?php require('footer.php'); echo $OUTPUT->standard_end_of_body_html() ?>
</div>
</body>
</html>
