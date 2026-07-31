<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Event observers for gradingform_benchmarks.
 *
 * FIX-BM-ZEROGRADE (v1.2.33): Subscribe to submission_graded so the plugin
 * can grant a new attempt when Moodle core fails to. Core's
 * reopen_submission_if_required() uses grade_floats_different($grade, null)
 * to detect whether a student is graded. Because round(null,5) === round(0,5)
 * === 0.0, a genuine grade of exactly 0 is treated as "ungraded" — the
 * gradepass comparison is skipped and the attempt is never opened.
 *
 * 'internal' => false is mandatory: the observer must run AFTER mod_assign
 * commits its transaction so we can read the final grade state.
 *
 * @package    gradingform_benchmarks
 * @copyright  2026 EssayGraderAI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\mod_assign\event\submission_graded',
        'callback'  => '\gradingform_benchmarks\observer::submission_graded',
        'priority'  => 0,
        'internal'  => false,
    ],
];
