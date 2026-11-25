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

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use core\url;
use core_course\external\course_summary_exporter;

/**
 * The personal header shown above the Dashboard blocks: greeting, learning
 * stats, quick actions and the courses to continue.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dashboard implements renderable, templatable {
    /** @var int How many courses are offered under "Continue learning". */
    const CONTINUE_COURSES = 3;

    /** @var int Days ahead counted as upcoming deadlines. */
    const DEADLINE_DAYS = 7;

    /** @var \stdClass The user the dashboard belongs to. */
    protected \stdClass $user;

    /**
     * Constructor.
     *
     * @param \stdClass $user The user the dashboard belongs to.
     */
    public function __construct(\stdClass $user) {
        $this->user = $user;
    }

    /**
     * Export the data for the dashboard header template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/course/lib.php');

        $courses = enrol_get_all_users_courses($this->user->id, true, null);
        $inprogress = 0;
        $completed = 0;
        $progress = [];
        foreach ($courses as $course) {
            $percentage = \core_completion\progress::get_course_progress_percentage($course, $this->user->id);
            $progress[$course->id] = $percentage;
            if ($percentage !== null && $percentage >= 100) {
                $completed++;
            } else if ($course->startdate <= time() && (empty($course->enddate) || $course->enddate > time())) {
                $inprogress++;
            }
        }

        $userpicture = new \core\output\user_picture($this->user);
        $userpicture->size = 100;

        return [
            'greeting' => get_string($this->greeting_key(), 'theme_roshnilite', s($this->user->firstname)),
            'date' => userdate(time(), get_string('strftimedaydate', 'langconfig')),
            'avatar' => $userpicture->get_url($PAGE, $output)->out(false),
            'stats' => [
                ['value' => count($courses), 'label' => get_string('statenrolled', 'theme_roshnilite'),
                    'icon' => 'fa-book-open', 'tone' => 'primary'],
                ['value' => $inprogress, 'label' => get_string('statinprogress', 'theme_roshnilite'),
                    'icon' => 'fa-person-running', 'tone' => 'amber'],
                ['value' => $completed, 'label' => get_string('statcompleted', 'theme_roshnilite'),
                    'icon' => 'fa-circle-check', 'tone' => 'emerald'],
                ['value' => $this->count_deadlines(), 'label' => get_string('statdeadlines', 'theme_roshnilite'),
                    'icon' => 'fa-calendar-day', 'tone' => 'sky'],
            ],
            'actions' => $this->export_actions(),
            'continue' => $this->export_continue($courses, $progress, $output),
            'hascourses' => !empty($courses),
            'browseurl' => (new url('/course/index.php'))->out(false),
        ];
    }

    /**
     * The greeting string for the time of day in the user's timezone.
     *
     * @return string
     */
    protected function greeting_key(): string {
        $hour = (int) userdate(time(), '%H', \core_date::get_user_timezone($this->user), false);
        if ($hour < 12) {
            return 'greetingmorning';
        }
        return $hour < 18 ? 'greetingafternoon' : 'greetingevening';
    }

    /**
     * Activities with a due date in the next week, from the calendar's action events.
     *
     * @return int
     */
    protected function count_deadlines(): int {
        try {
            $events = \core_calendar\local\api::get_action_events_by_timesort(
                time(),
                time() + DAYSECS * self::DEADLINE_DAYS,
                null,
                50,
                false,
                $this->user
            );
            return count($events);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Shortcuts to the pages learners use most.
     *
     * @return array
     */
    protected function export_actions(): array {
        $actions = [
            ['url' => new url('/my/courses.php'), 'label' => get_string('mycourses'), 'icon' => 'fa-graduation-cap'],
            ['url' => new url('/calendar/view.php', ['view' => 'month']), 'label' => get_string('calendar', 'calendar'),
                'icon' => 'fa-calendar'],
            ['url' => new url('/grade/report/overview/index.php'), 'label' => get_string('grades'), 'icon' => 'fa-chart-line'],
            ['url' => new url('/message/index.php'), 'label' => get_string('messages', 'message'), 'icon' => 'fa-comments'],
        ];
        return array_map(function (array $action): array {
            $action['url'] = $action['url']->out(false);
            return $action;
        }, $actions);
    }

    /**
     * The courses to continue: recently accessed first, then other current enrolments.
     *
     * @param array $courses The user's enrolled courses, keyed by id.
     * @param array $progress Completion percentage (or null) per course id.
     * @param renderer_base $output
     * @return array
     */
    protected function export_continue(array $courses, array $progress, renderer_base $output): array {
        $ordered = [];
        foreach (course_get_recent_courses($this->user->id, self::CONTINUE_COURSES) as $recent) {
            if (isset($courses[$recent->id])) {
                $ordered[$recent->id] = $courses[$recent->id];
            }
        }
        foreach ($courses as $id => $course) {
            if (count($ordered) >= self::CONTINUE_COURSES) {
                break;
            }
            $ordered[$id] = $ordered[$id] ?? $course;
        }

        $items = [];
        foreach ($ordered as $course) {
            $context = \context_course::instance($course->id);
            $category = \core_course_category::get($course->category, IGNORE_MISSING);
            $percentage = $progress[$course->id] ?? null;
            $items[] = [
                'name' => format_string($course->fullname, true, ['context' => $context]),
                'category' => $category ? $category->get_formatted_name() : '',
                'url' => (new url('/course/view.php', ['id' => $course->id]))->out(false),
                'image' => course_summary_exporter::get_course_image($course) ?: $output->get_generated_image_for_id($course->id),
                'hasprogress' => $percentage !== null,
                'progress' => $percentage === null ? 0 : (int) floor($percentage),
            ];
        }
        return $items;
    }
}
