<?php
/**
 * Assignment Benchmarks - TGA Training Component API.
 * 
 * Hybrid approach using 3 data sources:
 * 1. XML Files (Primary, fastest): https://training.gov.au/TrainingComponentFiles/{Package}/{Code}_R{Release}.xml
 * 2. REST API (Metadata): https://training.gov.au/api/Training/{code} - Returns title, release dates
 * 3. SOAP API (Fallback): V13 endpoint with WS-Security - Only used if XML fails
 *
 * @package    gradingform_benchmarks
 * @copyright  2025 AI Grader <support@lmshostingservices.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_benchmarks\tga;

defined('MOODLE_INTERNAL') || die();

class training_component {

    private const WSDL = 'https://ws.training.gov.au/Deewr.Tga.Webservices/TrainingComponentServiceV13.svc?wsdl';
    private const XML_BASE = 'https://training.gov.au/TrainingComponentFiles/';
    private const REST_BASE = 'https://training.gov.au/api/Training/';
    private const CACHE_TTL = 2592000;

    private $client = null;
    private $username;
    private $password;

    public function __construct() {
        global $CFG;
        $this->username = get_config('gradingform_benchmarks', 'tga_username');
        $this->password = get_config('gradingform_benchmarks', 'tga_password');
        
        if (empty($this->username) && !empty($CFG->TGA_USERNAME)) {
            $this->username = $CFG->TGA_USERNAME;
        }
        if (empty($this->password) && !empty($CFG->TGA_PASSWORD)) {
            $this->password = $CFG->TGA_PASSWORD;
        }
    }

    public function get_unit(string $code): ?array {
        $code = strtoupper(trim($code));

        try {
            $xmldata = $this->fetch_xml_content($code);
            $metadata = $this->fetch_rest_metadata($code);
            
            if ($xmldata && !empty($xmldata['elements'])) {
                return [
                    'code' => $code,
                    'title' => $metadata['title'] ?? "Unit $code",
                    'releaseDate' => $metadata['releaseDate'] ?? date('Y-m-d'),
                    'description' => $xmldata['application'] ?? '',
                    'elements' => $xmldata['elements'],
                    'performanceEvidence' => $xmldata['performanceEvidence'] ?? [],
                    'knowledgeEvidence' => $xmldata['knowledgeEvidence'] ?? [],
                    'assessmentConditions' => $xmldata['assessmentConditions'] ?? [],
                ];
            }
            
            $result = $this->call_soap_api($code);
            return $result;
            
        } catch (\Exception $e) {
            debugging('TGA API error: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return null;
        }
    }

    private function fetch_xml_content(string $code, int $release = 1): ?array {
        $package = $this->extract_package_code($code);
        $url = self::XML_BASE . $package . '/' . $code . '_R' . $release . '.xml';
        
        $xml = $this->http_get($url, 15);
        if (!$xml) {
            $altpackages = ['BSB', 'ICT', 'TAE', 'HLT', 'CHC', 'SIT', 'AHC', 'CPC', 'RII', 'TLI', 'AUR', 'MEM', 'MSM', 'UEE', 'CPP'];
            foreach ($altpackages as $pkg) {
                if ($pkg === $package) continue;
                $alturl = self::XML_BASE . $pkg . '/' . $code . '_R' . $release . '.xml';
                $xml = $this->http_get($alturl, 10);
                if ($xml) break;
            }
        }
        
        if (!$xml) {
            return null;
        }
        
        return $this->parse_xml_content($xml);
    }

    private function parse_xml_content(string $xml): array {
        $elements = [];
        $performanceEvidence = [];
        $knowledgeEvidence = [];
        $assessmentConditions = [];
        $application = '';
        
        // Extract all text content from HTML tags (p, li, div)
        $textContent = $this->extract_all_text_content($xml);
        
        $currentElement = null;
        $inElementsSection = false;
        $inPerformanceEvidence = false;
        $inKnowledgeEvidence = false;
        $inAssessmentConditions = false;
        $inApplication = false;
        
        foreach ($textContent as $text) {
            $lower = strtolower($text);
            
            // Detect section headers
            if (strpos($lower, 'elements and performance criteria') !== false) {
                $inElementsSection = true;
                $inPerformanceEvidence = $inKnowledgeEvidence = $inAssessmentConditions = $inApplication = false;
                continue;
            }
            if (strpos($lower, 'performance evidence') !== false && strlen($text) < 50) {
                if ($currentElement) {
                    $elements[] = $currentElement;
                    $currentElement = null;
                }
                $inPerformanceEvidence = true;
                $inElementsSection = $inKnowledgeEvidence = $inAssessmentConditions = $inApplication = false;
                continue;
            }
            if (strpos($lower, 'knowledge evidence') !== false && strlen($text) < 50) {
                $inKnowledgeEvidence = true;
                $inElementsSection = $inPerformanceEvidence = $inAssessmentConditions = $inApplication = false;
                continue;
            }
            if (strpos($lower, 'assessment conditions') !== false && strlen($text) < 50) {
                $inAssessmentConditions = true;
                $inElementsSection = $inPerformanceEvidence = $inKnowledgeEvidence = $inApplication = false;
                continue;
            }
            if ($lower === 'application') {
                $inApplication = true;
                $inElementsSection = $inPerformanceEvidence = $inKnowledgeEvidence = $inAssessmentConditions = false;
                continue;
            }
            
            // Elements and performance criteria
            if ($inElementsSection) {
                if (preg_match('/^(\d+)\.\s+(.+)$/', $text, $m)) {
                    if ($currentElement) {
                        $elements[] = $currentElement;
                    }
                    $currentElement = [
                        'code' => $m[1],
                        'name' => $m[2],
                        'performanceCriteria' => []
                    ];
                } elseif (preg_match('/^(\d+)\.(\d+)\s+(.+)$/', $text, $m) && $currentElement) {
                    $currentElement['performanceCriteria'][] = $m[3];
                }
            }
            
            // Performance evidence - flatten and simplify
            if ($inPerformanceEvidence && strlen($text) > 3) {
                $items = $this->flatten_evidence_text($text);
                foreach ($items as $item) {
                    if (!empty($item)) {
                        $performanceEvidence[] = $item;
                    }
                }
            }
            
            // Knowledge evidence - flatten and simplify
            if ($inKnowledgeEvidence && strlen($text) > 3) {
                $items = $this->flatten_evidence_text($text);
                foreach ($items as $item) {
                    if (!empty($item)) {
                        $knowledgeEvidence[] = $item;
                    }
                }
            }
            
            // Assessment conditions
            if ($inAssessmentConditions && strlen($text) > 3) {
                $items = $this->flatten_evidence_text($text);
                foreach ($items as $item) {
                    if (!empty($item)) {
                        $assessmentConditions[] = $item;
                    }
                }
            }
            
            // Application
            if ($inApplication && strlen($text) > 20) {
                $application .= ($application ? ' ' : '') . $text;
            }
        }
        
        if ($currentElement) {
            $elements[] = $currentElement;
        }
        
        // Deduplicate and clean evidence arrays
        $performanceEvidence = $this->clean_evidence_array($performanceEvidence);
        $knowledgeEvidence = $this->clean_evidence_array($knowledgeEvidence);
        $assessmentConditions = $this->clean_evidence_array($assessmentConditions);
        
        return [
            'elements' => $elements,
            'performanceEvidence' => $performanceEvidence,
            'knowledgeEvidence' => $knowledgeEvidence,
            'assessmentConditions' => $assessmentConditions,
            'application' => $application
        ];
    }
    
    /**
     * Extract all text content from HTML (p, li, div, span tags)
     */
    private function extract_all_text_content(string $html): array {
        $textContent = [];
        
        // Match li tags first (often contain the nested criteria)
        preg_match_all('/<li[^>]*>(.*?)<\/li>/si', $html, $liMatches);
        foreach ($liMatches[1] as $li) {
            $text = trim(strip_tags($li));
            if (!empty($text) && strlen($text) > 2) {
                $textContent[] = $text;
            }
        }
        
        // Match p tags
        preg_match_all('/<p[^>]*>(.*?)<\/p>/si', $html, $pMatches);
        foreach ($pMatches[1] as $p) {
            $text = trim(strip_tags($p));
            if (!empty($text) && strlen($text) > 2) {
                $textContent[] = $text;
            }
        }
        
        return $textContent;
    }
    
    /**
     * Flatten evidence text into simple criteria items
     * Handles "including:", colon patterns, and nested structures
     */
    private function flatten_evidence_text(string $text): array {
        $items = [];
        
        // Clean leading bullets/markers
        $text = preg_replace('/^[\-\•\*\○\●]\s*/', '', trim($text));
        
        // Skip section headers or intro text
        if (preg_match('/^(the candidate must|during the above|there must be)/i', $text)) {
            // These are often intro paragraphs, skip them
            return [];
        }
        
        // Remove "including:" suffixes - the next items will be the actual criteria
        $text = preg_replace('/,?\s*including:?\s*$/i', '', $text);
        
        // If text ends with colon, it's a category header - skip it
        if (preg_match('/:$/s', trim($text))) {
            return [];
        }
        
        // Handle multi-part items split by semicolons or line breaks
        if (strpos($text, ';') !== false) {
            $parts = preg_split('/\s*;\s*/', $text);
            foreach ($parts as $part) {
                $part = trim($part);
                if (strlen($part) > 3) {
                    $items[] = $this->simplify_criterion($part);
                }
            }
            return $items;
        }
        
        // Single item
        if (strlen($text) > 3) {
            $items[] = $this->simplify_criterion($text);
        }
        
        return $items;
    }
    
    /**
     * Simplify a criterion text to be clear and readable
     */
    private function simplify_criterion(string $text): string {
        // Clean up text
        $text = trim($text);
        
        // Remove leading bullets/markers
        $text = preg_replace('/^[\-\•\*\○\●]\s*/', '', $text);
        
        // Remove trailing periods if present
        $text = rtrim($text, '.');
        
        // Capitalize first letter if not already
        if (!empty($text) && ctype_lower($text[0])) {
            $text = ucfirst($text);
        }
        
        // Truncate extremely long criteria (over 200 chars) - keep first sentence
        if (strlen($text) > 200) {
            $sentences = preg_split('/(?<=[.!?])\s+/', $text, 2);
            if (count($sentences) > 1 && strlen($sentences[0]) > 20) {
                $text = rtrim($sentences[0], '.');
            }
        }
        
        return $text;
    }
    
    /**
     * Clean and deduplicate an evidence array
     */
    private function clean_evidence_array(array $items): array {
        $cleaned = [];
        $seen = [];
        
        foreach ($items as $item) {
            if (empty($item)) continue;
            
            // Skip duplicates (case-insensitive)
            $key = strtolower(trim($item));
            if (isset($seen[$key])) continue;
            
            // Skip very short items
            if (strlen($item) < 5) continue;
            
            // Skip items that are just headers/titles
            if (preg_match('/^(key|principles|techniques|requirements|policies|procedures|statutory|regulatory)\s*$/i', $item)) {
                continue;
            }
            
            $seen[$key] = true;
            $cleaned[] = $item;
        }
        
        return $cleaned;
    }

    private function fetch_rest_metadata(string $code): array {
        $url = self::REST_BASE . $code;
        $json = $this->http_get($url, 10);
        
        if (!$json) {
            return [];
        }
        
        $data = json_decode($json, true);
        if (!$data) {
            return [];
        }
        
        return [
            'title' => $data['Title'] ?? $data['title'] ?? '',
            'releaseDate' => $data['ReleaseDate'] ?? $data['releaseDate'] ?? date('Y-m-d')
        ];
    }

    private function call_soap_api(string $code): ?array {
        if (empty($this->username) || empty($this->password)) {
            return null;
        }

        try {
            if (!$this->client) {
                $this->client = new \SoapClient(self::WSDL, [
                    'trace' => true,
                    'exceptions' => true,
                    'cache_wsdl' => WSDL_CACHE_BOTH,
                    'connection_timeout' => 30,
                    'login' => $this->username,
                    'password' => $this->password
                ]);
            }

            $request = new \stdClass();
            $request->request = new \stdClass();
            $request->request->Code = $code;
            $request->request->IncludeLegacyData = false;

            $response = $this->client->GetDetails($request);

            if (empty($response->TrainingComponent)) {
                return null;
            }

            return $this->normalize_soap_unit($response->TrainingComponent);
            
        } catch (\Exception $e) {
            debugging('SOAP API error: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return null;
        }
    }

    private function normalize_soap_unit($tc): array {
        $unit = [
            'code' => $tc->Code ?? '',
            'title' => $tc->Title ?? '',
            'description' => $tc->Description ?? '',
            'elements' => [],
            'performanceEvidence' => [],
            'knowledgeEvidence' => [],
            'assessmentConditions' => [],
        ];

        if (!empty($tc->Releases->Release)) {
            $releases = is_array($tc->Releases->Release)
                ? $tc->Releases->Release
                : [$tc->Releases->Release];

            $latestRelease = end($releases);

            if (!empty($latestRelease->UnitElements->UnitElement)) {
                $elements = is_array($latestRelease->UnitElements->UnitElement)
                    ? $latestRelease->UnitElements->UnitElement
                    : [$latestRelease->UnitElements->UnitElement];

                foreach ($elements as $element) {
                    $el = [
                        'code' => $element->Code ?? '',
                        'name' => $element->Name ?? '',
                        'performanceCriteria' => []
                    ];

                    if (!empty($element->PerformanceCriteria->PerformanceCriterion)) {
                        $criteria = is_array($element->PerformanceCriteria->PerformanceCriterion)
                            ? $element->PerformanceCriteria->PerformanceCriterion
                            : [$element->PerformanceCriteria->PerformanceCriterion];

                        foreach ($criteria as $pc) {
                            $el['performanceCriteria'][] = $pc->Name ?? $pc;
                        }
                    }

                    $unit['elements'][] = $el;
                }
            }

            if (!empty($latestRelease->PerformanceEvidence)) {
                $unit['performanceEvidence'] = $this->parse_evidence_list($latestRelease->PerformanceEvidence);
            }

            if (!empty($latestRelease->KnowledgeEvidence)) {
                $unit['knowledgeEvidence'] = $this->parse_evidence_list($latestRelease->KnowledgeEvidence);
            }

            if (!empty($latestRelease->AssessmentConditions)) {
                $unit['assessmentConditions'] = $this->parse_evidence_list($latestRelease->AssessmentConditions);
            }
        }

        return $unit;
    }

    private function parse_evidence_list($text): array {
        if (is_array($text)) {
            return $text;
        }

        $text = strip_tags($text);
        $lines = preg_split('/[\r\n]+/', $text);
        $evidence = [];

        foreach ($lines as $line) {
            $line = trim($line);
            $line = preg_replace('/^[\-\•\*]\s*/', '', $line);
            if (!empty($line) && strlen($line) > 3) {
                $evidence[] = $line;
            }
        }

        return $evidence;
    }

    private function extract_package_code(string $code): string {
        if (preg_match('/^([A-Z]{2,3})/', $code, $m)) {
            return $m[1];
        }
        return 'BSB';
    }

    private function http_get(string $url, int $timeout): ?string {
        \core\session\manager::write_close();
        $curl = new \curl();
        $curl->setopt(['CURLOPT_TIMEOUT' => $timeout, 'CURLOPT_SSL_VERIFYPEER' => true, 'CURLOPT_FOLLOWLOCATION' => true, 'CURLOPT_USERAGENT' => 'MoodleChecklistGrading/1.0']);
        $response = $curl->get($url);
        $httpcode = $curl->info['http_code'];
        
        if ($httpcode !== 200 || empty($response)) {
            return null;
        }
        
        return $response;
    }
}
