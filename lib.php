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
 * Internal library for SWITCH LTI Source Plugin.
 *
 * @package    ltisource_switch_config
 * @copyright  2022 SWITCH {@link https://switch.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('KALTURA_MEDIA_GALLERY_URL_PATH', '/hosted/index/course-gallery');

/**
 * Implementation of before_launch callback. (https://docs.moodle.org/dev/Callbacks)
 *
 * @param  object $instance
 * @param  string $endpoint
 * @param  array $requestparams
 *
 * @return array
 */
function ltisource_switch_config_before_launch($instance, $endpoint, $requestparams) {
  $params = array();

  // Check if this LTI launch needs to be overriden.
  $kaltura_host = get_config('ltisource_switch_config', 'kaltura_host');
  $parsed_endpoint = parse_url($endpoint);
  if ($kaltura_host == $parsed_endpoint['host']) {

    $params['user_id'] = ltisource_switch_config_get_lti_user_id();

    // Check if we are in a media gallery activity launch.
    if ($instance->cmid && ($parsed_endpoint['path'] == KALTURA_MEDIA_GALLERY_URL_PATH)) {
      // Override context_id parameter.
      $params['context_id'] = $instance->course . '-' . $instance->cmid;
    }
  }

  return $params;
}

/**
 * Return the user_id parameter to be used in the LTI launch call for the
 * current user, based on this plugin admin settings.
 *
 * @return string
 */
function ltisource_switch_config_get_lti_user_id() {
  global $USER;

  // Default case.
  $id = $USER->id;
  // Get the field to be used as LTI user id.
  $override_userid = get_config('ltisource_switch_config', 'lti_user_id');
  if ($override_userid == 'username') {
    $id = $USER->username;
  } else if ($override_userid == 'email') {
    $id = $USER->email;
  } else if ($override_userid == 'idnumber') {
    $id = $USER->idnumber;
  } else if (substr($override_userid, 0, strlen('profile_field_')) == 'profile_field_') {
    $fieldname = substr($override_userid, strlen('profile_field_'));
    if (array_key_exists($fieldname, $USER->profile)) {
      $id = $USER->profile[$fieldname];
    } else {
      // Could be the case if a custom field is deleted.
      error_log('User custom field ' . $fieldname . ' can\'t be used as LTI user id because it doesn\'t exist.');
    }
  }

  // Add (usually empty) suffix.
  $suffix = get_config('ltisource_switch_config', 'lti_user_id_suffix');

  return $id . $suffix;
}

/**
 * This acts as an event observer for course module deletion. If an LTI Kaltura
 * Media Gallery is deleted, it also deletes the associated Category in Kaltura
 * server.
 */
function ltisource_switch_config_pre_course_module_delete($cm) {
  global $DB;
  $module = $DB->get_record('modules', array('id' => $cm->module));
  // If the deleted module is not an LTI module, for sure is not a gallery.
  if ($module->name !== 'lti') {
    return;
  }
  // Get instance object and lti_type object.
  $lti = $DB->get_record('lti', array('id' => $cm->instance));
  $ltitype = $DB->get_record('lti_types', array('id' => $lti->typeid));
  // Deduce the tool LTI launch url.
  $url = !empty(trim($lti->toolurl)) ? trim($lti->toolurl) : trim($ltitype->baseurl);
  // Check if the url is a Kaltura Media Gallery url.
  $parsed_url = parse_url($url);
  if ($parsed_url['path'] == KALTURA_MEDIA_GALLERY_URL_PATH) {
    // Check if the deleted module is indeed a LTI Kaltura Gallery.
    $controller = new \ltisource_switch_config\controller();
    $controller->delete_kaltura_media_gallery($cm->course, $cm->id);
  }
}

/**
 * This acts as an event observer for course deletion. If a course is deleted,
 * it also deletes the associated Kaltura Media Gallery in Kaltura server both
 * for the course gallery and for all activity galleries.
 */
function ltisource_switch_config_pre_course_delete($course) {
  $controller = new \ltisource_switch_config\controller();

  // Delete course media gallery.
  $controller->delete_kaltura_course_media_gallery($course->id);

  // Delete activity media galleries.
  $controller->delete_kaltura_activity_media_galleries($course->id);

}




