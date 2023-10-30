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
  function __construct() {
    $this->logger = new logger();
  }

  /**
   * When a course is restored, copy the Kaltura Media Gallery contents from the
   * original media gallery to the new media gallery using the Kaltura API.
   */
  public function restore_kaltura_course_media_gallery($oldcourse, $newcourse) {
    $api = new kaltura_api($this->logger);
    $parent = $api->getCategoryByFullName("Moodle>site>channels");
    if ($parent === false) {
      $this->logger->error("Could not find parent category 'Moodle>site>channels'");
      return;
    }

    $category = $api->getCategoryByFullName("Moodle>site>channels>$oldcourse");
    if ($category === false) {
      $this->logger->error("Original category Moodle>site>channels>$oldcourse not found");
      return;
    }

    $newcategory = $api->copyCategory($category, $parent, $newcourse);
    $this->logger->log("Copied Course Media Gallery category $oldcourse to $newcourse");

    // Now copy the InContext subcategory used for mashups.
    $inContext = $api->getCategoryByFullName("Moodle>site>channels>$oldcourse>InContext");
    if ($inContext !== false ) {
      $api->copyCategory($inContext, $newcategory, 'InContext');
      $this->logger->log("Copied InContext subcategory $oldcourse>InContext to $newcourse>InContext");
    } else {
      $this->logger->log("No InContext subcategory for category $oldcourse");
    }

  }

  /**
   * Copy the contents of each Kaltura Media Gallery activity from the original
   * course to the new course.
   */
  public function restore_kaltura_media_galleries($oldcourse, $newcourse) {
    // Get the Kaltura Media Gallery activities from the old course.
    $oldcms = $this->get_kaltura_media_galleries($oldcourse);
    $this->logger->log("Got " . count($oldcms) . " Kaltura Media Gallery activities from the source course.");

    $restored = $this->get_restored_tools($oldcms, $newcourse);

    foreach ($oldcms as $id => $oldcm) {
      if ($restored[$id] !== null) {
        // Copy the Kaltura Media Gallery contents from the old category to the new one.
        $this->copy_kaltura_media_gallery($oldcm, $restored[$id]);
      } else {
        $this->logger->error("Could not find restored equivalent for LTI course module id $id.");
      }
    }
  }
  /**
   * Return the course module objects for all Kaltura Media Gallery LTI external
   * tools in the given course.
   */
  private function get_kaltura_media_galleries($courseid) {
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
  private function get_restored_tools($modules, $courseid) {
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
  private function copy_kaltura_media_gallery($source, $destination) {
    $api = new kaltura_api($this->logger);
    $parent = $api->getCategoryByFullName("Moodle>site>channels");
    if ($parent === false) {
      $this->logger->error("Could not find parent category 'Moodle>site>channels'");
      return;
    }
    $oldcategory = $source->course . '-' . $source->id;
    $category = $api->getCategoryByFullName("Moodle>site>channels>$oldcategory");
    if ($category === false) {
      $this->logger->error("Original category $oldcategory not found");
    }
    $newcategory = $destination->course . '-' . $destination->id;
    $api->copyCategory($category, $parent, $newcategory);
    $this->logger->log("Copied category $oldcategory to $newcategory");
  }
}
