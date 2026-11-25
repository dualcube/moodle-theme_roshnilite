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
        $this->assertNull($data['courses']);
        $this->assertCount(frontpage::ABOUT_ITEMS, $data['about']['items']);
        $this->assertNull($data['about']['items'][0]['image'], 'Without an upload, the feature uses an icon.');
        $this->assertNotEmpty($data['about']['items'][0]['icon']);
        $this->assertSame(get_string('aboutsitename2default', 'theme_roshnilite'), $data['about']['items'][1]['name']);
        $this->assertCount(3, $data['contact']['items']);
        $this->assertCount(frontpage::SOCIAL_LINKS, $data['social']['links']);
        $this->assertNull($data['login'], 'Logged-in users get no quick login form.');
    }

    /**
     * Visitors who are not logged in get a log in button, and sign up only when self-registration is on.
     */
    public function test_login_and_signup_for_visitors(): void {
        $this->resetAfterTest();
        $this->setUser(null);

        $data = $this->export();
        $this->assertStringContainsString('/login/index.php', $data['login']['url']);
        $this->assertNull($data['cta']['signupurl']);

        set_config('registerauth', 'email');
        $data = $this->export();
        $this->assertStringEndsWith('/login/signup.php', $data['cta']['signupurl']);
    }

    /**
     * The hero numbers count visible courses and categories.
     */
    public function test_stats(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_course(['visible' => 0]);

        $stats = $this->export()['stats'];

        $this->assertEquals(1, $stats[0]['value']);
        $this->assertEquals(1, $stats[1]['value']);
    }

    /**
     * Only courses the user can see are listed, with their names escaped.
     */
    public function test_courses_respect_visibility(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $generator->create_course(['fullname' => 'Visible <b>course</b>']);
        $generator->create_course(['fullname' => 'Hidden course', 'visible' => 0]);
        $this->setUser($generator->create_user());

        $data = $this->export();

        $names = array_column($data['courses']['items'], 'name');
        $this->assertCount(1, $names);
        $this->assertStringNotContainsString('<b>', $names[0]);
        $this->assertTrue($data['courses']['items'][0]['active']);
        $this->assertNotEmpty($data['courses']['items'][0]['category']);
        $this->assertNotEmpty($data['courses']['items'][0]['image'], 'Courses without an image get a generated one.');
    }

    /**
     * The category carousel needs at least two categories.
     */
    public function test_categories(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->assertNull($this->export()['categories']);

        $this->getDataGenerator()->create_category(['name' => 'Science', 'description' => '<p>Physics</p>']);
        $data = $this->export();

        $this->assertCount(2, $data['categories']['items']);
        $this->assertContains('Science', array_column($data['categories']['items'], 'name'));
        $this->assertContains('Physics', array_column($data['categories']['items'], 'description'));
        $science = array_values(array_filter($data['categories']['items'], fn($item) => $item['name'] === 'Science'))[0];
        $this->assertSame(get_string('coursecount', 'theme_roshnilite', 0), $science['coursecount']);
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

        $data = $this->export();

        $this->assertCount(2, $data['slider']['slides']);
        $this->assertSame('Start', $data['slider']['slides'][0]['buttontext']);
        $this->assertNull($data['slider']['slides'][1]['buttontext']);
        $this->assertSame('Jane Doe', $data['faculty']['members'][0]['name']);
        $this->assertSame('JD', $data['faculty']['members'][0]['initials']);
        $this->assertNull($data['faculty']['members'][0]['image'], 'No photo uploaded, so the initials avatar is used.');
        $this->assertTrue($data['slider']['multiple'], 'Two slides show the indicators.');
        $this->assertCount(1, $data['faculty']['members'][0]['links']);
    }
}
