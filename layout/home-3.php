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
global $DB, $USER, $CFG, $PAGE;

$PAGE->requires->css('/theme/roshnilite/css/font-awesome.min.css', true);
$PAGE->requires->css('/theme/roshnilite/css/styles.css');
$PAGE->requires->js('/theme/roshnilite/js/jquery-1.11.1.min.js', true);
$PAGE->requires->js('/theme/roshnilite/js/bootstrap.min.js', true);
$PAGE->requires->js('/theme/roshnilite/js/jquery.bxslider.min.js', true);
$PAGE->requires->js('/theme/roshnilite/js/jquery.scroll.js', true);
$PAGE->requires->js('/theme/roshnilite/js/engine.js', true);
$PAGE->requires->js('/theme/roshnilite/js/backtop.js', true);
if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-bs-main-and-pre';
}
echo $OUTPUT->doctype()
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
	<head>
		<title><?php echo $OUTPUT->page_title(); ?></title>
		<?php echo $OUTPUT->standard_head_html() ?>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link type="image/x-icon" rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>">
	</head>
	<body class="header-3">
		<?php echo $OUTPUT->standard_top_of_body_html() ?>
		<header id="header">
				<div class="main-menu header3">
					<div class="container">
						<a href="<?php echo $CFG->wwwroot;?>" class="inner-logo logo-text"></a>
						<?php if (isguestuser()) {?>
							<div class="usermenu">
								<div>
									<ul class="menubar">
										<li>
											<a href="javascript:void(0);">
												<span class="userbutton">
													<span>
														<span class="avatar current">
																<?php echo $OUTPUT->user_profile_picture(); ?>
														</span>
													</span>
													<span>Hi, <?php echo $USER->firstname ." ". $USER->lastname; ?></span>
												</span>
											</a>
										</li>
									</ul>
									<ul class="menu">
										<li>
											<a href="<?php echo $CFG->wwwroot; ?>/login/logout.php"><span><?php echo get_string('logout');?></span></a>
										</li>
									</ul>
								</div>
							</div>
						<?php 
} else if (isloggedin() and !isguestuser()) { ?>
							<div class="usermenu">
								<div>
									<ul class="menubar">
										<li>
											<a href="javascript:void(0);">
												<span class="userbutton">
													<span>
														<span class="avatar current">
															<?php echo $OUTPUT->user_profile_picture(); ?>
														</span>
													</span>
													<span>Hi, <?php echo $USER->firstname ." ". $USER->lastname; ?></span>
												</span>
											</a>
										</li>
									</ul>
									<ul class="menu">
										<li>
											<a href="<?php echo $CFG->wwwroot; ?>/user/edit.php"><span><?php echo get_string('profile');?></span></a>
										</li>
										<li>
											<a href="<?php echo $CFG->wwwroot.'/course/index.php';?>"><span><?php echo get_string('courses');?></span></a>
										</li>
										<li>
											<a href="<?php echo $CFG->wwwroot; ?>/login/logout.php"><span><?php echo get_string('logout');?></span></a>
										</li>
									</ul>
								</div>
							</div>
						<?php 
}
                        ?>
						<div class="navbar header3">
							<div class="navbar-inner">
								<div class="">
									<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</button>
									<div class="nav-collapse collapse">
										<ul class="nav">
											<li><a href="<?php echo $CFG->wwwroot;?>" class="active"><?php echo get_string('home');?></a></li>
											<li><a href="<?php echo $CFG->wwwroot.'/course/index.php';?>"><?php echo get_string('courses');?></a></li>
											<?php if ($CFG->enableblogs == 1) { ?><li><a href="<?php echo $CFG->wwwroot.'/blog/index.php';?>">
											<?php echo get_string('blogssite', 'blog');?></a></li><?php } ?>
											<li><a href="<?php echo $CFG->wwwroot.'/mod/forum/user.php?id='.$USER->id;?>">
											<?php echo get_string('forum', 'forum');?></a></li>
										</ul>
									</div><!--/.nav-collapse -->
								</div>
							</div><!-- END of .navbar-inner -->
						</div><!-- END of .navbar -->
					</div><!-- END of .container -->
				</div><!-- END of main-menu -->
			
			<?php if (!isloggedin()) { ?>
				<div class="header3-login">
					<div class="container">
						<form method="post" action="<?php echo $CFG->wwwroot; ?>/login/index.php?authldap_skipntlmsso=1">
							<input type="text" name="username" placeholder="Username:">
							<input type="password" name="password" placeholder="Password:">
							<input type="submit" value="LOG IN">
						</form>
					</div>
				</div>
            <?php 
}
            ?>
            <?php
                require($CFG->dirroot.'/theme/roshnilite/layout/home/firstslider.php');
            ?>
		</header><!-- END of header -->

		<div class="content">
            <?php	
                require($CFG->dirroot.'/theme/roshnilite/layout/home/aboutsite.php');
                require($CFG->dirroot.'/theme/roshnilite/layout/home/availablecourse.php');
                require($CFG->dirroot.'/theme/roshnilite/layout/home/categories.php');
                require($CFG->dirroot.'/theme/roshnilite/layout/home/contacts.php');
                require($CFG->dirroot.'/theme/roshnilite/layout/home/social_network.php');
            ?>
		</div><!-- END of .content -->
		<?php
           echo $OUTPUT->main_content(); require('footer.php'); echo $OUTPUT->standard_end_of_body_html()
        ?>
		<a href="#header" class="btn-to-top"><i class="fa fa-arrow-circle-up"></i></a>
	</body>
</html>
