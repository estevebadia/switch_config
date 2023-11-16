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
 * Version details.
 *
 * @package    ltisource_switch_config
 * @copyright  2022 SWITCH {@link https://switch.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$observers = array(
  array(
    'eventname' => '\mod_lti\event\course_module_viewed',
    'callback' => '\ltisource_switch_config\observer::course_module_viewed',
  ),
  array(
    'eventname' => '\core\event\course_restored',
    'callback' => '\ltisource_switch_config\observer::course_restored',
  )
);
