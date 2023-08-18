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

namespace ltisource_switch_config;

class observer {
  public static function course_module_viewed(\core\event\base $event) {
    global $PAGE;
    // Inject custom CSS (in order to have a minimum iframe height).
    $PAGE->requires->css('/mod/lti/source/switch_config/override-styles.css');
  }

  /**
   * When a course is restored, copy the Kaltura Media Gallery contents from the
   * original media galleries to the new media galleries using the Kaltura API.
   */
  public static function course_restored(\core\event\course_restored $event) {
    // Don't process restores from different sites.
    if (!$event->other['samesite']) {
      return;
    }
    $logger = new logger();
    $oldcourse = $event->other['originalcourseid'];
    $newcourse = $event->courseid;

    // Get the Kaltura Media Gallery activities from the old course.
    $oldcms = \ltisource_switch_config\controller::get_kaltura_media_galleries($oldcourse);
    $logger->log("Got " . count($oldcms) . " Kaltura Media Gallery activities from the source course.");

    $restored = \ltisource_switch_config\controller::get_restored_tools($oldcms, $newcourse);

    foreach ($oldcms as $id => $oldcm) {
      if ($restored[$id] !== null) {
        // Copy the Kaltura Media Gallery contents from the old category to the new one.
        \ltisource_switch_config\controller::copy_kaltura_media_gallery($oldcm, $restored[$id]);
      } else {
        $logger->error("Could not find restored equivalent for LTI course module id $id.");
      }
    }

  }
}
