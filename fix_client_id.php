<?php
require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

// Require this script to be accessed by a POST request.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  die('This script can only be accessed by a POST request.');
}

// Require admin login.
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Run fix script
$clientid = get_client_id();
$n = set_client_id($clientid);

// Redirect to the admin settings page.
$url = '/admin/settings.php?section=ltisourcesettingswitch_config&fix_clientid=' . $n;
redirect(new moodle_url($url));

/**
 * Returns the client_id parameter defined by the Kaltura plugin if exists,
 * otherwise returns a random string and sets it as 'local_kaltura/client_id'
 * config value.
 *
 * @return string The client_id.
 */
function get_client_id() {
  if (!$clientid = get_config('local_kaltura','client_id')) {
    $clientid = random_string(15);
    set_config('client_id', $clientid, 'local_kaltura');
  }
  return $clientid;
}

/**
 * Sets the provider clientid to all external tools pointing to the kaltura
 * domain defined in this plugin settings.
 *
 * @param string $clientid The clientid to set.
 * @return int The number of updated tools.
 */
function set_client_id($clientid) {
  // Load all kaltura external tool types.
  $kaltura_host = get_config('ltisource_switch_config', 'kaltura_host');
  $tool_types = lti_get_tools_by_domain($kaltura_host);
  // Update them and count updates.
  global $DB;
  $count = 0;
  foreach ($tool_types as $type) {
    if ($type->clientid !== $clientid) {
      $type->clientid = $clientid;
      $DB->update_record('lti_types', $type);
      $count++;
    }
  }
  return $count;
}
