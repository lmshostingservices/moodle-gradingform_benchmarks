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
 * Support for restore API
 *
 * @package    gradingform_benchmarks
 * @subpackage benchmarks
 * @copyright  2024 CBPlugins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines benchmarks restore structures.
 *
 * NOTE: Paths must match the prefixed XML element names used in the backup:
 * benchmarks_groups / benchmarks_group / benchmarks_items / benchmarks_item / benchmarks_fillings / benchmarks_filling
 */
class restore_gradingform_benchmarks_plugin extends restore_gradingform_plugin {
    /**
     * Declares the benchmarks XML paths attached to the form definition element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_definition_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradingform_benchmarks_group',
            $this->get_pathfor('/benchmarks_groups/benchmarks_group'));

        $paths[] = new restore_path_element('gradingform_benchmarks_item',
            $this->get_pathfor('/benchmarks_groups/benchmarks_group/benchmarks_items/benchmarks_item'));

        return $paths;
    }

    /**
     * Declares the benchmarks XML paths attached to the form instance element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_instance_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradingform_benchmarks_filling',
            $this->get_pathfor('/benchmarks_fillings/benchmarks_filling'));

        return $paths;
    }

    /**
     * Processes group element data
     *
     * @param stdClass $data
     */
    public function process_gradingform_benchmarks_group($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->definitionid = $this->get_new_parentid('grading_definition');

        $newid = $DB->insert_record('gradingbench_grp', $data);
        $this->set_mapping('gradingform_benchmarks_group', $oldid, $newid);
    }

    /**
     * Processes item element data
     *
     * @param stdClass $data
     */
    public function process_gradingform_benchmarks_item($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->groupid = $this->get_new_parentid('gradingform_benchmarks_group');

        $newid = $DB->insert_record('gradingbench_items', $data);
        $this->set_mapping('gradingform_benchmarks_item', $oldid, $newid);
    }

    /**
     * Processes filling element data
     *
     * @param stdClass $data
     */
    public function process_gradingform_benchmarks_filling($data) {
        global $DB;

        $data = (object)$data;
        $data->instanceid = $this->get_new_parentid('grading_instance');
        $data->groupid = $this->get_mappingid('gradingform_benchmarks_group', $data->groupid);
        $data->itemid = $this->get_mappingid('gradingform_benchmarks_item', $data->itemid);

        $DB->insert_record('gradingbench_fills', $data);
    }
}
