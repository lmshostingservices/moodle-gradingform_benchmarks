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
 * Grading panel for gradingform_benchmarks.
 *
 * @module     gradingform_benchmarks/grades/grader/gradingpanel
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('gradingform_benchmarks/grades/grader/gradingpanel', [
    'core/ajax',
    'core_grades/grades/grader/gradingpanel/normalise',
    'core_grades/grades/grader/gradingpanel/comparison',
    'jquery'
], function(Ajax, Normalise, Comparison, jQuery) {

    var fetchMany = Ajax.call;
    var normaliseResult = Normalise.normaliseResult;
    var compareData = Comparison.compareData;

    /**
     * For a given component, contextid, itemname & gradeduserid we can fetch the currently assigned grade.
     *
     * @param {String} component
     * @param {Number} contextid
     * @param {String} itemname
     * @param {Number} gradeduserid
     * @returns {Promise}
     */
    var fetchCurrentGrade = function(component, contextid, itemname, gradeduserid) {
        return fetchMany([{
            methodname: 'gradingform_benchmarks_grader_gradingpanel_fetch',
            args: {
                component: component,
                contextid: contextid,
                itemname: itemname,
                gradeduserid: gradeduserid,
            },
        }])[0];
    };

    /**
     * For a given component, contextid, itemname & gradeduserid we can store the currently assigned grade.
     *
     * @param {String} component
     * @param {Number} contextid
     * @param {String} itemname
     * @param {Number} gradeduserid
     * @param {Boolean} notifyUser
     * @param {HTMLElement} rootNode
     * @returns {Promise}
     */
    var storeCurrentGrade = function(component, contextid, itemname, gradeduserid, notifyUser, rootNode) {
        var form = rootNode.querySelector('form');
        if (compareData(form) === true) {
            return normaliseResult(fetchMany([{
                methodname: 'gradingform_benchmarks_grader_gradingpanel_store',
                args: {
                    component: component,
                    contextid: contextid,
                    itemname: itemname,
                    gradeduserid: gradeduserid,
                    notifyuser: notifyUser,
                    formdata: jQuery(form).serialize(),
                },
            }])[0]);
        } else {
            return Promise.resolve('');
        }
    };

    return {
        fetchCurrentGrade: fetchCurrentGrade,
        storeCurrentGrade: storeCurrentGrade
    };

});
