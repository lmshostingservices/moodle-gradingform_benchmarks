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

namespace gradingform_benchmarks\local;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Thin subclass of assign that promotes process_add_attempt() to public.
 *
 * In some Moodle versions process_add_attempt() is declared protected, which
 * means external code (e.g. an event observer) cannot call it directly.
 * Subclassing is the cleanest way to unlock access without duplicating the
 * complex add-attempt logic from locallib.php.
 *
 * FIX-BM-ZEROGRADE (v1.2.34): replaces the direct $assign->process_add_attempt()
 * call in observer.php that caused "Call to protected method" exceptions.
 */
class assign_proxy extends \assign {

    /**
     * Re-expose process_add_attempt() as public so external callers can use it
     * regardless of which Moodle version declared it protected.
     *
     * @param int $userid
     * @return bool
     */
    public function add_attempt_for_user(int $userid): bool {
        return $this->process_add_attempt($userid);
    }
}
