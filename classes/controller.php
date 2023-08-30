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
 * @copyright  2023 SWITCH {@link https://switch.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace ltisource_switch_config;

require_once __DIR__ . '/../lib.php';

class controller {
  /**
   * Return the course module objects for all Kaltura Media Gallery LTI external
   * tools in the given course.
   */
  static function get_kaltura_media_galleries($courseid) {
    global $DB;
    $sql = "SELECT cm.*, lti.name, lti.timecreated, lti.typeid, lti.toolurl, ltit.baseurl
            FROM {course_modules} cm
            JOIN {modules} m ON cm.module = m.id
            JOIN {lti} lti ON cm.instance = lti.id
            LEFT JOIN {lti_types} ltit ON ltit.id = lti.typeid
            WHERE cm.course = :courseid
              AND m.name = 'lti'
              AND ((lti.toolurl LIKE :toolurl) OR (ltit.baseurl LIKE :baseurl))
           ";
    $params = array(
      'courseid' => $courseid,
      'toolurl' => '%' . KALTURA_MEDIA_GALLERY_URL_PATH . '%',
      'baseurl' => '%' . KALTURA_MEDIA_GALLERY_URL_PATH . '%'
    );
    return $DB->get_records_sql($sql, $params);
  }

  /**
   * Provides the restored tool from a given original tool. The provided array is
   * the result of get_kaltura_media_galleries().
   */
  static function get_restored_tools($modules, $courseid) {
    global $DB;
    $restored = array();
    foreach ($modules as $module) {
      $sql = "SELECT cm.*, lti.name, lti.timecreated, lti.typeid, lti.toolurl, ltit.baseurl
              FROM {course_modules} cm
              JOIN {lti} lti ON cm.instance = lti.id
              LEFT JOIN {lti_types} ltit ON ltit.id = lti.typeid
              WHERE cm.course = :courseid
                AND lti.typeid = :ltitypeid
                AND lti.toolurl = :ltitoolurl
                AND ltit.baseurl = :ltitbaseurl
                AND lti.timecreated = :ltitimecreated
             ";
      $params = array(
        'courseid' => $courseid,
        'ltitypeid' => $module->typeid,
        'ltitoolurl' => $module->toolurl,
        'ltitbaseurl' => $module->baseurl,
        'ltitimecreated' => $module->timecreated
      );
      $candidates = $DB->get_records_sql($sql, $params);
      if (count($candidates) > 1) {
        // Filter by name
        $candidates = array_filter($candidates, function($candidate) use ($module) {
          return $candidate->name == $module->name;
        });
      }
      if (count($candidates) > 0) {
        $restored[$module->id] = array_shift($candidates);
      } else {
        $restored[$module->id] = null;
      }
    }
    return $restored;
  }
  /**
   * Uses the Kaltura API to copy the resources from cm $source to
   * cm $destination.
   */
  static function copy_kaltura_media_gallery($source, $destination) {
    $logger = new logger();
    $api = new kaltura_api($logger);
    $parent = $api->getCategoryByFullName("Moodle>site>channels");
    if ($parent === false) {
      $logger->error("Could not find parent category 'Moodle>site>channels'");
      return;
    }
    $oldcategory = $source->course . '-' . $source->id;
    $category = $api->getCategoryByFullName("Moodle>site>channels>$oldcategory");
    if ($category === false) {
      $logger->error("Original category $oldcategory not found");
    }
    $newcategory = $destination->course . '-' . $destination->id;
    $api->copyCategory($category, $parent, $newcategory);
    $logger->log("Copied category $oldcategory to $newcategory");
  }
}
