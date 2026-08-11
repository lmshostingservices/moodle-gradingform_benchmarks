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
 * Uninstall hook for gradingform_benchmarks.
 *
 * Because this plugin has no db/install.xml, Moodle's automatic table-drop
 * step during uninstall is a no-op.  We explicitly drop all three plugin
 * tables here so that a subsequent reinstall starts clean.
 *
 * Drop order respects foreign-key dependencies:
 *   gradingbench_fills  →  gradingbench_items  →  gradingbench_grp
 *
 * @package    gradingform_benchmarks
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  2026 LMS-Labs
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Called by Moodle when the plugin is uninstalled via the plugin manager.
 *
 * @return bool true on success.
 */
function xmldb_gradingform_benchmarks_uninstall() {
    global $DB;
    $dbman = $DB->get_manager();

    foreach (['gradingbench_fills', 'gradingbench_items', 'gradingbench_grp'] as $tablename) {
        $table = new xmldb_table($tablename);
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
    }

    return true;
}
