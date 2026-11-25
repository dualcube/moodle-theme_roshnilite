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

/**
 * The personal header shown above the Dashboard blocks: greeting, learning
 * stats, the course to jump back into and shortcuts.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dashboard implements renderable, templatable {
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

        $courses = enrol_get_all_users_courses($this->user->id, true, 'summary, summaryformat, enablecompletion');
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

        $daypart = $this->daypart();
        $deadlines = $this->get_deadlines();

        return [
            'daypart' => $daypart,
            'dayparticon' => ['morning' => 'fa-sun', 'afternoon' => 'fa-cloud-sun', 'evening' => 'fa-moon'][$daypart],
            'greeting' => get_string('greeting' . $daypart, 'theme_roshnilite', s($this->user->firstname)),
            'date' => userdate(time(), get_string('strftimedaydate', 'langconfig')),
            'avatar' => $userpicture->get_url($PAGE, $output)->out(false),
            'stats' => [
                ['value' => count($courses), 'label' => get_string('statenrolled', 'theme_roshnilite'),
                    'icon' => 'fa-book-open', 'tone' => 'primary'],
                ['value' => $inprogress, 'label' => get_string('statinprogress', 'theme_roshnilite'),
                    'icon' => 'fa-person-running', 'tone' => 'amber'],
                ['value' => $completed, 'label' => get_string('statcompleted', 'theme_roshnilite'),
                    'icon' => 'fa-circle-check', 'tone' => 'emerald'],
                ['value' => count($deadlines), 'label' => get_string('statdeadlines', 'theme_roshnilite'),
                    'icon' => 'fa-calendar-day', 'tone' => 'sky'],
            ],
            'actions' => $this->export_actions(),
            // One course to pick up. The full list and the deadlines are the
            // Course overview and Timeline blocks' job, so they are not repeated here.
            'featured' => $this->export_featured($courses, $progress),
            'hascourses' => !empty($courses),
            'browseurl' => (new url('/course/index.php'))->out(false),
        ];
    }

    /**
     * The part of the day in the user's timezone: morning, afternoon or evening.
     *
     * @return string
     */
    protected function daypart(): string {
        $hour = (int) userdate(time(), '%H', $this->timezone(), false);
        if ($hour >= 5 && $hour < 12) {
            return 'morning';
        }
        return $hour >= 12 && $hour < 18 ? 'afternoon' : 'evening';
    }

    /**
     * The dashboard owner's timezone, as userdate() expects it.
     *
     * @return string
     */
    protected function timezone(): string {
        return \core_date::get_user_timezone($this->user->timezone ?? 99);
    }

    /**
     * Activities with a due date in the next week, from the calendar's action events.
     *
     * @return \core_calendar\local\event\entities\action_event_interface[]
     */
    protected function get_deadlines(): array {
        try {
            return array_values(\core_calendar\local\api::get_action_events_by_timesort(
                time(),
                time() + DAYSECS * self::DEADLINE_DAYS,
                null,
                50,
                true,
                $this->user
            ));
        } catch (\Throwable $e) {
            return [];
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
     * The course to jump back into: the most recently accessed one, or else the first enrolment.
     *
     * @param array $courses The user's enrolled courses, keyed by id.
     * @param array $progress Completion percentage (or null) per course id.
     * @return array|null
     */
    protected function export_featured(array $courses, array $progress): ?array {
        if (!$courses) {
            return null;
        }
        $course = reset($courses);
        foreach (course_get_recent_courses($this->user->id, 1) as $recent) {
            $course = $courses[$recent->id] ?? $course;
        }

        $context = \context_course::instance($course->id);
        $category = \core_course_category::get($course->category, IGNORE_MISSING);
        $percentage = $progress[$course->id] ?? null;
        $name = format_string($course->fullname, true, ['context' => $context, 'escape' => false]);
        return course_cover::export($course, $name) + [
            'name' => $name,
            'summary' => $this->summary($course, $context),
            'category' => $category ? $category->get_formatted_name(['escape' => false]) : '',
            'url' => (new url('/course/view.php', ['id' => $course->id]))->out(false),
            'hasprogress' => $percentage !== null,
            'progress' => $percentage === null ? 0 : (int) floor($percentage),
        ];
    }

    /**
     * A short plain-text course summary.
     *
     * @param \stdClass $course
     * @param \context_course $context
     * @return string
     */
    protected function summary(\stdClass $course, \context_course $context): string {
        $summary = file_rewrite_pluginfile_urls($course->summary ?? '', 'pluginfile.php', $context->id, 'course', 'summary', null);
        $summary = format_text($summary, $course->summaryformat ?? FORMAT_HTML, ['context' => $context]);
        return frontpage::plain_text($summary, 140);
    }
}
