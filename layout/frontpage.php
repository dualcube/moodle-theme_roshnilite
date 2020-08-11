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
 * @copyright  2020 DualCube {@link https://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB, $PAGE;

if (!empty($PAGE->theme->setting_file_url('logo', 'logo'))) {
    $imgpath = $PAGE->theme->setting_file_url('logo', 'logo');
} else {
    $imgpath = $CFG->wwwroot."/theme/roshnilite/style/img/logo.png";
}
if (!isloggedin()) { 
    $login = '    <div class="loginnavbar">
                <div class="container">
                    <form method="post" action="' . $CFG->wwwroot . '/login/index.php?authldap_skipntlmsso=1">
                        <input type="hidden" name="logintoken" value="' . s(\core\session\manager::get_login_token()) . '" />
                        <input type="text" name="username" placeholder="Username:">
                        <input type="password" name="password" placeholder="Password:">
                        <input type="submit" value="LOG IN">
                    </form>
                </div>
            </div>    ';
    $logvar = 0;
}else{
    $login = '';
    $logvar = 1;
}

if (!empty($PAGE->theme->setting_file_url('favicon', 'favicon'))) {
    $favicon = $PAGE->theme->setting_file_url('favicon', 'favicon');
} else {
    $favicon = $CFG->wwwroot."/theme/roshnilite/pix/favicon.ico";
}

$bodyattributes = $OUTPUT->body_attributes();
$backgroundimage = $PAGE->theme->setting_file_url('backgroundimage', 'backgroundimage');

$html = theme_roshnilite_get_html_for_settings($OUTPUT, $PAGE);

$roshniliteformatoptions = new stdClass();
$roshniliteformatoptions->noclean = true;
$roshniliteformatoptions->overflowdiv = false;

$enablemoodlemaincontent = get_config('theme_roshnilite', 'moodlemaincontentinfrontpage');

if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-bs-main-and-pre';
}

$context = context_system::instance();

$checkslidercount = $PAGE->theme->setting_file_url('slidercount', 'slidercount');
$sliderdetails = '';
if (!empty($checkslidercount)) {

    $slideimagecheck = $PAGE->theme->setting_file_url('slideimage1', 'slideimage1');
    $slidertextcheck = get_config('theme_roshnilite', 'slidertext1');
    $sliderbuttontextcheck = get_config('theme_roshnilite', 'sliderbuttontext1');
    $sliderurlcheck = get_config('theme_roshnilite', 'sliderurl1');
    if (!empty($slideimagecheck) || !empty($slidertextcheck) || !empty($sliderbuttontextcheck) || !empty($sliderurlcheck)) {

        $start = strlen($checkslidercount) - 1;
        $totalslidercount = '';

        for ($x = $start; $x < strlen($checkslidercount); $x++) {
            $totalslidercount = $checkslidercount[$x];
        }
        $totalslidercount = (int)$totalslidercount;
        $sliderdetails = '<div class="container-fluid no-padding">
        <div id="home-slide" class="carousel slide" data-ride="carousel">
            <ul class="carousel-indicators">';
        for ($i = 0; $i < $totalslidercount; $i++) {
            $sliderdetails .= '<li data-target="#home-slide" data-slide-to="' . $i . '"></li>';
        }
        $sliderdetails .= '</ul><div class="carousel-inner">';

        for ($i = 1; $i <= $totalslidercount; $i++) {
            $slideimage = $PAGE->theme->setting_file_url('slideimage' . $i, 'slideimage' . $i);
            if ($slideimage == '') {
                $slideimage = $CFG->wwwroot."/theme/roshnilite/pix/sl-1.jpg";
            }
            $slidertext = get_config('theme_roshnilite', 'slidertext' . $i);
            $sliderbuttontext = get_config('theme_roshnilite', 'sliderbuttontext' . $i);
            $sliderurl = get_config('theme_roshnilite', 'sliderurl' . $i);
            if ($i == 1) {
                $active = 'active';
            } else {
                $active = '';
            }

            $sliderdetails .= '<div class="carousel-item ' . $active . '">
                    <img src="' . $slideimage . '" alt="sliderimage" />
                    <div class="carousel-caption">
                        ' . $slidertext;
            if ($sliderbuttontext != '') {
                $sliderdetails .= '<a href="' . $sliderurl . '" class="btn-theme">' . $sliderbuttontext.'</a>';
            }
            $sliderdetails .= '</div>
                </div>';
        }
        $sliderdetails .= '</div>';
    }
    $sliderdetails .= '</div>
    </div>';
} else {
    $sliderdetails = '';
}

