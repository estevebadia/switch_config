<?php
namespace ltisource_switch_config;

class logger {
  public function log($message) {
    mtrace("[INFO] $message");
  }
  public function error($message) {
    mtrace("[ERROR] $message");
  }
}
