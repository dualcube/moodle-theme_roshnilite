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

use core_course\external\course_summary_exporter;

/**
 * The cover shown for a course on the site home and the Dashboard.
 *
 * A course image uploaded by the teacher is used as it is. Without one, the
 * theme draws a cover: a geometric pattern in its subject's colour, varied by
 * course, with the course's monogram as a small badge, so a catalogue of new
 * courses still looks intentional.
 *
 * @package    theme_roshnilite
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_cover {
    /** @var string[] Colour tones, picked per subject so a subject keeps its colour everywhere. */
    const TONES = ['primary', 'amber', 'emerald', 'sky', 'violet', 'pink'];

    /** @var string[] Cover patterns, picked per course so courses in a subject differ. */
    const PATTERNS = ['rings', 'grid', 'arcs', 'stripes', 'dots'];

    /**
     * The colour tone of a course category.
     *
     * @param int $categoryid
     * @return string
     */
    public static function tone(int $categoryid): string {
        return self::TONES[$categoryid % count(self::TONES)];
    }

    /**
     * Up to two capital letters from the first words of a name.
     *
     * @param string $name A formatted, plain-text name.
     * @return string
     */
    public static function monogram(string $name): string {
        $initials = '';
        foreach (preg_split('/\s+/u', trim(strip_tags($name)), -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $letter = \core_text::substr($word, 0, 1);
            if (preg_match('/\p{L}|\p{N}/u', $letter)) {
                $initials .= \core_text::strtoupper($letter);
            }
            if (\core_text::strlen($initials) === 2) {
                break;
            }
        }
        return $initials;
    }

    /**
     * Cover data for a course.
     *
     * @param \stdClass|\core_course_list_element $course
     * @param string $name The course's formatted full name.
     * @return array image (URL or null), tone, pattern and monogram.
     */
    public static function export($course, string $name): array {
        $image = course_summary_exporter::get_course_image($course);
        return [
            'image' => $image ?: null,
            'tone' => self::tone((int) $course->category),
            'pattern' => self::PATTERNS[(int) $course->id % count(self::PATTERNS)],
            'monogram' => self::monogram($name),
        ];
    }
}