$aboutsiteheading = format_text((!empty($html->aboutsiteheading)) ? $html->aboutsiteheading :
    get_string('aboutsiteheadingdefault', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsitesubheading = format_text((!empty($html->aboutsitesubheading)) ? $html->aboutsitesubheading :
    get_string('aboutsitesubheadingdefault', 'theme_roshnilite'), "", $roshniliteformatoptions);

if ($enablemoodlemaincontent == 1) {
    $maincontent = '<div class="container" style="display:none;">
                    <div class="moodlecorecontent">';
    $maincontent .= $OUTPUT->main_content();
    if (isloggedin()) {
        if (has_capability('moodle/course:create', $context)) {
            if ($PAGE->user_is_editing()) {
                $maincontent .= '<a class = "turnedit turneditbtn" href="' . $turneditingoff . '">' . get_string('turneditingoff') . '</a>';
            } else {
                $maincontent .= '<a class = "turnedit turneditbtn" href="">' . get_string('turneditingon') . '</a>';
            }
        }
    }
    $maincontent .= '</div></div>';
} else {
    $maincontent = '<div class="maincontent" style="display:none;">' . $OUTPUT->main_content() . '</div>';
}

if ($CFG->version >= 2018120300) {
    $version18 = $OUTPUT->standard_after_main_region_html();
} else {
    $version18 = '';
}

$aboutsitename1 = format_text((!empty($html->aboutsitename1)) ? $html->aboutsitename1 :
    get_string('aboutsitename1default', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsitetext1 = format_text((!empty($html->aboutsitetext1)) ? $html->aboutsitetext1 :
    get_string('aboutsitetext1default', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsiteurl1 = (!empty($html->aboutsiteurl1)) ? $html->aboutsiteurl1 : '';

$checkaboutsiteimage1 = $PAGE->theme->setting_file_url('aboutsiteimage1', 'aboutsiteimage1');
if (!empty($checkaboutsiteimage1)) {
    $aboutsiteimage1 = $PAGE->theme->setting_file_url('aboutsiteimage1', 'aboutsiteimage1');
} else {
    $aboutsiteimage1 = $CFG->wwwroot."/theme/roshnilite/pix/icon-conts-1.png";
}

$aboutsitename2 = format_text((!empty($html->aboutsitename2)) ? $html->aboutsitename2 :
    get_string('aboutsitename2default', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsitetext2 = format_text((!empty($html->aboutsitetext2)) ? $html->aboutsitetext2 :
    get_string('aboutsitetext2default', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsiteurl2 = (!empty($html->aboutsiteurl2)) ? $html->aboutsiteurl2 : '';
$checkaboutsiteimage2 = $PAGE->theme->setting_file_url('aboutsiteimage2', 'aboutsiteimage2');
if (!empty($checkaboutsiteimage2)) {
    $aboutsiteimage2 = $PAGE->theme->setting_file_url('aboutsiteimage2', 'aboutsiteimage2');
} else {
    $aboutsiteimage2 = $CFG->wwwroot."/theme/roshnilite/pix/icon-conts-2.png";
}

$aboutsitename3 = format_text((!empty($html->aboutsitename3)) ? $html->aboutsitename3 :
    get_string('aboutsitename3default', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsitetext3 = format_text((!empty($html->aboutsitetext3)) ? $html->aboutsitetext3 :
    get_string('aboutsitetext3default', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsiteurl3 = (!empty($html->aboutsiteurl3)) ? $html->aboutsiteurl3 : '';
$checkaboutsiteimage3 = $PAGE->theme->setting_file_url('aboutsiteimage3', 'aboutsiteimage3');
if (!empty($checkaboutsiteimage3)) {
    $aboutsiteimage3 = $PAGE->theme->setting_file_url('aboutsiteimage3', 'aboutsiteimage3');
} else {
    $aboutsiteimage3 = $CFG->wwwroot."/theme/roshnilite/pix/icon-conts-3.png";
}

$aboutsitename4 = format_text((!empty($html->aboutsitename4)) ? $html->aboutsitename4 :
    get_string('aboutsitename4default', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsitetext4 = format_text((!empty($html->aboutsitetext4)) ? $html->aboutsitetext4 :
    get_string('fraboutsitetextdescdefault', 'theme_roshnilite'), "", $roshniliteformatoptions);
$aboutsiteurl4 = (!empty($html->aboutsiteurl4)) ? $html->aboutsiteurl4 : '';
$checkaboutsiteimage4 = $PAGE->theme->setting_file_url('aboutsiteimage4', 'aboutsiteimage4');
if (!empty($checkaboutsiteimage4)) {
    $aboutsiteimage4 = $PAGE->theme->setting_file_url('aboutsiteimage4', 'aboutsiteimage4');
} else {
    $aboutsiteimage4 = $CFG->wwwroot."/theme/roshnilite/pix/icon-conts-4.png";
}

$course = $DB->get_records_sql('SELECT c.* FROM {course} c where id != ? AND visible = ?', array(1, 1));
$coursedetailsarray = array();
if (count($course) > 0) {
    $coursegetstring = get_string('courses');
    $coursedetail = '<div class="divider"></div>
    <div class="container-fluid text-center course-section">
        <div class="heading-large text-center">' . $coursegetstring . '</div>
        <div class="row mx-auto my-auto">
            <div id="courseCarousel" class="carouselMultiple carousel slide w-100" data-ride="carousel">';

    foreach ($course as $key => $coursevalue) {
        $coursedetailsarray[$key]["courseid"] = $CFG->wwwroot."/course/view.php?id=".$coursevalue->id;
        $coursedetailsarray[$key]["coursename"] = $coursevalue->fullname;
        $coursecontext = context_course::instance($coursevalue->id);
        $isfile = $DB->get_records_sql("Select * from {files} where contextid = ? and filename != ? and filearea = ?",
            array($coursecontext->id, ".", "overviewfiles"));
        if ( $isfile ) {
            foreach ($isfile as $key1 => $isfilevalue) {
                $courseimage = $CFG->wwwroot . "/pluginfile.php/" . $isfilevalue->contextid .
                "/" . $isfilevalue->component . "/" . $isfilevalue->filearea . "/" . $isfilevalue->filename;
            }
        }
        if ( !empty( $courseimage ) ) {
            $coursedetailsarray[$key]["courseimage"] = $courseimage;
        } else {
            $coursedetailsarray[$key]["courseimage"] = $CFG->wwwroot."/theme/roshnilite/pix/nopic.jpg";
        }
        $courseimage = '';
    }

    $coursedetail .= '<div class="carousel-inner w-100" role="listbox">';
    $a = 0;
    foreach ($coursedetailsarray as $avlcoursearrayvalue) {
        if ($a == 0) {
            $active = 'active';
        } else {
            $active = '';
        }
        $a++;
        $coursedetail .= '<div class="carousel-item ' . $active . '">
        <div class="course-grid">';
        if ( !empty ($avlcoursearrayvalue["courseimage"]) ) {
            $coursedetail .=
            '<img width="900" height="1200" class="img-fluid" src="' . $avlcoursearrayvalue["courseimage"] . '"  alt="courseimage" />';
        }
        if ( !empty ($avlcoursearrayvalue["coursename"]) ) {
            $coursedetail .= '<div class="mask"></div>
            <a class="av-course-item-cont" href="' . 
                           $avlcoursearrayvalue["courseid"] . '">' . 
                           $avlcoursearrayvalue["coursename"] . '</a>';
        }
        $coursedetail .= '</div>
                    </div>';
    }
    $coursedetail .= '</div>
                    </div>
                    <a class="carousel-control-prev" href="#courseCarousel" role="button" data-slide="prev">
                    <div class="round-arrow">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </div>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#courseCarousel" role="button" data-slide="next">
                    <div class="round-arrow-next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </div>
                    <span class="sr-only">Next</span>
                </a>

                </div>
        </div>
    </div>';
} else {
    $coursedetail = '';
}
$allcourse = get_string('viewallcourses');

$masonryheading = format_text((!empty($html->masonryheading)) ? $html->masonryheading :
    get_string('masonryheadingdefault', 'theme_roshnilite'), "", $roshniliteformatoptions);

$masonrysubheading1 = format_text((!empty($html->masonrysubheading1)) ? $html->masonrysubheading1 :
    get_string('masonrysubheadingdefault', 'theme_roshnilite'), "", $roshniliteformatoptions);

$categorydetails = '';
$categories = $DB->get_records('course_categories');

if (!empty( $categories ) && count($categories) > 1) {
    $categorydetails .= '<div class="container text-center our-category">
  <div class="heading-large text-center">' . $masonryheading . '</div>
  <div class="header-small text-center">' . $masonrysubheading1 . '</div>
    <div class="row mx-auto my-auto">
        <div id="categoryCarousel" class="carouselMultiple carousel slide w-100" data-ride="carousel">
            <div class="carousel-inner w-100" role="listbox">';
    $a = 0;
    foreach ($categories as $cat) {
        $active = ($a == 0) ? 'active' : '';
        $a++;
        $catdes = (strip_tags($cat->description));
        $categorydetails .= '<div class="carousel-item ' . $active . '">
                    <div class="categories-item item">
                      <div class="item-inner media align-items-center">
                          <div class="icon-holder">
                          </div>
                          <div class="media-body">
                              <h5 class="title">' . $cat->name . '</h5>
                              <div class="desc">' . $catdes . '</div>
                              <a class="item-link" href="' . $CFG->wwwroot . '/course/index.php?categoryid=' . $cat->id . '"></a>
                          </div>
                        </div>
                    </div>
                </div>';
    }
    $categorydetails .= '</div>
                <a class="carousel-control-prev" href="#categoryCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#categoryCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
    <div class="divider"></div>';
}

$checkfacultycount = $PAGE->theme->setting_file_url('facultycount', 'facultycount');

$facultydetails = '';
if (!empty($checkfacultycount)) {

    $facultyimagecheck = $PAGE->theme->setting_file_url('facultyimage1', 'facultyimage1');
    $facultynamecheck = get_config('theme_roshnilite', 'facultyname1');
    $facultysubtextcheck = get_config('theme_roshnilite', 'facultysubtext1');
    if (!empty($facultyimagecheck) || !empty($facultynamecheck) || !empty($facultysubtextcheck)) {

        $facultyheading = format_text(get_string('facultyheading', 'theme_roshnilite'), "", $roshniliteformatoptions);
        $start = strlen($checkfacultycount) - 1;
        $str1 = '';

        for ($x = $start; $x < strlen($checkfacultycount); $x++) {
            $str1 = $checkfacultycount[$x];
        }
        $str1 = (int)$str1;
        $facultydetails = '<div class="container text-center top-faculti">
        <div class="heading-large text-center">' . $facultyheading . '</div>
        <div class="row mx-auto my-auto">
            <div id="facultyCarousel" class="carouselMultiple carousel slide w-100" data-ride="carousel">

                <div class="carousel-inner w-100" role="listbox">';

        for ($i = 1; $i <= $str1; $i++) {
            $facultyimage = $PAGE->theme->setting_file_url('facultyimage' . $i, 'facultyimage' . $i);
            if ($facultyimage == '') {
                $facultyimage = $CFG->wwwroot."/theme/roshnilite/pix/nopic.jpg";
            }
            $facultyname = get_config('theme_roshnilite', 'facultyname' . $i);
            $facultysubtext = get_config('theme_roshnilite', 'facultysubtext' . $i);
            $facultyfburl = get_config('theme_roshnilite', 'facultyfburl' . $i);
            $facultylnkdnurl = get_config('theme_roshnilite', 'facultylnkdnurl' . $i);
            $facultygoogleurl = get_config('theme_roshnilite', 'facultygoogleurl' . $i);
            $facultytwitterurl = get_config('theme_roshnilite', 'facultytwitterurl' . $i);
            $active = ($i == 1) ? 'active' : '';
            $facultydetails .= '<div class="carousel-item ' . $active . '">
                        <div class="instructor-block col-md-6">
                            <div class="instructor-block-left">
                                <img src="' . $facultyimage.'" alt="' . $facultyname . '" />
                            </div>
                            <div class="instructor-block-right">
                                <div class="instroctor-name">' . $facultyname . '</div>
                                <div class="instructor-desc"><p>' . $facultysubtext . '</p></div>
                                <ul class="social-network social-circle">
                                    <li><a href="' . $facultyfburl . '"
                                    class="icoFacebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="' . $facultytwitterurl . '"
                                    class="icoTwitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="' . $facultygoogleurl . '"
                                    class="icoGoogle" title="Google +"><i class="fa fa-google-plus"></i></a></li>
                                    <li><a href="' . $facultylnkdnurl . '"
                                    class="icoLinkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>';
        }
    }
    $facultydetails .= '
          </div>
          <a class="carousel-control-prev" href="#facultyCarousel" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#facultyCarousel" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
        </div>
    </div>';
} else {
    $facultydetails = '';
}
$addressfontawesomeicon = get_config('theme_roshnilite', 'addressfontawesomeicon');
$emailfontawesomeicon = get_config('theme_roshnilite', 'emailfontawesomeicon');
$phonefontawesomeicon = get_config('theme_roshnilite', 'phonefontawesomeicon');

$address = get_config('theme_roshnilite', 'address');
$email = get_config('theme_roshnilite', 'email');
$phone = get_config('theme_roshnilite', 'phone');
if (empty($addressfontawesomeicon) || empty($emailfontawesomeicon) || empty($phonefontawesomeicon) ||
    empty($address) || empty($email) || empty($phone)) {
    $addressfontawesomeicon = '<i class="fa fa-map-marker"></i>';
    $emailfontawesomeicon = '<i class="fa fa-envelope"></i>';
    $phonefontawesomeicon = '<i class="fa fa-phone"></i>';
    $address = format_text(get_string('addressdefault', 'theme_roshnilite'), "", $roshniliteformatoptions);
    $email = format_text(get_string('emaildefault', 'theme_roshnilite'), "", $roshniliteformatoptions);
    $phone = format_text(get_string('phonedefault', 'theme_roshnilite'), "", $roshniliteformatoptions);
}

$socialfontawesomeicon1 = get_config('theme_roshnilite', 'socialfontawesomeicon1');
$socialicon1 = get_config('theme_roshnilite', 'socialicon1');
$socialfontawesomeicon2 = get_config('theme_roshnilite', 'socialfontawesomeicon2');
$socialicon2 = get_config('theme_roshnilite', 'socialicon2');
$socialfontawesomeicon3 = get_config('theme_roshnilite', 'socialfontawesomeicon3');
$socialicon3 = get_config('theme_roshnilite', 'socialicon4');
$socialfontawesomeicon4 = get_config('theme_roshnilite', 'socialfontawesomeicon4');
$socialicon4 = get_config('theme_roshnilite', 'socialicon4');

$socialheading = get_config('theme_roshnilite', 'socialheading');

if (empty($socialfontawesomeicon1) || empty($socialfontawesomeicon2) || empty($socialfontawesomeicon3) ||
    empty($socialfontawesomeicon4) || empty($socialheading)
    || empty($socialicon1) || empty($socialicon2) ||
    empty($socialicon3) || empty($socialicon4)) {

    $socialfontawesomeicon1 = '<i class="fa fa-facebook"></i>';
    $socialfontawesomeicon2 = '<i class="fa fa-twitter"></i>';
    $socialfontawesomeicon3 = '<i class="fa fa-linkedin"></i>';
    $socialfontawesomeicon4 = '<i class="fa fa-google-plus"></i>';
    $socialheading = format_text(get_string('socialheadingdefault', 'theme_roshnilite'), "", $roshniliteformatoptions);
    $socialicon1 = 'javascript:void(0);';
    $socialicon2 = 'javascript:void(0);';
    $socialicon3 = 'javascript:void(0);';
    $socialicon4 = 'javascript:void(0);';
}

$templatecontext = [
    'html' => $html,
    'output' => $OUTPUT,
    'page' => $PAGE,
    'regionbsid' => $regionbsid,
    'enablemoodlemaincontent' => $enablemoodlemaincontent,
    'context' => $context,
    'maincontent' => $maincontent,
    'bodyattributes' => $bodyattributes,
    'login' => $login,
    'logvar' => $logvar,

    'version18' => $version18,
    'imgpath' => $imgpath,
    'favicon' => $favicon,
    'backgroundimage' => $backgroundimage,

    'roshniliteformatoptions' => $roshniliteformatoptions,

    'aboutsiteimage1' => $aboutsiteimage1,
    'aboutsiteimage2' => $aboutsiteimage2,
    'aboutsiteimage3' => $aboutsiteimage3,
    'aboutsiteimage4' => $aboutsiteimage4,

    'aboutsiteheading' => $aboutsiteheading,
    'aboutsitesubheading' => $aboutsitesubheading,

    'aboutsiteurl1' => $aboutsiteurl1,
    'aboutsiteurl2' => $aboutsiteurl2,
    'aboutsiteurl3' => $aboutsiteurl3,
    'aboutsiteurl4' => $aboutsiteurl4,

    'aboutsitetext1' => $aboutsitetext1,
    'aboutsitetext2' => $aboutsitetext2,
    'aboutsitetext3' => $aboutsitetext3,
    'aboutsitetext4' => $aboutsitetext4,

    'aboutsitename1' => $aboutsitename1,
    'aboutsitename2' => $aboutsitename2,
    'aboutsitename3' => $aboutsitename3,
    'aboutsitename4' => $aboutsitename4,

    'coursedetailsarray' => $coursedetailsarray,
    'avlcoursearrayvalue' => $avlcoursearrayvalue,
    'coursegetstring' => $coursegetstring,
    'allcourse' => $allcourse,
    'coursedetail' => $coursedetail,

    'masonryheading' => $masonryheading,
    'masonrysubheading1' => $masonrysubheading1,

    'socialfontawesomeicon1' => $socialfontawesomeicon1,
    'socialfontawesomeicon2' => $socialfontawesomeicon2,
    'socialfontawesomeicon3' => $socialfontawesomeicon3,
    'socialfontawesomeicon4' => $socialfontawesomeicon4,

    'socialheading' => $socialheading,

    'socialicon1' => $socialicon1,
    'socialicon2' => $socialicon2,
    'socialicon3' => $socialicon3,
    'socialicon4' => $socialicon4,

    'addressfontawesomeicon' => $addressfontawesomeicon,
    'emailfontawesomeicon' => $emailfontawesomeicon,
    'phonefontawesomeicon' => $phonefontawesomeicon,
    'address' => $address,
    'email' => $email,
    'phone' => $phone,

    'categorydetails' => $categorydetails,
    'facultydetails' => $facultydetails,
    'sliderdetails' => $sliderdetails

];

echo $OUTPUT->render_from_template('theme_roshnilite/frontpage', $templatecontext);
