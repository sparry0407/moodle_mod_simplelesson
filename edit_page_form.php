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
 * Form for editing lesson pages
 *
 * @package   mod_simplelesson
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../lib/formslib.php');
require_once('lib.php');
/**
 * Define the edit page form elements
 * Elements I will need - title, content editor, question picker, [file picker?]
 */
class simplelesson_edit_page_form extends moodleform {

    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('text', 'pagetitle', get_string('pagetitle', MOD_SIMPLELESSON_LANG), array('size'=>'64'));
        $mform->addRule('pagetitle', null, 'required', null, 'client');
        // $mform->addHelpButton('pagetitle', 'pagetitle', MOD_SIMPLELESSON_LANG);
        $mform->setType('pagetitle', PARAM_TEXT);                     
                                                        
        // First page text - editor field
        $context = $this->_customdata['context'];
        $editpageoptions = simplelesson_get_editor_options($context);
        $mform->addElement('editor', 'pagecontents_editor', 
                get_string('pagecontents', MOD_SIMPLELESSON_LANG), 
                null, $editpageoptions);
        $mform->setType('pagecontents_editor', PARAM_RAW);
        $mform->addRule('pagecontents_editor', get_string('required'), 
                'required', null, 'client');

        // To add, question picker
        // need a utility function to scan the question bank

        // To add link data
        // If this is first page then just a link to home, otherwise a list
        // of available page titles to link to.

        $this->add_action_buttons($cancel=true);
    }
}
