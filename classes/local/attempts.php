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
 * Question attempt and related utilities for simplelesson
 *
 * @package    mod_simplelesson
 * @copyright  Richard Jones https://richardnz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use \mod_simplelesson\local\pages;
use \mod_simplelesson\local\questions;
namespace mod_simplelesson\local;
require_once('../../config.php'); 
require_once($CFG->libdir . '/questionlib.php');
//require_once('../../question/previewlib.php');
//require_once('../../question/engine/lib.php');
//use question_preview_options;
defined('MOODLE_INTERNAL') || die();
/**
 * Utility class for question usage actions
 *
 * @package    mod_simplelesson
 * @copyright  2018 Richard Jones https://richardnz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempts  {
    /** 
     * Creates the question usage for this simple lesson
     * 
     * @param $context - module context 
     * @param $behaviour - question behaviour
     * @param $entries - questions selected by user (edit.php)
     * @param $simplelessonid - module instance id
     * @return $qubaid - the id of the question engine usage.
     */
    public static function create_usage($context, 
            $behaviour, $entries, $simplelessonid) {
        global $DB;
        $quba = \question_engine::
                make_questions_usage_by_activity(
                'mod_simplelesson', 
                $context);
        $quba->set_preferred_behaviour($behaviour);
        $questions = array();
        foreach($entries as $entry) {
            
            $question_def = \question_bank::load_question($entry->qid);
            // add questions that have page id's
            if ($entry->pageid != 0) {
                $id = $quba->add_question(
                        $question_def, $entry->defaultmark);
            }
        }
        $quba->start_all_questions();
        \question_engine::
                save_questions_usage_by_activity($quba);
        $qubaid = $quba->get_id();
        $DB->set_field('simplelesson', 
                    'qubaid', $qubaid,  
                    array('id' => $simplelessonid));
        return $qubaid;
    }
    /**
     * Get the usage id for a simplelesson instance
     *
     * @param $simplelessonid - module instance id
     * @return $qubaid - the question usage id associated with this lesson
     */
    public static function get_usageid($simplelessonid) {
        global $DB;
        return $DB->get_field('simplelesson', 
                'qubaid',
                array('id' => $simplelessonid));
    }
    /**
     * Return the wanted row from question attempts
     *
     * @param $qubaid usage id
     * @param $slot question attempt slot
     * @return object corresponding row in question attempts
     */
    public static function get_question_attempt_id(
            $qubaid, $slot) {
        global $DB;
        $data = $DB->get_record('question_attempts',
                  array('questionusageid' => $qubaid, 
                  'slot' => $slot),
                  '*', MUST_EXIST);
        return $data;
    }
    /**
     * Return the wanted row from simplelesson answers
     *
     * @param $courseid int course id
     * @param $simplelessonid int id of simplelesson instance
     * @param $userid int id of simplelesson user
     * @return object corresponding row in question attempts
     */
    public static function get_lesson_answer_data($courseid, 
            $simplelessonid, $userid, $attemptid) {
        global $DB;
        // Get the records for this user on this course
        $sql = "SELECT  a.id, a.courseid, a.simplelessonid, 
                        a.attemptid, a.slqid, a.userid, 
                        a.qatid, a.starttime, 
                        a.endtime, c.fullname, u.firstname, 
                        u.lastname 
                  FROM  {simplelesson_answers} a
                  JOIN  {course} c ON c.id = a.courseid
                  JOIN  {user} u ON u.id = a.userid
                 WHERE  a.courseid = :cid 
                   AND  a.simplelessonid = :slid 
                   AND  a.userid = :uid
                   AND  a.attemptid = :aid";
        
        $answer_data = $DB->get_records_sql($sql,
                array('cid' => $courseid,
                      'slid' => $simplelessonid,
                      'uid' => $userid,
                      'aid' => $attemptid));
        // Add the data for the summary table
        foreach ($answer_data as $data) {
        
            // Get the record from the questions table
            $qdata = $DB->get_record('simplelesson_questions',
                    array('id' => $data->slqid), '*',
                    MUST_EXIST);
        
            // Add the page name
            $data->pagename = pages::get_page_title($qdata->pageid);
        
            // Add the question name
            $data->qname = questions::fetch_question_name($qdata->qid);
        
            // We'll need the slot to get the response data
            // $data->slot = $qdata->slot;
            // Get the record from the question attempt data
            $qdata = $DB->get_record('question_attempts',
                    array('id' => $data->qatid), '*',
                    MUST_EXIST);
            $data->youranswer = $qdata->responsesummary;
            $data->rightanswer = $qdata->rightanswer;
        }
        return $answer_data;        
    }
    /**
     * Make an entry in the attempts table
     *
     * @param $data data to insert (from start_attempt.php)
     * @return int record->id
     */
    public static function set_attempt_start($data) {
        global $DB;
        return $DB->insert_record(
                'simplelesson_attempts',
                $data);
    }
    /**
     * Complete an entry in the attempts table
     *
     * @param $attemptid - record id to update
     * @return int number of attempts by user
     *         on this lesson and course
     */
    public static function set_attempt_completed($attemptid) {
        global $DB;
        $DB->set_field('simplelesson_attempts',
                'status',
                MOD_SIMPLELESSON_ATTEMPT_COMPLETED,
                array('id' => $attemptid));
    }
}