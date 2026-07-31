<?php
/**
 * External function to fetch TGA unit of competency data.
 *
 * @package    gradingform_benchmarks
 * @copyright  2025 AI Grader <support@lmshostingservices.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_benchmarks\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

class get_unit extends external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'code' => new external_value(PARAM_ALPHANUMEXT, 'Unit of competency code'),
        ]);
    }

    public static function execute(string $code): array {
        global $USER;
        
        $params = self::validate_parameters(self::execute_parameters(), ['code' => $code]);
        $code = strtoupper(trim($params['code']));
        
        $context = \context_system::instance();
        self::validate_context($context);
        
        require_login();
        
        $tga = new \gradingform_benchmarks\tga\training_component();
        $unit = $tga->get_unit($code);
        
        if (!$unit) {
            return [
                'success' => false,
                'code' => $code,
                'title' => '',
                'elements' => [],
                'performanceevidence' => [],
                'knowledgeevidence' => [],
                'error' => get_string('unitnotfound', 'gradingform_benchmarks', $code),
            ];
        }
        
        $elements = [];
        foreach ($unit['elements'] as $el) {
            $pcs = [];
            foreach ($el['performanceCriteria'] as $pc) {
                $pcs[] = ['text' => $pc];
            }
            $elements[] = [
                'code' => $el['code'],
                'name' => $el['name'],
                'performancecriteria' => $pcs,
            ];
        }
        
        $pe = [];
        foreach ($unit['performanceEvidence'] as $item) {
            $pe[] = ['text' => $item];
        }
        
        $ke = [];
        foreach ($unit['knowledgeEvidence'] as $item) {
            $ke[] = ['text' => $item];
        }
        
        return [
            'success' => true,
            'code' => $unit['code'],
            'title' => $unit['title'],
            'elements' => $elements,
            'performanceevidence' => $pe,
            'knowledgeevidence' => $ke,
            'error' => '',
        ];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the fetch was successful'),
            'code' => new external_value(PARAM_TEXT, 'Unit code'),
            'title' => new external_value(PARAM_TEXT, 'Unit title'),
            'elements' => new external_multiple_structure(
                new external_single_structure([
                    'code' => new external_value(PARAM_TEXT, 'Element code'),
                    'name' => new external_value(PARAM_TEXT, 'Element name'),
                    'performancecriteria' => new external_multiple_structure(
                        new external_single_structure([
                            'text' => new external_value(PARAM_TEXT, 'Performance criterion text'),
                        ])
                    ),
                ])
            ),
            'performanceevidence' => new external_multiple_structure(
                new external_single_structure([
                    'text' => new external_value(PARAM_TEXT, 'Performance evidence item'),
                ])
            ),
            'knowledgeevidence' => new external_multiple_structure(
                new external_single_structure([
                    'text' => new external_value(PARAM_TEXT, 'Knowledge evidence item'),
                ])
            ),
            'error' => new external_value(PARAM_TEXT, 'Error message if failed'),
        ]);
    }
}
