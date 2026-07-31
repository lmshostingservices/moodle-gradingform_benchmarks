<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace gradingform_benchmarks;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observers for gradingform_benchmarks.
 *
 * @package    gradingform_benchmarks
 * @copyright  2026 EssayGraderAI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * FIX-BM-ZEROGRADE: Grant a new attempt when a benchmarks-graded student
     * receives exactly grade 0.
     *
     * Moodle's assign::reopen_submission_if_required() (mod/assign/locallib.php)
     * calls grade_floats_different($gradebookgrade->grade, null) to determine
     * whether the student has been graded at all. Because PHP casts null to 0,
     * round(null,5) === round(0,5) === 0.0, so the comparison returns false for
     * a legitimate grade of zero — Moodle concludes the student is "ungraded"
     * and never reaches the gradepass comparison. The attempt is never reopened.
     *
     * In a competency-based RTO model, an assessor marking DNS / nothing-
     * demonstrated ticks zero criteria. gradingform_benchmarks correctly sums
     * these to 0 and writes grade 0.00 — but that grade is then invisible to
     * Moodle's reopen logic.
     *
     * This observer catches that exact case and calls process_add_attempt()
     * itself. It guards against every adjacent hazard:
     *   - Non-zero grades below gradepass → already handled by core; skip.
     *   - attemptreopenmethod != UNTILPASS → assessor or none; skip.
     *   - Non-benchmarks assignments → skip.
     *   - max attempts exhausted → skip.
     *   - Re-saving the same zero grade twice → idempotency check skips.
     *   - Genuinely ungraded → grade is null in gradebook; skip.
     *
     * @param \mod_assign\event\submission_graded $event
     */
    public static function submission_graded(\mod_assign\event\submission_graded $event): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->libdir . '/gradelib.php');

        $cmid      = $event->contextinstanceid;
        $studentid = $event->relateduserid;
        if (empty($cmid) || empty($studentid)) {
            return;
        }

        $cm = get_coursemodule_from_id('assign', $cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }

        $context = \context_module::instance($cmid);
        $course  = $DB->get_record('course', ['id' => $cm->course], '*', IGNORE_MISSING);
        if (!$course) {
            return;
        }

        // FIX-BM-ZEROGRADE (v1.2.34): use assign_proxy so process_add_attempt()
        // is accessible even when Moodle declares it protected in locallib.php.
        $assign   = new \gradingform_benchmarks\local\assign_proxy($context, $cm, $course);
        $instance = $assign->get_instance();

        // Only act when the assignment is configured for auto-reopen until pass.
        // Under MANUAL the assessor decides; under NONE no reopening is intended.
        if ($instance->attemptreopenmethod !== ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS) {
            return;
        }

        // Only act on assignments graded with THIS plugin. Rubric, guide, and
        // simple-graded assignments have their own logic — do not interfere.
        $gradingmanager = get_grading_manager($context, 'mod_assign', 'submissions');
        if ($gradingmanager->get_active_method() !== 'benchmarks') {
            return;
        }

        // Fetch the live gradebook grade and pass mark.
        $gradinginfo = grade_get_grades($cm->course, 'mod', 'assign', $instance->id, $studentid);
        if (empty($gradinginfo->items[0])) {
            return;
        }
        $gradeitem      = $gradinginfo->items[0];
        $gradebookgrade = $gradeitem->grades[$studentid] ?? null;

        // Genuinely ungraded — grade is null in the gradebook. Leave alone.
        if (!$gradebookgrade || $gradebookgrade->grade === null) {
            return;
        }

        // No pass mark configured — nothing to compare against. Leave alone.
        $gradepass = (float)($gradeitem->gradepass ?? 0);
        if ($gradepass <= 0) {
            return;
        }

        // THE BUG WINDOW: only a grade of exactly 0 is mishandled by core.
        //
        // Any non-zero grade below gradepass is ALREADY reopened correctly by
        // mod_assign. Acting on those too would double-add an attempt, which is
        // worse than the original bug. The grade_floats_different(..., 0.0)
        // check returns true for any non-zero value — we return early so only
        // a genuine zero reaches the code below.
        if (grade_floats_different((float)$gradebookgrade->grade, 0.0)) {
            return;
        }

        // Grade is exactly 0, which is below gradepass. Core should have
        // reopened this but did not. We do it now.

        $submission = $assign->get_user_submission($studentid, false);
        if (!$submission) {
            return;
        }

        // Respect maxattempts — mirror mod_assign's own guard exactly.
        $maxattemptsreached = $submission->attemptnumber >= ($instance->maxattempts - 1)
                              && $instance->maxattempts != ASSIGN_UNLIMITED_ATTEMPTS;
        if ($maxattemptsreached) {
            return;
        }

        // Idempotency: if a later attempt already exists (e.g. zero grade saved
        // twice), do nothing — the new attempt was already created on the first
        // save.
        $latest = $DB->get_field('assign_submission', 'MAX(attemptnumber)',
            ['assignment' => $instance->id, 'userid' => $studentid]);
        if ($latest !== false && (int)$latest > (int)$submission->attemptnumber) {
            return;
        }

        // Grant the attempt via the proxy — handles all related records
        // (submission plugins, grade rows) that a raw SQL INSERT would miss.
        try {
            $assign->add_attempt_for_user($studentid);
        } catch (\Exception $e) {
            debugging(
                'gradingform_benchmarks: FIX-BM-ZEROGRADE reopen failed for user '
                . $studentid . ' on cmid ' . $cmid . ': ' . $e->getMessage(),
                DEBUG_DEVELOPER
            );
        }
    }
}
