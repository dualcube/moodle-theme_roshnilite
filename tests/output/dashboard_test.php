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
        $this->assertNull($data['featured']);
        $this->assertContains($data['daypart'], ['morning', 'afternoon', 'evening']);
        $this->assertSame([0, 0, 0, 0], array_column($data['stats'], 'value'));
        $this->assertCount(4, $data['actions']);
    }

    /**
     * Enrolled courses are counted, and the most recently accessed one is offered to jump back into.
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
        $this->assertSame('Gamma', $data['featured']['name']);
        $this->assertNull($data['featured']['image'], 'Without an upload the course gets a typographic cover.');
        $this->assertSame('G', $data['featured']['monogram']);
        $this->assertArrayNotHasKey('others', $data, 'The course list is left to the Course overview block.');
    }

    /**
     * Activities due in the next week are counted; later ones are not.
     */
    public function test_deadlines(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course(['fullname' => 'Biology']);
        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->create_module('assign', ['course' => $course->id, 'name' => 'Later essay', 'duedate' => time() + 3 * DAYSECS]);
        $generator->create_module('assign', ['course' => $course->id, 'name' => 'Soon essay', 'duedate' => time() + DAYSECS]);
        $generator->create_module('assign', ['course' => $course->id, 'name' => 'Far essay', 'duedate' => time() + 30 * DAYSECS]);

        $data = $this->export($user);

        $this->assertEquals(2, $data['stats'][3]['value']);
        $this->assertArrayNotHasKey('deadlines', $data, 'The list is left to the Timeline block.');
    }
}
