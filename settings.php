<?php
// This file is part of Moodle - https://moodle.org/
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
 * Adds admin settings for the plugin.
 *
 * @package     ltisource_switch_config
 * @category    admin
 * @copyright   2022 SWITCH {@link https://switch.ch}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/profile/lib.php');

if ($hassiteconfig) {
  if ($ADMIN->fulltree) {
    // Only LTI launches with a specific "organization url" will be overriden.
    $settings->add(new admin_setting_configtext('ltisource_switch_config/kaltura_host',
      new lang_string('kaltura_host_setting', 'ltisource_switch_config'),
      new lang_string('kaltura_host_setting_description', 'ltisource_switch_config'),
      '1234.kaf.cast.switch.ch', PARAM_RAW_TRIMMED));

    $fields = array(
      'user_id' => new lang_string('lti_user_id_setting_user_id', 'ltisource_switch_config'),
      'username' => new lang_string('lti_user_id_setting_username', 'ltisource_switch_config'),
      'email' => new lang_string('lti_user_id_setting_email', 'ltisource_switch_config'),
      'idnumber' => new lang_string('lti_user_id_setting_idnumber', 'ltisource_switch_config'),
    );

    // Add custom profile fields of type text.
    $custom_fields = profile_get_custom_fields();
    foreach ($custom_fields as $id => $custom_field) {
      if ($custom_field->datatype == 'text') {
        $fields['profile_field_' . $custom_field->shortname] = $custom_field->name;
      }
    }
    // LTI param user_id will be overriden by one of the following options.
    $settings->add(new admin_setting_configselect(
      'ltisource_switch_config/lti_user_id',
      new lang_string('lti_user_id_setting', 'ltisource_switch_config'),
      new lang_string('lti_user_id_setting_description', 'ltisource_switch_config'),
      'user_id',
      $fields
    ));

    $settings->add(new admin_setting_configtext('ltisource_switch_config/lti_user_id_suffix',
      new lang_string('lti_user_id_suffix_setting', 'ltisource_switch_config'),
      new lang_string('lti_user_id_suffix_setting_description', 'ltisource_switch_config'),
      '', PARAM_RAW_TRIMMED));
  }
}
