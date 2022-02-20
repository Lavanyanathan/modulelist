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
 * Block for displayed logged in user's course completion status
 *
 * @package    block_modulelist
 * @copyright  2022
 * @author     Lavanya <lavanyanathan.s@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/completionlib.php");
/**
 * Course completion status.
 * Displays overall, and individual criteria status for logged in user.
 */
class block_modulelist extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_modulelist');
    }
    public function applicable_formats() {
        return array(
            'course' => true
        );
    }
    public function get_content() {
        global $USER, $DB;
        $rows  = array();
        $srows = array();
        $prows = array();
        // If content is cached.
        if ($this->content !== null) {
            return $this->content;
        }
        $course                = $this->page->course;
        $context               = context_course::instance($course->id);
        // Create empty content.
        $this->content         = new stdClass();
        $this->content->text   = '';
        $this->content->footer = '';
        $activities            = get_array_of_activities($course->id);
        if ($activities) {
            $this->content->text .= '<ul>';
            foreach ($activities as $key => $value) {
                $data = $DB->get_record('course_modules_completion', array(
                    'userid' => $USER->id,
                    'coursemoduleid' => $value->cm
                ));
                if ($data) {
                    $status = ($data->completionstate == COMPLETION_COMPLETE_PASS || COMPLETION_COMPLETE) ? ' - '
                    . get_string('completed') : '';
                } else {
                    $status = '';
                }
                $this->content->text .= '<li>' . $value->cm . ' - ' . $value->name . ' - ' .
                userdate($value->added, get_string("strftimedate")) . $status . ' </li>';
            }
            $this->content->text .= '</ul>';
        } else {
            $this->content->text .= '';
        }
        return $this->content;
    }
}
