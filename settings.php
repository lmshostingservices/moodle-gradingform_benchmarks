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
 * Settings for gradingform_benchmarks.
 *
 * @package   gradingform_benchmarks
 * @copyright 2025 Essay Grader AI
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Moodle 4.x does not include gradingform plugins in the standard admin settings
// loading loop, so $settings (passed in from load_plugin_settings) may be null here.
// We must therefore create the settings page ourselves AND register it in the admin
// tree under a parent category that is guaranteed to exist.
//
// Parent candidates:
//   'localplugins' — the "Local plugins" category (always present, always loaded)
//   'modsettings'  — "Activity modules" (always present)
//
// We use 'localplugins' as the most semantically appropriate home for credentials/
// unlock settings shared across plugin types, but guard with ADMIN->locate() to
// avoid adding a duplicate node if the admin tree was already populated.

if ($hassiteconfig) {
    if (!$ADMIN->locate('gradingformbenchmarks')) {
        // Create the settings page.
        $page = new admin_settingpage('gradingformbenchmarks', get_string('pluginname', 'gradingform_benchmarks'));

        // Check if Central Config plugin is installed (provides site-wide credentials)
        $centralconfiginstalled = file_exists($CFG->dirroot . '/local/aiconfig/version.php');

        // API Credentials heading
        $page->add(new admin_setting_heading(
            'gradingform_benchmarks/apicredentials',
            get_string('apicredentials', 'gradingform_benchmarks'),
            get_string('apicredentials_desc', 'gradingform_benchmarks')
        ));

        // Site ID (fallback if Central Config not installed)
        $page->add(new admin_setting_configtext(
            'gradingform_benchmarks/siteid',
            get_string('siteid', 'gradingform_benchmarks'),
            get_string('siteid_desc', 'gradingform_benchmarks') . ($centralconfiginstalled ? ' ' . get_string('centralconfig_fallback', 'gradingform_benchmarks') : ''),
            '',
            PARAM_TEXT
        ));

        // API Key (fallback if Central Config not installed)
        $page->add(new admin_setting_configpasswordunmask(
            'gradingform_benchmarks/apikey',
            get_string('apikey', 'gradingform_benchmarks'),
            get_string('apikey_desc', 'gradingform_benchmarks') . ($centralconfiginstalled ? ' ' . get_string('centralconfig_fallback', 'gradingform_benchmarks') : ''),
            ''
        ));

        // Add to localplugins (guaranteed to exist in Moodle 4.x admin tree).
        $ADMIN->add('localplugins', $page);
    }

    // If Moodle pre-created a $settings page for us (which happens on some Moodle builds
    // that DO iterate gradingform settings), populate it with our settings as well so that
    // the built-in breadcrumb path is honoured.
    if ($settings instanceof admin_settingpage) {
        $centralconfiginstalled = isset($centralconfiginstalled) ? $centralconfiginstalled
            : file_exists($CFG->dirroot . '/local/aiconfig/version.php');

        $settings->add(new admin_setting_heading(
            'gradingform_benchmarks/apicredentials_alt',
            get_string('apicredentials', 'gradingform_benchmarks'),
            get_string('apicredentials_desc', 'gradingform_benchmarks')
        ));

        $settings->add(new admin_setting_configtext(
            'gradingform_benchmarks/siteid',
            get_string('siteid', 'gradingform_benchmarks'),
            get_string('siteid_desc', 'gradingform_benchmarks') . ($centralconfiginstalled ? ' ' . get_string('centralconfig_fallback', 'gradingform_benchmarks') : ''),
            '',
            PARAM_TEXT
        ));

        $settings->add(new admin_setting_configpasswordunmask(
            'gradingform_benchmarks/apikey',
            get_string('apikey', 'gradingform_benchmarks'),
            get_string('apikey_desc', 'gradingform_benchmarks') . ($centralconfiginstalled ? ' ' . get_string('centralconfig_fallback', 'gradingform_benchmarks') : ''),
            ''
        ));
    }
}
