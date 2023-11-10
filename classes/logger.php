<?php
namespace ltisource_switch_config;

require_once($CFG->libdir . '/weblib.php');

class logger {
  public function log($message) {
    mtrace("[KALTURA INFO] $message");

  }
  public function error($message) {
    mtrace("[KALTURA ERROR] $message");
    debugging($message);
    error_log("[KALTURA ERROR] $message");
  }
}
