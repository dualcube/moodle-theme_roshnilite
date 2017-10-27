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
 * @copyright  2015 dualcube {@link https://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$course = $DB->get_records_sql('SELECT c.* FROM {course} c where id != ? AND visible = ?', array(1, 1));
$coursedetailsarray = array();
foreach ($course as $key => $coursevalue) {
    $coursedetailsarray[$key]["courseid"] = $CFG->wwwroot."/course/view.php?id=".$coursevalue->id;
    $coursedetailsarray[$key]["coursename"] = $coursevalue->fullname;
    $coursecontext = context_course::instance($coursevalue->id);
    $isfile = $DB->get_records_sql("Select * from {files} where contextid = ? and filename != ? and filearea = ?", array($coursecontext->id, ".", "overviewfiles"));
    if ( $isfile ) {
        foreach ($isfile as $key1 => $isfilevalue) {
            $courseimage = $CFG->wwwroot . "/pluginfile.php/" . $isfilevalue->contextid .
            "/" . $isfilevalue->component . "/" . $isfilevalue->filearea . "/" . $isfilevalue->filename;
        }
    }
    if ( !empty( $courseimage ) ) {
        $coursedetailsarray[$key]["courseimage"] = $courseimage;
    } else {
        $coursedetailsarray[$key]["courseimage"] = $CFG->wwwroot."/theme/roshnilite/data/nopic.jpg";
    }
    $courseimage = '';
}
?>
<div class = "clearfix"></div>
<?php if ( $coursedetailsarray ) { ?>
    <div class="av-courses header2-nav-color">
        <div class="header-top">
            <h2 class="header-b"><?php echo get_string('courses');?></h2>
        </div>
        <ul class="av-courses-slider">
      <?php
      if (COUNT($coursedetailsarray) == 1) { ?>
        <script type="text/javascript">
            $( document ).ready(function() {
                $(".av-courses").find(".bx-wrapper").find(".bx-viewport").find("ul").find(".bx-clone").remove();
                $(".av-courses").find(".bx-wrapper").find(".bx-controls").find(".bx-controls-direction").find(".bx-prev").hide();
                $(".av-courses").find(".bx-wrapper").find(".bx-controls").find(".bx-controls-direction").find(".bx-next").hide();
                $(".av-courses").find(".bx-wrapper").find(".bx-viewport").find(".av-courses-slider").find(".fourth-effect").css("float", "none");  
            });
        </script>
<?php  } else { ?>
        <script type="text/javascript">
            $( document ).ready(function() {
                $(".av-courses").find(".bx-wrapper").find(".bx-controls").find(".bx-controls-direction").find(".bx-prev").show();
                $(".av-courses").find(".bx-wrapper").find(".bx-controls").find(".bx-controls-direction").find(".bx-next").show();
                $(".av-courses").find(".bx-wrapper").find(".bx-viewport").find(".av-courses-slider").find(".fourth-effect").css("float", "left");  
            });
        </script>
<?php  }
       foreach ($coursedetailsarray as $avlcoursearrayvalue) { ?>
                <li class = "view fourth-effect">
          <?php if ( !empty ($avlcoursearrayvalue["courseimage"]) ) { ?> 
                    <img src="<?php echo $avlcoursearrayvalue["courseimage"]; ?>" alt="">
          <?php }
                if ( !empty ($avlcoursearrayvalue["coursename"]) ) { ?> 
                    <div class="mask"></div>
                    <div class="av-course-item-cont">
                       <h2><a href="<?php echo $avlcoursearrayvalue["courseid"]; ?>">
                       <?php echo $avlcoursearrayvalue["coursename"]; ?></a></h2>
                    </div>
          <?php } ?>
                </li>
      <?php } ?>
        </ul><!-- END of .av-courses-slider -->
    <div>
    <a href="<?php echo $CFG->wwwroot ?>/course/index.php" class="btn-view-all"><?php echo get_string('viewallcourses');?></a>
</div>
</div><!-- END of .av-courses -->
<?php
}
