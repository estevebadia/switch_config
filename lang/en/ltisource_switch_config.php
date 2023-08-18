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
 * English language file.
 *
 * @package    ltisource_switch_config
 * @copyright  2022 SWITCH {@link https://switch.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'SWITCH LTI configuration';
$string['manage_switch_config'] = 'Manage SWITCH LTI configuration';
$string['lti_user_id_setting'] = 'LTI user_id paramater';
$string['lti_user_id_setting_description'] = 'Value to send as user_id LTI parameter. Moodle default is User Id, Kaltura default is Username.';
$string['lti_user_id_setting_user_id'] = 'User Id';
$string['lti_user_id_setting_username'] = 'Username';
$string['lti_user_id_setting_email'] = 'Email';
$string['kaltura_host_setting'] = 'Kaltura host';
$string['kaltura_host_setting_description'] = 'Your host for accessing kaltura servicves. LTI launches matching this host will be overriden. Other LTI launches won\'t be modified.';
$string['lti_user_id_setting_idnumber'] = 'ID number';
$string['lti_user_id_suffix_setting'] = 'LTI user_id suffix';
$string['lti_user_id_suffix_setting_description'] = 'Optional suffix text string to append to the user_id LTI parameter. For example "@domain.com".';
// Define remaining strings from settins page
$string['lti_fix_clientid_button_name'] = 'Fix Client IDs';
$string['lti_fix_clientid_button'] = 'Execute';
$string['lti_fix_clientid_button_description'] = 'Click to set the client_id of all existing Kaltura external tools to the same value as the Kaltura plugin.';
$string['lti_fix_clientid_button_success'] = 'Updated the client_id of {$a} tool types.';
$string['lti_fix_clientid_button_empty'] = 'No tool types were updated.';
$string['api_url'] = 'Kaltura API URL';
$string['api_url_description'] = 'The URL of the Kaltura API. Used when migrating media galleries.';
$string['partner_id'] = 'Kaltura Partner ID';
$string['partner_id_description'] = 'The Partner ID of the Kaltura API. Used when migrating media galleries.';
$string['adminsecret'] = 'Kaltura Admin Secret';
$string['adminsecret_description'] = 'The Admin Secret of the Kaltura API. Used when migrating media galleries.';
