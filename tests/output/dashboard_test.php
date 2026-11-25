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
 * Tests for the Roshni Lite dashboard header.
 *
 * @package    theme_roshnilite
 * @category   test
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_roshnilite\output\dashboard
 */
#[\PHPUnit\Framework\Attributes\CoversClass(dashboard::class)]
final class dashboard_test extends \advanced_testcase {
    /**
     * Export the dashboard header for a user.
     *
     * @param \stdClass $user
     * @return array
     */
    protected function export(\stdClass $user): array {
        global $PAGE;
        $this->setUser($user);
        return (new dashboard($user))->export_for_template($PAGE->get_renderer('core'));
    }

    /**
     * A user with no enrolments gets zero stats, the empty state and the quick actions.
     */
    public function test_no_courses(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user(['firstname' => 'Sam']);

        $data = $this->export($user);

        $this->assertStringContainsString('Sam', $data['greeting']);
        $this->assertFalse($data['hascourses']);
        $this->assertSame([], $data['continue']);
        $this->assertSame([0, 0, 0, 0], array_column($data['stats'], 'value'));
        $this->assertCount(4, $data['actions']);
    }

    /**
     * Enrolled courses are counted and offered under "Continue learning", most recently accessed first.
     */
    public function test_enrolled_courses(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $courses = [];
        foreach (['Alpha', 'Beta', 'Gamma', 'Delta'] as $name) {
            $course = $generator->create_course(['fullname' => $name, 'startdate' => time() - DAYSECS]);
            $generator->enrol_user($user->id, $course->id, 'student');
            $courses[$name] = $course;
        }
        $DB->insert_record('user_lastaccess', (object) [
            'userid' => $user->id,
            'courseid' => $courses['Gamma']->id,
            'timeaccess' => time(),
        ]);

        $data = $this->export($user);

        $this->assertTrue($data['hascourses']);
        $this->assertEquals(4, $data['stats'][0]['value']);
        $this->assertEquals(4, $data['stats'][1]['value']);
        $this->assertCount(dashboard::CONTINUE_COURSES, $data['continue']);
        $this->assertSame('Gamma', $data['continue'][0]['name']);
        $this->assertNotEmpty($data['continue'][0]['image']);
    }
}
