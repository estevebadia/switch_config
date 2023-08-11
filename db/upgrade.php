<?php
// This file is part of Moodle - http://moodle.org/
//
// It is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die;

function xmldb_ltisource_switch_config_upgrade($oldversion) {
  global $CFG, $DB, $OUTPUT;

  $dbman = $DB->get_manager();

  if ($oldversion < 2023081103) {
    // In order for Kaltura tools to work, all of them need to share the same
    // client_id LTI 1.3 parameter. The unique index in the clientid field from
    // the lti_types table prevents us from setting several tools to the same
    // client_id. We therefore replace this index by a not unique index.
    $table = new xmldb_table('lti_types');
    $index = new xmldb_index('clientid', XMLDB_INDEX_UNIQUE, array('clientid'));
    if ($dbman->index_exists($table, $index)) {
      $dbman->drop_index($table, $index);
    }
    $index = new xmldb_index('clientid', XMLDB_INDEX_NOTUNIQUE, array('clientid'));
    if (!$dbman->index_exists($table, $index)) {
      $dbman->add_index($table, $index);
    }
  }

  return true;
}
