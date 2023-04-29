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
 * Provides meta-data about the plugin.
 *
 * @package     block_sic
 * @author      {2023} {Andres Cubillos Salazar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined("MOODLE_INTERNAL") || die;

function xmldb_block_sic_upgrade($oldversion) {

    global $DB;

    $dbman = $DB->get_manager();

    $version = 2021112000;

    if ($oldversion < $version) {

        // Define table sic to be created.

        $table = new xmldb_table('sic');

        // Adding fields to table sic.

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_field('data', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        $table->add_field('datetime', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_field('errors', XMLDB_TYPE_TEXT, null, null, null, null, null);



        // Adding keys to table sic.

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);



        // $field = new xmldb_field('data', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);



        // Conditionally launch create table for sic.

        if (!$dbman->table_exists($table)) {

            $dbman->create_table($table);

        }



        // Conditionally launch drop field sessions.

        // if ($dbman->field_exists($table, $field)) {

        //     $dbman->drop_field($table, $field);

        // }



        // Sic savepoint reached.

        // upgrade_block_savepoint(true, $version, 'sic');



        // // Define table sic_sessions to be created.

        // $table2 = new xmldb_table('sic_sessions');



        // // Conditionally launch drop table for sic_sessions.

        // if ($dbman->table_exists($table2)) {

        //     $dbman->drop_table($table2);

        // }



        // Sic savepoint reached.

        upgrade_block_savepoint(true, $version, 'sic');

    }

}



?>