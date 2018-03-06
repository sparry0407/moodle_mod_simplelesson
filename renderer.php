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


defined('MOODLE_INTERNAL') || die();

/**
 * A custom renderer class that extends the plugin_renderer_base.
 *
 * @package mod_simplelesson
 * @copyright 2015 Justin Hunt, modified 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_simplelesson_renderer extends plugin_renderer_base {

	/**
     * Returns the header for the module
     *
     * @param mod $instance
     * @param string $currenttab current tab that is shown.
     * @param int    $item id of the anything that needs to be displayed.
     * @param string $extrapagetitle String to append to the page title.
     * @return string
     */
    public function header($lessontitle, $activityname) {

        //$context = context_module::instance($cm->id);

        // Header setup
        $this->page->set_title($this->page->course->shortname.": ".$activityname);
        $this->page->set_heading($this->page->course->fullname);
        $output = $this->output->header();

        $output .= $this->output->heading($lessontitle);

        return $output;
    }
	
	/**
     * Return HTML to display limited header
     */
      public function notabsheader() {
      	return $this->output->header();
      }
      
     /**
     * Returns the text for the first page as defined in the
     * module's instance settings page
     * @param string $firstpage text
     * @return string
     */
    public function fetch_firstpage($firstpagetext, 
            $show_index, $page_links) {

       // Show the index if required) 
       $html = '';	        
       if ($show_index) {
            $html .= self::fetch_index($page_links);
        }
        
        // main content div on first page
        $html .= html_writer::start_div(MOD_SIMPLELESSON_CLASS . '_content');
        $html .=  '<p>' . $firstpagetext . '</p>'; 
        
        $html .= html_writer::end_div();  // page text     

        return $html;
    }
    /**
     * Returns the html for the page index
     * module's instance settings page
     * @param array $page_links for the lesson page index
     * @return string
     */
    public function fetch_index($page_links) {
        // page index
        $html = html_writer::start_div(
                MOD_SIMPLELESSON_CLASS . '_page_index_container');
        $html .= $this->output->heading(get_string('page_index_header', MOD_SIMPLELESSON_LANG), 4, 'main');
        $html .= html_writer::start_div(MOD_SIMPLELESSON_CLASS . '_page_index');
        $html .= html_writer::alist($page_links, null, 'ul');
        $html .= html_writer::end_div();  // page index      
        $html .= html_writer::end_div();  // container

        return $html;
    }
    
    /**
     * Returns the link to the lesson's real first content page
     *
     * @param string $courseid
     * @param string $moduleid
     * @param string $pagesequence

     * @return string
     */
    public function fetch_firstpage_link($courseid, 
            $simplelessonid, $pageid) {

        $html =  $this->output->box_start();

        $url = new moodle_url('/mod/simplelesson/showpage.php',
                    array('courseid' => $courseid, 
                          'simplelessonid' => $simplelessonid, 
                          'pageid' => $pageid));
        $link = html_writer::link($url, 
                    get_string('gotofirstpage', MOD_SIMPLELESSON_LANG));
        $html .= html_writer::div($link, 
                    MOD_SIMPLELESSON_CLASS . '_content_links');
        
        $html .=  $this->output->box_end();

        return $html;
    }
    /**
     * Returns the html for the page index
     * module's instance settings page
     * @param int $simplelessonid for the lesson page index
     * @return string
     */
    public function fetch_module_edit_button($moduleid) {
        // page index
        $html = '';
        $html .= html_writer::start_div(
                MOD_SIMPLELESSON_CLASS . '_page');
        $url = new moodle_url('/course/modedit.php', 
                array('update' => $moduleid));
        $html .= html_writer::link($url,get_string('edit_settings', MOD_SIMPLELESSON_LANG));

        $html .= html_writer::end_div();

        return $html;
    }
    /**
     * Returns add first page button, used when no pages exist yet
     *
     * @param int $simplelesson id
     * @param int $courseid
     * @return string
     */
    public function add_firstpage_button($simplelessonid, $courseid) {

        $html =  $this->output->box_start();
        
        $url = new moodle_url('/mod/simplelesson/add_page.php', 
                array('courseid' => $courseid, 
                      'simplelessonid' => $simplelessonid,
                      'sequence' => 1));
        $link = html_writer::link($url,get_string('addfirstpage', MOD_SIMPLELESSON_LANG));
        $text = '<p>' . get_string('nopages', MOD_SIMPLELESSON_LANG) . '</p>' . $link;
        $html .=  html_writer::div($text, MOD_SIMPLELESSON_CLASS . '_content');
        $html .=  $this->output->box_end();
        
        return $html;
    }
    /**
     * Show the current page
     *
     * @param object $data object instance of current page
     * @param array $page_links list of links to all simplelesson pages
     * @param int $courseid
     * @return string html representation of page object
     */
    public function show_page($data, $show_index, $page_links) {
        
        $html = '';
        // Show the index if required)           
        if ($show_index) {
            $html .= self::fetch_index($page_links);
        }

        // Show page content
        $html .= html_writer::start_div(
                MOD_SIMPLELESSON_CLASS . '_content');
        $html .= $this->output->heading($data->pagetitle, 4);
        $html .= $data->pagecontents;
        $html .= html_writer::end_div();     
        return $html;

    }
    /**
     * Show the home, previous and next links
     *
     * @param object $data object instance of current page
     * @param int $courseid
     * @return string html representation of navigation links
     */
    public function show_page_nav_links($data, $courseid) {
        
        $links = array();

        $html =  $this->output->box_start();
        $html .= html_writer::start_div(MOD_SIMPLELESSON_CLASS . '_page_links');      
        // Home link
        $return_view = new moodle_url('/mod/simplelesson/view.php', 
                array('n' => $data->simplelessonid));
        $links[] = html_writer::link($return_view, 
                    get_string('homelink', MOD_SIMPLELESSON_LANG));
        
        
        if ($data->prevpageid != 0) {
            $prev_url = new moodle_url('/mod/simplelesson/showpage.php',
                        array('courseid' => $courseid, 
                        'simplelessonid' => $data->simplelessonid, 
                        'pageid' => $data->prevpageid));
            $links[] = html_writer::link($prev_url, 
                        get_string('gotoprevpage', MOD_SIMPLELESSON_LANG));
        
        } else {
            // Just put out the link text
            $links[] = get_string('gotoprevpage', MOD_SIMPLELESSON_LANG);
        }
        // Check link is valid
        if ($data->nextpageid != 0) {
            $next_url = new moodle_url('/mod/simplelesson/showpage.php',
                        array('courseid' => $courseid, 
                        'simplelessonid' => $data->simplelessonid, 
                        'pageid' => $data->nextpageid));
            $links[] = html_writer::link($next_url, 
                        get_string('gotonextpage', MOD_SIMPLELESSON_LANG));
        
        } else {
            // Just put out the link text
            $links[] = get_string('gotonextpage', MOD_SIMPLELESSON_LANG);
        }
        $html .= html_writer::alist($links, null, 'ul');
        $html .= html_writer::end_div();  // pagelinks 

        $html .=  $this->output->box_end();  
        
        return $html;    
    }

    /**
     * Returns the link to edit the current page
     *
     * @param string $courseid
     * @param object $data represents the current page
     * @return string html link
     */
    public function fetch_action_links($courseid, $data) {
    
        $links = array();

        $html =  $this->output->box_start();       
        $html .= html_writer::start_div(MOD_SIMPLELESSON_CLASS . '_action_links');      
        
        // edit link
        $link = new moodle_url('/mod/simplelesson/edit_page.php',
                    array('courseid' => $courseid, 
                          'simplelessonid' => $data->simplelessonid, 
                          'pageid' => $data->id,
                          'sequence' => $data->sequence));
        $links[] = html_writer::link($link, 
                    get_string('gotoeditpage', MOD_SIMPLELESSON_LANG));
        
        // add link
        $link = 
                new moodle_url('/mod/simplelesson/add_page.php', 
                array('courseid' => $courseid, 
                'simplelessonid' => $data->simplelessonid,
                'sequence' => $data->sequence + 1));    
        $links[] = html_writer::link($link, 
                get_string('gotoaddpage', MOD_SIMPLELESSON_LANG));
        
        $html .= html_writer::alist($links, null, 'ul');
        $html .= html_writer::end_div();  // action links 

        $html .=  $this->output->box_end();  
        
        return $html;    
    }
}