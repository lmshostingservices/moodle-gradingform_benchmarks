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
 * gradingform_benchmarks file.
 *
 * @package    gradingform_benchmarks
 * @copyright  2026 LMS-Labs
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

// This file is part of Moodle - http://moodle.org/
//
// Assignment Benchmarks
// Enhanced grading method with TGA integration for lms-labs.com
// Based on the standard gradingform_benchmarks plugin with bulk paste and training.gov.au import.

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'gradingform_benchmarks';

// Version above official plugin to prevent Moodle "updates".
$plugin->version   = 2026072300;   // 2026-07-17, v1.2.34

// Minimum Moodle version required (4.0+).
$plugin->requires  = 2022041900;

// Moodle 4.0 - 5.x supported.
$plugin->supported = [400, 500];

$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.2.38'; // FIX-BM-ZEROGRADE-PROXY (v1.2.34): assign_proxy subclass promotes process_add_attempt() to public — fixes "Call to protected method assign::process_add_attempt() from scope gradingform_benchmarks\observer" on Moodle installs where that method is declared protected. No schema change.

