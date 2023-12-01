<?php
namespace ltisource_switch_config;

require_once($CFG->libdir . '/weblib.php');

class logger {
  public function log($message) {
    // Use default moodle debugging config for info messages.
    debugging($message);
  }
  public function error($message) {
    // Output error.
    if (CLI_SCRIPT) {
      mtrace("[KALTURA ERROR] $message");
    } else {
      echo '<div class="notifytiny debuggingmessage" data-rel="debugging">[KALTURA ERROR] ' . $message . '</div>';
    }
    // And also output it to error log.
    error_log("[KALTURA ERROR] $message");
  }
}
