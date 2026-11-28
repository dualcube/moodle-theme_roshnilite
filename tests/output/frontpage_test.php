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

namespace theme_roshnilite\output;

/**
 * Tests for the Roshni Lite site home output class.
 *
 * @package    theme_roshnilite
 * @category   test
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_roshnilite\output\frontpage
 */
#[\PHPUnit\Framework\Attributes\CoversClass(frontpage::class)]
final class frontpage_test extends \advanced_testcase {
    /**
     * Export the site home data as the given user.
     *
     * @return array
     */
    protected function export(): array {
        global $PAGE;
        \theme_reset_all_caches();
        $theme = \theme_config::load('roshnilite');
        return (new frontpage($theme))->export_for_template($PAGE->get_renderer('core'));
    }

    /**
     * Sections with nothing configured fall back to the language string defaults.
     */
    public function test_defaults(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $data = $this->export();

        $this->assertNull($data['slider']);
        $this->assertNull($data['faculty']);
        $this->assertNull($data['catalogue'], 'No courses, no catalogue.');
        $this->assertCount(frontpage::ABOUT_ITEMS, $data['about']['items']);
        $this->assertNull($data['about']['items'][0]['image'], 'Without an upload, the feature uses an icon.');
        $this->assertNotEmpty($data['about']['items'][0]['icon']);
        $this->assertSame(get_string('aboutsitename2default', 'theme_roshnilite'), $data['about']['items'][1]['name']);
        $this->assertNull($data['contact'], 'No contact details are invented.');
        $this->assertSame([], $data['social']['links'], 'No social links lead nowhere.');
        $this->assertNull($data['featured']);
        $this->assertNull($data['preview'], 'No courses, no course preview.');
        $this->assertNull($data['announcement']);
        $this->assertNull($data['teachers']);
        $this->assertSame(get_string('courses'), $data['footer']['links'][0]['text']);
        $this->assertStringEndsWith('/course/search.php', $data['search']['action']);
    }

    /**
     * Visitors are offered sign up only when self-registration is on.
     */
    public function test_signup_for_visitors(): void {
        $this->resetAfterTest();
        $this->setUser(null);

        $data = $this->export();
        $this->assertNull($data['cta']['signupurl']);

        set_config('registerauth', 'email');
        $data = $this->export();
        $this->assertStringEndsWith('/login/signup.php', $data['cta']['signupurl']);
    }

    /**
     * The hero numbers count visible courses and categories, and are only shown once big enough.
     */
    public function test_stats(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $generator->create_course();
        $generator->create_course(['visible' => 0]);

        $this->assertSame([], $this->export()['stats'], 'One course and one category make the site look empty.');

        for ($i = 0; $i < frontpage::STAT_MINIMUMS['courses']; $i++) {
            $generator->create_course();
        }
        for ($i = 1; $i < frontpage::STAT_MINIMUMS['categories']; $i++) {
            $generator->create_category();
        }
        $stats = $this->export()['stats'];

        $this->assertCount(2, $stats, 'Learners stay hidden below their minimum.');
        $this->assertEquals(frontpage::STAT_MINIMUMS['courses'] + 1, $stats[0]['value']);
        $this->assertEquals(frontpage::STAT_MINIMUMS['categories'], $stats[1]['value']);
    }

    /**
     * Only courses the user can see are listed, with names formatted once.
     */
    public function test_courses_respect_visibility(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['fullname' => 'Visible <b>course</b> & more',
            'summary' => '<p>Learn <strong>everything</strong>.</p>']);
        $generator->create_course(['fullname' => 'Hidden course', 'visible' => 0]);
        $teacher = $generator->create_user(['firstname' => 'Jane', 'lastname' => 'Doe']);
        $generator->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($generator->create_user());

        $data = $this->export();

