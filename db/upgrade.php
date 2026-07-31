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
 * @package    gradingform
 * @subpackage benchmarks
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Keeps track of benchmarks plugin upgrade path.
 *
 * @param int $oldversion the DB version of currently installed plugin
 * @return bool true
 */
function xmldb_gradingform_benchmarks_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012051001) {

        // Changing type of field description on table gradingform_benchmark_grp to text.
        $table = new xmldb_table('gradingform_benchmark_grp');
        if ($dbman->table_exists($table)) {
            $field = new xmldb_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'sortorder');
            $dbman->change_field_type($table, $field);
        }

        // Changing type of field definition on table gradingform_benchmark_items to text.
        $table = new xmldb_table('gradingform_benchmark_items');
        if ($dbman->table_exists($table)) {
            $field = new xmldb_field('definition', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'score');
            $dbman->change_field_type($table, $field);
        }

        upgrade_plugin_savepoint(true, 2012051001, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026032001216) {
        // BUG-DB-NAME-LENGTH FIX (v1.2.16): With mdl_ prefix the full DB table name must be
        // under 28 characters total. All prior table names exceeded this limit.
        // Final names: gradingbench_grp (20), gradingbench_items (22), gradingbench_fills (22).
        // This step handles every possible prior-install state:
        //   - Original installs:  gradingform_benchmarks_groups / _items / _fills
        //   - v1.2.15 installs:   gradingform_benchmarks_grp   / _items / _fills
        //   - v1.2.16a installs:  gradingform_benchmark_grp    / _items / _fills

        // Groups table — two possible old names.
        foreach (['gradingform_benchmarks_groups', 'gradingform_benchmarks_grp', 'gradingform_benchmark_grp'] as $oldname) {
            $t = new xmldb_table($oldname);
            if ($dbman->table_exists($t)) {
                $dbman->rename_table($t, 'gradingbench_grp');
                break;
            }
        }

        // Items table.
        foreach (['gradingform_benchmarks_items', 'gradingform_benchmark_items'] as $oldname) {
            $t = new xmldb_table($oldname);
            if ($dbman->table_exists($t)) {
                $dbman->rename_table($t, 'gradingbench_items');
                break;
            }
        }

        // Fills table.
        foreach (['gradingform_benchmarks_fills', 'gradingform_benchmark_fills'] as $oldname) {
            $t = new xmldb_table($oldname);
            if ($dbman->table_exists($t)) {
                $dbman->rename_table($t, 'gradingbench_fills');
                break;
            }
        }

        upgrade_plugin_savepoint(true, 2026032001216, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026032001217) {
        // v1.2.17: VERSION-BUMP — Routine release. No code change. No DB schema change.
        upgrade_plugin_savepoint(true, 2026032001217, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026032501218) {
        // v1.2.18: NEW — Bulk paste format selector (Parts/Outline/Markdown) and ChatGPT
        // prompt download button. No DB schema change.
        upgrade_plugin_savepoint(true, 2026032501218, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026033001219) {
        // v1.2.19: FIX — Bulk paste and TGA import now work on Moodle 4.x (replaced
        // .getDOMNode().click() with direct YUI template injection). Also moved table
        // creation from install.xml to db/install.php to survive re-installs where
        // tables were left from a prior uninstall. No DB schema change.
        upgrade_plugin_savepoint(true, 2026033001219, 'gradingform', 'benchmarks');
    }

    // v1.2.20: FIX — unlock_verifier.php switched from raw PHP curl_init() to Moodle's \curl
    // class (require_once $CFG->libdir/filelib.php). Raw curl_init() bypassed Moodle's SSL
    // cert bundle, causing silent API call failures on Moodle hosting environments.
    // Moodle \curl uses the correct CA bundle and respects proxy settings.
    // No DB schema changes. version.php → 2026041001220.
    if ($oldversion < 2026041001220) {
        upgrade_plugin_savepoint(true, 2026041001220, 'gradingform', 'benchmarks');
    }

    // v1.2.21: FIX — settings.php parent category changed from 'grades' (which does not exist
    // in the admin tree on Moodle 4.4+) to 'localplugins'. Root cause: the 'grades' admin
    // category node is no longer present in the default Moodle 4.4 admin tree, so the settings
    // page was silently never registered — making it impossible for admins to configure API
    // credentials. Using 'localplugins' as the parent guarantees the settings page is always
    // accessible at /admin/settings.php?section=gradingformbenchmarks. No DB schema changes.
    if ($oldversion < 2026041001221) {
        upgrade_plugin_savepoint(true, 2026041001221, 'gradingform', 'benchmarks');
    }

    // v1.2.22: FIX — settings.php completely rewritten to work on Moodle 4.4+.
    //   Root cause: Moodle 4.4's admin tree builder does NOT include gradingform plugins
    //   in its standard settings.php loading loop. The settings page created in v1.2.21
    //   via $ADMIN->add('localplugins', $settings) was silently discarded because the
    //   variable $settings (pre-created by Moodle's load_plugin_settings()) is NULL for
    //   gradingform plugin types — it is only non-null for plugin types that Moodle
    //   explicitly includes in its admin tree loop. Fix: guard with $ADMIN->locate() to
    //   avoid duplicate nodes; create page into $page (not $settings) then explicitly
    //   add to 'localplugins'. Falls back gracefully if Moodle DOES pre-create $settings
    //   (newer Moodle versions). No DB schema changes. version.php → 2026041001222.
    if ($oldversion < 2026041001222) {
        upgrade_plugin_savepoint(true, 2026041001222, 'gradingform', 'benchmarks');
    }

    // v1.2.24: FIX — Bulk paste Outline format split bullet lines at commas.
    //   Root cause: CSV detection in parseOutline() ran before markdown bullet detection,
    //   so any "- item with, a comma" line was treated as a CSV group header + sub-items.
    //   Fix: added !/^[-*]\s/.test(trimmed) exclusion to the CSV condition.
    //   No DB schema changes. version.php → 2026041500024.
    if ($oldversion < 2026041500024) {
        upgrade_plugin_savepoint(true, 2026041500024, 'gradingform', 'benchmarks');
    }

    // v1.2.25: FIX-BM-COMMA-BULLET — Bullet lines with commas still split in edge cases
    //   (Unicode dashes, ASCII dash with multi-char prefix, etc.). The v1.2.24 exclusion
    //   regex !/^[-*]\s/ only guarded ASCII hyphen/asterisk; any other bullet char
    //   fell through to CSV splitting. Fix (JS-only, checklisteditor.js):
    //   (1) Moved markdown bullet detection BEFORE the CSV check in parseOutline() so
    //       any "- text" or "* text" line returns early and commas inside it are never split.
    //   (2) Made the CSV splitting parentheses-aware: commas inside (...) are not separators,
    //       so "Group (e.g. A, B), Item 1" still parses as one group label + one CSV item.
    //   No DB schema changes. version.php → 2026041500025.
    if ($oldversion < 2026041500025) {
        upgrade_plugin_savepoint(true, 2026041500025, 'gradingform', 'benchmarks');
    }

    // v1.2.26: ENHANCEMENT — Added "Paste as code so numbered bullets can be copied
    //   as well." to all 3 ChatGPT prompt templates (Parts, Outline, Markdown) in
    //   checklisteditor.js. JS-only change, no DB schema changes.
    //   version.php → 2026041500026.
    if ($oldversion < 2026041500026) {
        upgrade_plugin_savepoint(true, 2026041500026, 'gradingform', 'benchmarks');
    }

    // v1.2.29 - MAINTENANCE: AMD build sync — gradingpanel.js build and .min.js were
    //   out of sync with src (all three AMD files had different MD5 hashes).
    //   Resynced build/grades/grader/gradingpanel.js and gradingpanel.min.js to
    //   match src. No PHP/DB/logic changes. version.php → 2026042200029.
    if ($oldversion < 2026042200029) {
        upgrade_plugin_savepoint(true, 2026042200029, 'gradingform', 'benchmarks');
    }
    // v1.2.30: AMD ENCODING FIX: All non-ASCII characters (em dashes, arrows, box-drawing chars, ellipsis, bullets, emoji, accented Latin) scrubbed from all AMD JS files (amd/src, amd/build, amd/build/*.min.js). Root cause of Moodle primary/secondary navigation menus disappearing site-wide: non-ASCII bytes in any installed plugin's AMD file cause a SyntaxError inside RequireJS's first.js bundle, throwing "No define call for core/first" and aborting the entire AMD module chain. No PHP, DB schema, or functional changes in this release.
    if ($oldversion < 2026042200030) {
        upgrade_plugin_savepoint(true, 2026042200030, 'gradingform', 'benchmarks');
    }

    // v1.2.31: AMD DEFINE FIX: Converted gradingpanel AMD build files
    //   (amd/build/grades/grader/gradingpanel.js and gradingpanel.min.js) from ES
    //   module syntax (import/export statements) to RequireJS define() format.
    //   ES module import statements in any AMD build file cause RequireJS to throw a
    //   SyntaxError when loading core/first, generating "No define call for
    //   core/first" and silently aborting the entire AMD chain -- hiding Moodle's
    //   primary and secondary navigation menus site-wide. No PHP, DB schema, or
    //   functional changes. version.php -> 2026042300031.
    if ($oldversion < 2026042300031) {
        upgrade_plugin_savepoint(true, 2026042300031, 'gradingform', 'benchmarks');
    }

    // v1.2.32: FIX-CURL-BATCH — tga/training_component.php switched from raw curl_init()
    //   to Moodle \curl wrapper. No DB schema changes.
    if ($oldversion < 2026051200032) {
        upgrade_plugin_savepoint(true, 2026051200032, 'gradingform', 'benchmarks');
    }

    // v1.2.33: FIX-BM-ZEROGRADE — new event observer (classes/observer.php +
    //   db/events.php) grants a next attempt when mod_assign fails to. Core's
    //   reopen_submission_if_required() uses grade_floats_different($grade, null)
    //   to detect whether a student is graded; because round(null,5) === round(0,5)
    //   === 0.0, a legitimate grade of exactly 0 is indistinguishable from
    //   "ungraded" and the gradepass comparison is never reached. Observer fires
    //   only for benchmarks-graded, untilpass assignments; guards maxattempts and
    //   idempotency. No schema change. Purge caches after deploy.
    if ($oldversion < 2026071100033) {
        if (function_exists('opcache_invalidate')) {
            $_pluginDir = realpath(__DIR__ . '/..');
            foreach (['lib.php', 'version.php', 'db/upgrade.php', 'db/events.php', 'classes/observer.php'] as $_f) {
                $_full = $_pluginDir . '/' . $_f;
                if (file_exists($_full)) {
                    opcache_invalidate($_full, true);
                }
            }
        } elseif (function_exists('opcache_reset')) {
            opcache_reset();
        }
        upgrade_plugin_savepoint(true, 2026071100033, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026071100034) {
        // v1.2.34 — PHP-only fix. No DB schema changes.
        // FIX-BM-ZEROGRADE-PROXY: assign_proxy subclass promotes
        // process_add_attempt() to public, fixing the "Call to protected method"
        // fatal exception thrown when an assessor saves a zero grade on a
        // benchmarks assignment with attemptreopenmethod = UNTILPASS.
        if (function_exists('opcache_invalidate')) {
            $_pluginDir = realpath(__DIR__ . '/..');
            foreach ([
                'version.php',
                'db/upgrade.php',
                'classes/observer.php',
                'classes/local/assign_proxy.php',
            ] as $_f) {
                $_full = $_pluginDir . '/' . $_f;
                if (file_exists($_full)) {
                    opcache_invalidate($_full, true);
                }
            }
        } elseif (function_exists('opcache_reset')) {
            opcache_reset();
        }
        upgrade_plugin_savepoint(true, 2026071100034, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026072300205) {
        // FIX-API-DOMAIN: Updated all API endpoint URLs from lms-labs.com to lms-labs.com.
        // lms-labs.com has no DNS resolution from Moodle server side; lms-labs.com is the
        // correct working domain. All ajax.php, api_client, unlock_verifier, lib.php calls updated.
        if (function_exists('opcache_invalidate')) {
            $_pluginDir = realpath(__DIR__ . '/..');
            foreach (['version.php', 'db/upgrade.php'] as $_f) {
                $_full = $_pluginDir . '/' . $_f;
                if (file_exists($_full)) {
                    opcache_invalidate($_full, true);
                }
            }
        } elseif (function_exists('opcache_reset')) {
            opcache_reset();
        }
        upgrade_plugin_savepoint(true, 2026072300205, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026072300206) {
        // FIX-API-DOMAIN: Reverted API endpoint to lms-labs.com (correct domain).
        // essaygraderai.app was the original single-plugin domain; lms-labs.com is correct.
        if (function_exists('opcache_invalidate')) {
            $_pluginDir = realpath(__DIR__ . '/..');
            foreach (['version.php', 'db/upgrade.php'] as $_f) {
                $_full = $_pluginDir . '/' . $_f;
                if (file_exists($_full)) { opcache_invalidate($_full, true); }
            }
        } elseif (function_exists('opcache_reset')) { opcache_reset(); }
        upgrade_plugin_savepoint(true, 2026072300206, 'gradingform', 'benchmarks');
    }

    if ($oldversion < 2026072300207) {
        // Domain update: lms-labs.com → lms-labs.com
        if (function_exists('opcache_invalidate')) {
            $_pluginDir = realpath(__DIR__ . '/..');
            foreach (['version.php', 'lib.php', 'db/upgrade.php'] as $_f) {
                $_full = $_pluginDir . '/' . $_f;
                if (file_exists($_full)) { opcache_invalidate($_full, true); }
            }
        } elseif (function_exists('opcache_reset')) { opcache_reset(); }
        upgrade_plugin_savepoint(true, 2026072300207, 'gradingform', 'benchmarks');
    }

    return true;
}