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
 * Proxy auth script so both Kaltura plugin and core moodle can work with
 * Kaltura LTI.
 *
 * This script checks the cmid property of the login_hint paramenter. If it is
 * one of the constant values used by the Kaltura plugin, it delegates the
 * request to the kaltura auth.php script, otherwise it delegates the request
 * to the core lti module.
 */

require_once(__DIR__ . '/../../../../config.php');
$loginhint = optional_param('lti_message_hint', '', PARAM_RAW);
$kaltura_plugin = true;
if (!empty($loginhint)) {
  // Core Moodle LTI module either does not send any cmid (for ContentItem requests)
  // or sends the course module ID.
  // Instead, Kaltura plugin always sends one of "coursegallery", "mymedia", or
  // "browseembed".
  if (!isset($ltimessagehint->cmid) || is_numeric($ltimessagehint->cmid)) {
    $kaltura_plugin = false;
  }
}

if ($kaltura_plugin) {
  include __DIR__ . '/../../../../local/kaltura/auth.php';
} else {
  include __DIR__ . '/../../auth.php';
}
