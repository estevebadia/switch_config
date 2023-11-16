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
    try {
      $api = new kaltura_api($this->logger);
      $parent = $this->get_kaltura_parent_category();
      if ($parent === false) {
        return;
      }
      $fullname = $parent->fullName . ">" . $oldcourse;
      $category = $api->getCategoryByFullName($fullname);
      if ($category === false) {
        $this->logger->error("Original category $fullname not found");
        return;
      }

      $newcategory = $api->copyCategory($category, $parent, $newcourse);
      if ($newcategory !== false) {
        $this->logger->log("Copied Course Media Gallery category $oldcourse to $newcourse");
        // Now copy the InContext subcategory used for mashups.
        $inContext = $api->getCategoryByFullName($fullname . ">InContext");
        if ($inContext !== false) {
          $newInContext = $api->copyCategory($inContext, $newcategory, 'InContext');
          if ($newInContext !== false) {
            $this->logger->log("Copied InContext subcategory $oldcourse>InContext to $newcourse>InContext");
          }
        } else {
          $this->logger->log("No InContext subcategory for category $oldcourse");
        }
      }
    } catch (\Exception $e) {
      $this->logger->error("Exception copying Kaltura course media gallery" . $e->getMessage());
    }
  }

  /**
   * Copy the contents of each Kaltura Media Gallery activity from the original
   * course to the new course.
   */
  public function restore_kaltura_media_galleries($oldcourse, $newcourse) {
    // Get the Kaltura Media Gallery activities from the old course.
    try {
      $oldcms = $this->get_kaltura_media_galleries($oldcourse);
      $this->logger->log("Got " . count($oldcms) . " Kaltura Media Gallery activities from the source course.");

      $restored = $this->get_restored_tools($oldcms, $newcourse);

      foreach ($oldcms as $id => $oldcm) {
        if ($restored[$id] !== null) {
          // Copy the Kaltura Media Gallery contents from the old category to the new one.
          try {
            $this->copy_kaltura_media_gallery($oldcm, $restored[$id]);
          } catch (\Exception $e) {
            $this->logger->error("Exception copying Kaltura Media Gallery $id: " . $e->getMessage());
          }
        } else {
          $this->logger->error("Could not find restored equivalent for LTI course module id $id.");
        }
      }
    } catch (\Exception $e) {
      $this->logger->error("Exception restoring Kaltura Media Galleries: " . $e->getMessage());
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
                AND cm.module = :moduleid
                AND lti.typeid = :ltitypeid
                AND lti.toolurl = :ltitoolurl
                AND ltit.baseurl = :ltitbaseurl
                AND lti.timecreated = :ltitimecreated
             ";
      $params = array(
        'courseid' => $courseid,
         // Ensure that the module is an LTI module.
        'moduleid' => $module->module,
        'ltitypeid' => $module->typeid,
        'ltitoolurl' => $module->toolurl,
        'ltitbaseurl' => $module->baseurl,
          // timecreated is the same for the original and the restored tool.
        'ltitimecreated' => $module->timecreated
      );
      $candidates = $DB->get_records_sql($sql, $params);
      if (count($candidates) > 1) {
        // Filter by name
        $candidates = array_filter($candidates, function($candidate) use ($module) {
          return $candidate->name == $module->name;
        });
        if (count($candidates) > 1) {
          // Log unexpected situation, but continue anyway.
          $this->logger->error("Found more than one candidate for LTI course module id $module->id in course $courseid.");
        }
      }
      if (count($candidates) > 0) {
        $restored[$module->id] = array_shift($candidates);
      } else {
        $restored[$module->id] = null;
        $this->logger->error("Could not find restored equivalent for LTI course module id $module->id in course $courseid.");
      }
    }
    return $restored;
  }
  /**
   * Uses the Kaltura API to copy the resources from cm $source to
   * cm $destination.
   */
  private function copy_kaltura_media_gallery($source, $destination) {
    $parent = $this->get_kaltura_parent_category();
    if ($parent === false) {
      return;
    }
    $oldcategory = $source->course . '-' . $source->id;
    $api = new kaltura_api($this->logger);
    $category = $api->getCategoryByFullName($parent->fullName . ">". $oldcategory);
    if ($category === false) {
      $this->logger->error("Original category $oldcategory not found");
      return;
    }
    $newcategory = $destination->course . '-' . $destination->id;
    $api->copyCategory($category, $parent, $newcategory);
    $this->logger->log("Copied category $oldcategory to $newcategory");
  }

  private function get_kaltura_parent_category_fullname() {
    $name = $this->get_kaltura_root_category_name();
    $fullname = "$name>site>channels";
    return $fullname;
  }

  private function get_kaltura_parent_category() {
    static $parent = null;
    if ($parent === null) {
      $fullname = $this->get_kaltura_parent_category_fullname();

      $api = new kaltura_api($this->logger);
      $parent = $api->getCategoryByFullName($fullname);

      if ($parent === false) {
        $this->logger->error("Could not find parent category '$fullname'");
      }
    }
    return $parent;
  }

  /**
   * Return the root category for this site media gallery. This is configured in
   * the plugin settings and must be the same setting as in
   * Kaltura Administration Site >
   *  Configuration Management > Global > Categories > rootCategory
   */
  protected function get_kaltura_root_category_name() {
    return get_config('ltisource_switch_config', 'kaltura_root_category');
  }

  /**
   * Deletes the category Moodle>site>channels>$name
   */
  private function delete_kaltura_gallery($name) {
    try {
      $parentname = $this->get_kaltura_parent_category_fullname();
      $fullname = $parentname . ">" . $name;

      $api = new kaltura_api($this->logger);
      $category = $api->getCategoryByFullName($fullname);
      if ($category === false) {
        $this->logger->error("Kaltura category $fullname not found for delete.");
        return;
      }
      $api->deleteCategory($category);
      $this->logger->log("Deleted Kaltura category $fullname");
    } catch (\Exception $e) {
      $this->logger->error("Exception deleting Kaltura category $fullname: " . $e->getMessage());
    }
  }

  /**
   * Delete the category related to the given course id.
   */
  public function delete_kaltura_course_media_gallery($courseid) {
    $this->delete_kaltura_gallery($courseid);
  }

  /**
   * Check if the course module id just given is indeed an LTI Media Gallery and
   * in this case, it deletes the associated category in Kaltura server.
   */
  public function delete_kaltura_media_gallery($courseid, $cmid) {
    $this->delete_kaltura_gallery($courseid . "-" . $cmid);
  }

  /**
   * Delete all the Kaltura Media Galleries (Activities) associated to the given
   * course.
   */
  public function delete_kaltura_activity_media_galleries($courseid) {
    $galleries = $this->get_kaltura_media_galleries($courseid);
    foreach ($galleries as $gallery) {
      $this->delete_kaltura_media_gallery($courseid, $gallery->id);
    }
  }
}
