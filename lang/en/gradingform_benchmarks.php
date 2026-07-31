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
 * Language file for the Assignment Benchmarks grading form plugin
 *
 * Assignment Benchmarks provides a structured checklist-based grading approach
 * where assessors mark criteria as achieved/not achieved. It is designed for
 * competency-based assessment (VET/RTO) where students must demonstrate
 * specific skills or knowledge against defined benchmarks.
 *
 * NOTE: This plugin uses "benchmark" to mean a measurable grading criterion
 * (like a checklist item), NOT a performance band or standards mastery level.
 * Teachers define benchmarks, students demonstrate them, assessors check them off.
 *
 * @package    gradingform_benchmarks
 * @copyright  2025 AI Grader (lms-labs.com)
 * @copyright  Based on Open LMS Checklist (2012)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'Add benchmark group';
$string['alwaysshowdefinition'] = 'Allow students to preview benchmarks before submission (otherwise benchmarks only visible after grading)';
$string['backtoediting'] = 'Back to editing';
$string['checked'] = 'Achieved';
$string['checkitem'] = 'Mark as achieved: "{$a}"';
$string['checklist'] = 'Assignment Benchmarks';
$string['checklistmapping'] = 'Score to grade mapping rules';
$string['checklistmappingexplained'] = 'The minimum possible score is <b>{$a->minscore} points</b> (converted to minimum grade, usually zero).
    The maximum score <b>{$a->maxscore} points</b> converts to maximum grade.<br />
    Intermediate scores are converted proportionally and rounded to the nearest available grade.<br />
    If using a scale instead of points, scores convert to scale elements as consecutive integers.';
$string['checklistoptions'] = 'Benchmark options';
$string['checkliststatus'] = 'Current benchmark status';
$string['confirmdeletegroup'] = 'Are you sure you want to delete this benchmark group?';
$string['confirmdeleteitem'] = 'Are you sure you want to delete this benchmark item?';
$string['definechecklist'] = 'Define benchmarks';
$string['description'] = 'Description';
$string['err_definitionmax'] = 'Benchmark definition cannot exceed 255 characters';
$string['err_descriptionmax'] = 'Group description cannot exceed 255 characters';
$string['err_nodefinition'] = 'Benchmark definition cannot be empty';
$string['err_nodescription'] = 'Group description cannot be empty';
$string['err_nogroups'] = 'Benchmarks must contain at least one group';
$string['err_scoreformat'] = 'Points for each benchmark must be a valid non-negative number';
$string['err_scoremax'] = 'Points for each benchmark must not exceed 1000';
$string['err_totalscore'] = 'Maximum points possible must be greater than zero';
$string['groupfeedback'] = 'Group feedback for "{$a}"';
$string['gradingof'] = '{$a} grading';
$string['groupadditem'] = 'Add benchmark';
$string['groupdelete'] = 'Delete group';
$string['groupdescription'] = 'Group description';
$string['groupempty'] = 'Click to edit group';
$string['groupmovedown'] = 'Move down';
$string['groupmoveup'] = 'Move up';
$string['grouppoints'] = 'Group points';
$string['groupremark'] = 'Group remark for "{$a}"';
$string['itemdefinition'] = 'Benchmark definition';
$string['itemdelete'] = 'Delete benchmark';
$string['itemempty'] = 'Click to edit benchmark';
$string['itemfeedback'] = 'Feedback for "{$a}"';
$string['itemremark'] = 'Benchmark remark for "{$a}"';
$string['itemscore'] = 'Benchmark score';
$string['name'] = 'Assignment Benchmarks';
$string['needregrademessage'] = 'The benchmark definition was changed after this student was graded. The student cannot see these benchmarks until you review and update the grade.';
$string['pluginname'] = 'Assignment Benchmarks';
$string['pluginname_help'] = 'Assignment Benchmarks is a structured grading method where teachers define groups of criteria (benchmarks) that students must demonstrate. Each benchmark can be marked as achieved or not achieved. This is ideal for competency-based assessment where specific skills or knowledge must be evidenced.';
$string['pluginname_link'] = 'gradingform_benchmarks';
$string['previewchecklist'] = 'Preview benchmarks';
$string['overallpoints'] = 'Overall points';
$string['regrademessage1'] = 'You are saving changes to benchmarks already used for grading. Indicate if existing grades need review. If set, benchmarks will be hidden from students until items are regraded.';
$string['regrademessage5'] = 'You are saving significant changes to benchmarks already used for grading. Gradebook values are unchanged, but benchmarks will be hidden from students until items are regraded.';
$string['regradeoption0'] = 'Do not mark for regrade';
$string['regradeoption1'] = 'Mark for regrade';
$string['restoredfromdraft'] = 'NOTE: The last grading attempt was not saved properly so draft grades have been restored. Use Cancel to discard these changes.';
$string['save'] = 'Save';
$string['savechecklist'] = 'Save benchmarks and make ready';
$string['savechecklistdraft'] = 'Save as draft';
$string['scorepostfix'] = '{$a} points';
$string['showitempointseval'] = 'Display points for each benchmark during evaluation';
$string['showitempointstudent'] = 'Display points for each benchmark to students';
$string['enableitemremarks'] = 'Allow grader to add remarks for each benchmark';
$string['enablegroupremarks'] = 'Allow grader to add remarks for each benchmark group';
$string['showremarksstudent'] = 'Show all remarks to students';
$string['unchecked'] = 'Not achieved';
$string['maxlengthalert'] = 'This field has a maximum length of {$a} characters';

