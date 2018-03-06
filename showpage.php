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
 * Display a page for a given simplelesson instance
 *
 * @package   mod_simplelesson
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir.'/resourcelib.php');
//fetch URL parameters
$courseid = required_param('courseid', PARAM_INT);
$simplelessonid = required_param('simplelessonid', PARAM_INT); 
$pageid = required_param('pageid', PARAM_INT);

$moduleinstance  = $DB->get_record('simplelesson', array('id' => $simplelessonid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('simplelesson', $simplelessonid, $courseid, false, MUST_EXIST);

//set up the page
$PAGE->set_url('/mod/simplelesson/showpage.php', 
        array('courseid' => $courseid, 
              'simplelessonid' => $simplelessonid, 
              'pageid' => $pageid));

require_login($course, true, $cm);
$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

$data = \mod_simplelesson\local\utilities::get_page_record($pageid);

// Prepare page text, re-write urls
$contextid = $modulecontext->id;
$data->pagecontents = file_rewrite_pluginfile_urls($data->pagecontents, 'pluginfile.php',
        $contextid, 'mod_simplelesson', 'pagecontents', $pageid);
$renderer = $PAGE->get_renderer('mod_simplelesson');

$page_links = \mod_simplelesson\local\utilities::fetch_page_links(
        $moduleinstance->id, $course->id, false);

// Now show this page
$data = \mod_simplelesson\local\utilities::get_page_record($pageid);
$data->pagecontents = file_rewrite_pluginfile_urls($data->pagecontents, 
        'pluginfile.php', $contextid, 'mod_simplelesson', 'pagecontents', 
        $pageid);

// Run the content through format_text to enable streaming video
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $modulecontext;
$data->pagecontents = format_text($data->pagecontents, $data->pagecontentsformat, $formatoptions);

$show_index = (int) $moduleinstance->show_index;    
echo $renderer->show_page($data, $show_index, $page_links);
echo $renderer->show_page_nav_links($data, $courseid);

// If we have the capability, show the action links
if(has_capability('mod/simplelesson:manage',$modulecontext)) {
    echo $renderer->fetch_action_links($courseid, $data);
}
echo $OUTPUT->footer();