        $this->assertCount(1, $data['catalogue']['subjects']);
        $this->assertFalse($data['catalogue']['hastabs'], 'One subject needs no tabs.');
        $card = $data['catalogue']['subjects'][0]['courses'][0];
        $this->assertCount(1, $data['catalogue']['subjects'][0]['courses']);
        $this->assertSame('Visible course & more', $card['name'], 'Tags stripped, not escaped twice.');
        $this->assertNull($card['image'], 'Without an upload the course gets a drawn cover.');
        $this->assertSame('VC', $card['monogram']);
        $this->assertContains($card['pattern'], course_cover::PATTERNS);
        $this->assertContains($card['tone'], course_cover::TONES);
        $this->assertSame('Learn everything.', $card['summary']);
        $this->assertSame('Jane Doe', $card['teacher']['name']);
        $this->assertSame('JD', $card['teacher']['initials']);
        $this->assertTrue($card['isnew']);
        $this->assertSame($card, $data['featured'], 'The first course is featured in the hero.');
        $this->assertCount(1, $data['preview']['courses']);
        $this->assertSame(
            $data['catalogue']['subjects'][0]['name'],
            $data['preview']['courses'][0]['subject'],
            'Preview rows name their subject.'
        );
        $this->assertSame(
            ['name' => $card['name'], 'url' => $card['url']],
            $data['announcement'],
            'A new course is announced in the hero.'
        );
        $this->assertNull($data['teachers'], 'One teacher is not shown as a faculty.');
    }

    /**
     * Subjects are the categories that hold courses, one tab each; empty ones are left out.
     */
    public function test_catalogue_subjects(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $science = $generator->create_category(['name' => 'Science & Maths', 'description' => '<p>Physics</p>']);
        $arts = $generator->create_category(['name' => 'Arts']);
        $generator->create_category(['name' => 'Empty']);
        $generator->create_course(['category' => $science->id, 'fullname' => 'Optics']);
        $generator->create_course(['category' => $arts->id, 'fullname' => 'Painting']);
        $generator->create_course(['category' => $arts->id, 'fullname' => 'Sculpture']);

        $data = $this->export();
        $subjects = $data['catalogue']['subjects'];

        $this->assertTrue($data['catalogue']['hastabs']);
        $this->assertSame(['Science & Maths', 'Arts'], array_column($subjects, 'name'));
        $this->assertTrue($subjects[0]['active']);
        $this->assertFalse($subjects[1]['active']);
        $this->assertSame('Physics', $subjects[0]['description']);
        $this->assertSame(get_string('coursecountone', 'theme_roshnilite'), $subjects[0]['coursecount']);
        $this->assertSame(get_string('coursecount', 'theme_roshnilite', 2), $subjects[1]['coursecount']);
        $this->assertSame(course_cover::tone($science->id), $subjects[0]['tone']);
        $this->assertSame(
            [$subjects[0]['courses'][0]['name'], $subjects[1]['courses'][0]['name']],
            array_slice(array_column($data['preview']['courses'], 'name'), 0, 2),
            'The preview shows one course per subject first.'
        );
        $this->assertCount(2, $data['preview']['courses'], 'The first subject has no more courses to add.');
        $this->assertTrue($data['preview']['courses'][1]['highlight']);
    }

    /**
     * Cards only state facts that hold: learners once worth showing, and "Free" for open self enrolment.
     */
    public function test_course_card_facts(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $open = $generator->create_course(['fullname' => 'Open']);
        $closed = $generator->create_course(['fullname' => 'Closed']);
        $DB->set_field('course', 'timecreated', time() - 90 * DAYSECS, ['id' => $closed->id]);
        $DB->set_field('enrol', 'status', ENROL_INSTANCE_ENABLED, ['courseid' => $open->id, 'enrol' => 'self']);
        $DB->set_field('enrol', 'customint6', 1, ['courseid' => $open->id, 'enrol' => 'self']);
        for ($i = 0; $i < frontpage::MIN_LEARNERS_SHOWN; $i++) {
            $generator->enrol_user($generator->create_user()->id, $open->id, 'student');
        }
        $generator->enrol_user($generator->create_user()->id, $closed->id, 'student');

        $cards = [];
        foreach ($this->export()['catalogue']['subjects'][0]['courses'] as $card) {
            $cards[$card['name']] = $card;
        }

        $learners = get_string('learnerscount', 'theme_roshnilite', frontpage::MIN_LEARNERS_SHOWN);
        $this->assertSame($learners, $cards['Open']['learners']);
        $this->assertSame(get_string('pricefree', 'theme_roshnilite'), $cards['Open']['price']);
        $this->assertNull($cards['Closed']['learners'], 'One learner is not worth showing.');
        $this->assertNull($cards['Closed']['price'], 'Without self enrolment there is no price to state.');
        $this->assertFalse($cards['Closed']['isnew']);
    }

    /**
     * Moodle's own full course list and course search give way to the catalogue and hero search;
     * other site home items stay.
     */
    public function test_core_course_list_gives_way(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->getDataGenerator()->create_course();
        set_config('moodlemaincontentinfrontpage', 1, 'theme_roshnilite');

        $CFG->frontpageloggedin = FRONTPAGEALLCOURSELIST . ',' . FRONTPAGECOURSESEARCH;
        $data = $this->export();
        $this->assertFalse($data['showmaincontent'], 'Nothing else would be left to show.');

        $CFG->frontpageloggedin = FRONTPAGEALLCOURSELIST . ',' . FRONTPAGENEWS;
        $data = $this->export();
        $this->assertTrue($data['showmaincontent']);
        $this->assertTrue($data['hidecorecourselist']);
    }

    /**
     * "How it works" and the questions use their defaults, can be switched off, and skip cleared questions.
     */
    public function test_steps_and_questions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $data = $this->export();
        $this->assertCount(frontpage::STEPS, $data['steps']['items']);
        $this->assertSame(get_string('steptitle1default', 'theme_roshnilite'), $data['steps']['items'][0]['title']);
        $this->assertCount(frontpage::QUESTIONS, $data['faq']['items']);
        $this->assertSame(get_string('faqquestion1default', 'theme_roshnilite'), $data['faq']['items'][0]['question']);

        set_config('faqquestion2', '', 'theme_roshnilite');
        set_config('showhowitworks', 0, 'theme_roshnilite');
        $data = $this->export();
        $this->assertNull($data['steps']);
        $this->assertCount(frontpage::QUESTIONS - 1, $data['faq']['items'], 'A cleared question is not shown.');

        set_config('showfaq', 0, 'theme_roshnilite');
        $this->assertNull($this->export()['faq']);
    }

    /**
     * Upcoming events list future site events only, and the section is hidden without any.
     */
    public function test_events(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        $this->assertNull($this->export()['events']);

        $generator = $this->getDataGenerator();
        $generator->create_event(['name' => 'Open day', 'eventtype' => 'site', 'courseid' => SITEID,
            'timestart' => time() + DAYSECS, 'location' => 'Main hall']);
        $generator->create_event(['name' => 'Past day', 'eventtype' => 'site', 'courseid' => SITEID,
            'timestart' => time() - DAYSECS]);
        $generator->create_event(['name' => 'Course only', 'eventtype' => 'course', 'courseid' => $course->id,
            'timestart' => time() + DAYSECS]);

        $events = $this->export()['events'];
        $this->assertSame(['Open day'], array_column($events['items'], 'name'));
        $this->assertSame('Main hall', $events['items'][0]['location']);
    }

    /**
     * Testimonials appear only once an admin has entered a quote.
     */
    public function test_testimonials(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->assertNull($this->export()['testimonials'], 'No made-up quotes by default.');

        set_config('testimonialquote2', 'It changed how I study.', 'theme_roshnilite');
        set_config('testimonialname2', 'Priya Nair', 'theme_roshnilite');
        $items = $this->export()['testimonials']['items'];

        $this->assertCount(1, $items);
        $this->assertSame('It changed how I study.', $items[0]['quote']);
        $this->assertSame('PN', $items[0]['initials']);
    }

    /**
     * Configured slides and faculty members are exported, skipping empty social links.
     */
    public function test_slider_and_faculty(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('slidercount', 2, 'theme_roshnilite');
        set_config('slidertext1', '<h2>Welcome</h2>', 'theme_roshnilite');
        set_config('sliderbuttontext1', 'Start', 'theme_roshnilite');
        set_config('sliderurl1', 'https://example.com', 'theme_roshnilite');
        set_config('facultycount', 1, 'theme_roshnilite');
        set_config('facultyname1', 'Jane Doe', 'theme_roshnilite');
        set_config('facultyfburl1', 'https://facebook.com/jane', 'theme_roshnilite');
        set_config('facultytwitterurl1', 'javascript:void(0);', 'theme_roshnilite');
        set_config('socialicon1', 'https://example.com/us', 'theme_roshnilite');
        set_config('socialicon2', '#', 'theme_roshnilite');
        set_config('phone', '+1 555 0100', 'theme_roshnilite');

        $data = $this->export();

        $this->assertCount(2, $data['slider']['slides']);
        $this->assertSame('Start', $data['slider']['slides'][0]['buttontext']);
        $this->assertNull($data['slider']['slides'][1]['buttontext']);
        $this->assertSame('Jane Doe', $data['faculty']['members'][0]['name']);
        $this->assertSame('JD', $data['faculty']['members'][0]['initials']);
        $this->assertNull($data['faculty']['members'][0]['image'], 'No photo uploaded, so the initials avatar is used.');
        $this->assertTrue($data['slider']['multiple'], 'Two slides show the indicators.');
        $this->assertCount(1, $data['faculty']['members'][0]['links'], 'Placeholder links are skipped.');
        $this->assertSame(['https://example.com/us'], array_column($data['social']['links'], 'url'));
        $this->assertCount(1, $data['contact']['items'], 'Only contact details that are set are shown.');
    }

    /**
     * The hero names the people who teach here, once there are two or more.
     */
    public function test_teachers(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        foreach (['Jane Doe', 'Ravi Shah'] as $index => $name) {
            [$first, $last] = explode(' ', $name);
            $course = $generator->create_course(['fullname' => 'Course ' . $index]);
            $teacher = $generator->create_user(['firstname' => $first, 'lastname' => $last]);
            $generator->enrol_user($teacher->id, $course->id, 'editingteacher');
        }

        $teachers = $this->export()['teachers'];

        $this->assertSame(get_string('teacherscount', 'theme_roshnilite', 2), $teachers['label']);
        $this->assertEqualsCanonicalizing(['JD', 'RS'], array_column($teachers['avatars'], 'initials'));
    }
}
