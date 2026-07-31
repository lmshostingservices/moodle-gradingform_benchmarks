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
 * Support for backup API
 *
 * @package    gradingform
 * @subpackage benchmarks
 * @copyright  2024 CBPlugins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines benchmarks backup structures.
 *
 * NOTE ON ELEMENT NAMING: All XML element names are prefixed with "benchmarks_"
 * to avoid collisions with other gradingform plugins (e.g. gradingform_checklist)
 * that share the same global optigroup element registry. Using generic names like
 * "groups" or "items" causes a multiple_optigroup_duplicate_element exception when
 * both plugins are installed simultaneously.
 */
class backup_gradingform_benchmarks_plugin extends backup_gradingform_plugin {

    /**
     * Declares benchmarks structures to append to the grading form definition
     * @return backup_plugin_element
     */
    protected function define_definition_plugin_structure() {

        // Append data only if the grand-parent element has 'method' set to 'benchmarks'
        $plugin = $this->get_plugin_element(null, '../../method', 'benchmarks');

        // Create a visible container for our data
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect our visible container to the parent
        $plugin->add_child($pluginwrapper);

        // Define our elements — prefixed with "benchmarks_" to avoid optigroup name collisions.
        $groups = new backup_nested_element('benchmarks_groups');

        $group = new backup_nested_element('benchmarks_group', array('id'), array(
                'sortorder', 'description', 'descriptionformat'));

        $items = new backup_nested_element('benchmarks_items');

        $item = new backup_nested_element('benchmarks_item', array('id'), array('sortorder',
                'score', 'definition', 'definitionformat'));

        // Build elements hierarchy
        $pluginwrapper->add_child($groups);
        $groups->add_child($group);
        $group->add_child($items);
        $items->add_child($item);

        // Set sources to populate the data

        $group->set_source_table('gradingbench_grp',
            array('definitionid' => backup::VAR_PARENTID));

        $item->set_source_table('gradingbench_items',
            array('groupid' => backup::VAR_PARENTID));

        return $plugin;
    }

    /**
     * Declares benchmarks structures to append to the grading form instances
     * @return backup_plugin_element
     */
    protected function define_instance_plugin_structure() {

        // Append data only if the ancestor 'definition' element has 'method' set to 'benchmarks'
        $plugin = $this->get_plugin_element(null, '../../../../method', 'benchmarks');

        // Create a visible container for our data
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect our visible container to the parent
        $plugin->add_child($pluginwrapper);

        // Define our elements — prefixed with "benchmarks_" to avoid optigroup name collisions.

        $fillings = new backup_nested_element('benchmarks_fillings');

        $filling = new backup_nested_element('benchmarks_filling', array('id'), array(
            'groupid', 'itemid', 'checked', 'remark', 'remarkformat'));

        // Build elements hierarchy

        $pluginwrapper->add_child($fillings);
        $fillings->add_child($filling);

        // Set sources to populate the data

        $filling->set_source_table('gradingbench_fills',
            array('instanceid' => backup::VAR_PARENTID));

        return $plugin;
    }
}
