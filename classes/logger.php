<?php
namespace ltisource_switch_config;

require_once($CFG->libdir . '/weblib.php');

class logger {
  public function log($message) {
    // Use default moodle debugging config for info messages.
    debugging($message, DEBUG_NORMAL);
  }
  public function error($message) {
    debugging("[KALTURA ERROR] $message", DEBUG_MINIMAL);
    // And also output it to error log.
    error_log("[KALTURA ERROR] $message");
  }
}
