<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_gradingform_benchmarks_upgrade($oldversion) {
    if ($oldversion < 2026072300) {
        upgrade_plugin_savepoint(true, 2026072300, 'gradingform', 'benchmarks');
    }
    return true;
}
