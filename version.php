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
 * Defines the version of simplelesson
 *
 *
 * @package    mod_simplelesson
 * @copyright  2015 Justin Hunt & Richard Jones 2018 https://richardnz/net/
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2018031309;  // If version == 0 then module will not be installed
$plugin->requires  = 2017051506;  // Requires this Moodle version
$plugin->cron      = 0;           // Period for cron to check this module (secs)
$plugin->component = 'mod_simplelesson';  // To check on upgrade, that module sits in correct place
