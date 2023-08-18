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

    $api_url = 'https://api.cast.switch.ch';
    $settings->add(new admin_setting_configtext('ltisource_switch_config/api_url',
    new lang_string('api_url', 'ltisource_switch_config'),
    new lang_string('api_url_description', 'ltisource_switch_config'),
    $api_url, PARAM_RAW_TRIMMED));

    $partner_id = get_config('local_kaltura', 'partner_id');
    $settings->add(new admin_setting_configtext('ltisource_switch_config/partner_id',
    new lang_string('partner_id', 'ltisource_switch_config'),
    new lang_string('partner_id_description', 'ltisource_switch_config'),
    $partner_id ? $partner_id : '', PARAM_RAW_TRIMMED));

    $adminsecret = get_config('local_kaltura', 'adminsecret');
    $settings->add(new admin_setting_configtext('ltisource_switch_config/adminsecret',
    new lang_string('adminsecret', 'ltisource_switch_config'),
    new lang_string('adminsecret_description', 'ltisource_switch_config'),
    $adminsecret ? $adminsecret : '', PARAM_RAW_TRIMMED));

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

    // Add a button to fix the client id. Since there is no suitable admin_setting for this,
    // we use a description with a button to POST to fix_client_id.php.
    $button = html_writer::empty_tag('input', array(
      'type' => 'submit',
      'value' => new lang_string('lti_fix_clientid_button', 'ltisource_switch_config'),
      'class' => 'btn btn-outline-primary mb-3',
      'id'=> 'fix_clientid',
      'name' => 'fix_clientid',
      'formaction' => new moodle_url('/mod/lti/source/switch_config/fix_client_id.php'),
    ));
    $button .= html_writer::tag('p', new lang_string('lti_fix_clientid_button_description', 'ltisource_switch_config'));

    $fixed_client_id = optional_param('fix_clientid', null, PARAM_INT);
    global $OUTPUT;
    if ($fixed_client_id !== null) {
      if ($fixed_client_id > 0) {
        $button .= $OUTPUT->notification(new lang_string('lti_fix_clientid_button_success', 'ltisource_switch_config', $fixed_client_id), 'notifysuccess');
      } else {
        $button .= $OUTPUT->notification(new lang_string('lti_fix_clientid_button_empty', 'ltisource_switch_config'), 'notifysuccess');
      }
    }

    $settings->add(new admin_setting_description('ltisource_switch_config/fix_client_id_heading', new lang_string('lti_fix_clientid_button_name', 'ltisource_switch_config'), $button));
  }
}