$string['bulkbuilderheading'] = 'Bulk add benchmark groups and items';
$string['bulkbuilderplaceholder'] = 'Paste your benchmark criteria here...';
$string['bulkbuilderhelp'] = 'Bulk paste replaces the current benchmarks. Choose a format below that matches your content, then paste and click Preview before inserting.';
$string['bulkbuilderpreview'] = 'Preview';
$string['bulkbuilderapply'] = 'Insert into benchmarks';

$string['bulkbuilderformatlabel'] = 'Format:';
$string['bulkbuilderformat_parts'] = 'Part A / B / C style (Part headers + numbered criteria)';
$string['bulkbuilderformat_outline'] = 'Numbered outline (1. Group — 1.1, 1.2 items)';
$string['bulkbuilderformat_markdown'] = 'Markdown (# Group — bullet items)';

$string['bulkbuilderdownloadprompt'] = 'Download ChatGPT Prompt';
$string['bulkbuilderdownloadprompt_title'] = 'Download a ready-made ChatGPT prompt for the selected format';
$string['bulkbuilderdownloadprompt_hint'] = 'Need help formatting? ';

$string['tgaimportheading'] = 'Import from training.gov.au';
$string['tgaimporthelp'] = 'Enter a unit of competency code to import elements, performance criteria, performance evidence, and knowledge evidence as benchmark groups and items.';
$string['tgaunitcode'] = 'Unit code';
$string['tgaunitcodeplaceholder'] = 'e.g. BSBWHS411';
$string['tgafetch'] = 'Fetch unit';
$string['tgafetching'] = 'Fetching...';
$string['tgainsert'] = 'Insert selected';
$string['tgaincludeelements'] = 'Elements & Performance Criteria';
$string['tgaincludepe'] = 'Performance Evidence';
$string['tgaincludeke'] = 'Knowledge Evidence';
$string['tgaautonumber'] = 'Auto-number items (1, 1.1, 1.2...)';
$string['tgaunitfound'] = 'Found: {$a}';
$string['unitnotfound'] = 'Unit "{$a}" not found on training.gov.au';
$string['tganoselection'] = 'Please select at least one option to import';
$string['tgapegroup'] = 'Performance Evidence';
$string['tgakegroup'] = 'Knowledge Evidence';

$string['benchmarks:editdefinition'] = 'Edit benchmark definitions';
$string['benchmarks:grade'] = 'Grade using benchmarks';
$string['benchmarks:viewresults'] = 'View benchmark grading results';

// Unlock verification
$string['unlock_required'] = 'This plugin requires 1000 AI credits to unlock. Please visit your AI Grader dashboard at lms-labs.com to unlock this plugin.';

// API Credentials
$string['apicredentials'] = 'API Credentials';
$string['apicredentials_desc'] = 'Enter your AI Grader credentials to enable plugin unlock verification. These credentials are available from your AI Grader dashboard at lms-labs.com.';
$string['siteid'] = 'Site ID';
$string['siteid_desc'] = 'Your unique Site ID from the AI Grader dashboard.';
$string['apikey'] = 'API Key';
$string['apikey_desc'] = 'Your API Key from the AI Grader dashboard.';
$string['centralconfig_fallback'] = '(Fallback - Central Config takes priority if installed)';
