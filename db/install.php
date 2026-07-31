<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

/**
 * Post-install hook for gradingform_benchmarks.
 *
 * This plugin deliberately ships NO db/install.xml.  Moodle only calls
 * install_from_xmldb_file() when that file is present, so by omitting it we
 * avoid the fatal "Table already exists" DDL error that occurs when orphaned
 * tables are left behind after an incomplete uninstall.
 *
 * All three plugin tables are created here via the XMLDB API, guarded with
 * table_exists() checks so the function is idempotent — safe on a clean
 * install and on a re-install where the tables already exist.
 *
 * The full schema definition is kept in db/schema.xml for developer reference
 * and the Moodle XMLDB editor; that file is NOT loaded by upgradelib.php.
 *
 * @return bool
 */
function xmldb_gradingform_benchmarks_install() {
    global $DB;
    $dbman = $DB->get_manager();

    // -------------------------------------------------------------------------
    // gradingbench_grp — one row per benchmark group (criterion set)
    // -------------------------------------------------------------------------
    if (!$dbman->table_exists('gradingbench_grp')) {
        $table = new xmldb_table('gradingbench_grp');
        $table->add_field('id',           XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('definitionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder',    XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description',  XMLDB_TYPE_TEXT,    null,  null, null,          null, null);
        $table->add_key('primary',      XMLDB_KEY_PRIMARY,  ['id']);
        $table->add_key('definitionid', XMLDB_KEY_FOREIGN,  ['definitionid'], 'grading_definitions', ['id']);
        $dbman->create_table($table);
    }

    // -------------------------------------------------------------------------
    // gradingbench_items — one row per benchmark criterion (item)
    // -------------------------------------------------------------------------
    if (!$dbman->table_exists('gradingbench_items')) {
        $table = new xmldb_table('gradingbench_items');
        $table->add_field('id',         XMLDB_TYPE_INTEGER, '10',   null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('groupid',    XMLDB_TYPE_INTEGER, '10',   null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder',  XMLDB_TYPE_INTEGER, '10',   null, XMLDB_NOTNULL, null, null);
        $table->add_field('score',      XMLDB_TYPE_NUMBER,  '10,5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('definition', XMLDB_TYPE_TEXT,    null,   null, null,           null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('groupid', XMLDB_KEY_FOREIGN, ['groupid'], 'gradingbench_grp', ['id']);
        $dbman->create_table($table);
    }

    // -------------------------------------------------------------------------
    // gradingbench_fills — stores each rater's checked/remark per item
    // -------------------------------------------------------------------------
    if (!$dbman->table_exists('gradingbench_fills')) {
        $table = new xmldb_table('gradingbench_fills');
        $table->add_field('id',           XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid',   XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('groupid',      XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid',       XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('checked',      XMLDB_TYPE_INTEGER, '1',  null, null,           null, null);
        $table->add_field('remark',       XMLDB_TYPE_TEXT,    null,  null, null,           null, null);
        $table->add_field('remarkformat', XMLDB_TYPE_INTEGER, '2',  null, null,           null, null);
        $table->add_key('primary',    XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('instanceid', XMLDB_KEY_FOREIGN, ['instanceid'], 'grading_instances',  ['id']);
        $table->add_key('groupid',    XMLDB_KEY_FOREIGN, ['groupid'],    'gradingbench_grp',   ['id']);
        $table->add_key('itemid',     XMLDB_KEY_FOREIGN, ['itemid'],     'gradingbench_items', ['id']);
        $table->add_index('instanceid-groupid-itemid', XMLDB_INDEX_UNIQUE, ['instanceid', 'groupid', 'itemid']);
        $dbman->create_table($table);
    }

    return true;
}
