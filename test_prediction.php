<?php

/**
 * Test file for BetOddsPredictionController
 * This file validates the new prediction algorithm implementation
 */

require_once 'BetOddsPredictionController.php';

class BetOddsPredictionTest
{
    private $controller;

    public function __construct()
    {
        $this->controller = new BetOddsPredictionController();
    }

    /**
     * Run all tests
     */
    public function runTests()
    {
        echo "=== Testing BetOddsPredictionController ===\n\n";
        
        $this->testBasicPrediction();
        $this->testValidation();
        $this->testDetailedAnalysis();
        $this->testEdgeCases();
        
        echo "\n=== All tests completed ===\n";
    }

    /**
     * Test basic prediction functionality
     */
    private function testBasicPrediction()
    {
        echo "Test 1: Basic Prediction\n";
        echo "------------------------\n";
        
        // Sample data with increasing trend for ft1_diff
        $testData = [
            'ft1_diff' => [1.0, 1.2, 1.5, 1.8, 2.0],
            'ft2_diff' => [2.0, 1.9, 1.8, 1.7, 1.6],
            'ft3_diff' => [1.5, 1.5, 1.5, 1.5, 1.5]
        ];
        
        $result = $this->controller->predictFromRatioChanges($testData);
        
        echo "Input data:\n";
        foreach ($testData as $field => $values) {
            echo "  {$field}: " . implode(', ', $values) . "\n";
        }
        
        echo "\nPrediction result:\n";
        echo "  Predicted field: " . ($result['prediction'] ?? 'null') . "\n";
        echo "  Confidence: " . ($result['confidence'] ?? 0) . "\n";
        echo "  Scores: " . json_encode($result['scores'] ?? []) . "\n";
        echo "\n";
    }

    /**
     * Test input validation
     */
    private function testValidation()
    {
        echo "Test 2: Input Validation\n";
        echo "------------------------\n";
        
        // Test invalid data
        $invalidData = [
            'ft1_diff' => [1.0], // Too few values
            'ft2_diff' => ['a', 'b', 'c'], // Non-numeric values
            'invalid_field' => [1, 2, 3]
        ];
        
        $validation = $this->controller->validateInputData($invalidData);
        
        echo "Validation result for invalid data:\n";
        echo "  Valid: " . ($validation['valid'] ? 'true' : 'false') . "\n";
        echo "  Errors: " . implode(', ', $validation['errors']) . "\n";
        
        // Test valid data
        $validData = [
            'ft1_diff' => [1.0, 2.0, 3.0],
            'ft2_diff' => [5.0, 4.0, 3.0]
        ];
        
        $validation = $this->controller->validateInputData($validData);
        echo "\nValidation result for valid data:\n";
        echo "  Valid: " . ($validation['valid'] ? 'true' : 'false') . "\n";
        echo "  Valid fields count: " . $validation['valid_fields_count'] . "\n";
        echo "\n";
    }

    /**
     * Test detailed analysis
     */
    private function testDetailedAnalysis()
    {
        echo "Test 3: Detailed Analysis\n";
        echo "-------------------------\n";
        
        $testData = [
            'ft1_diff' => [10.0, 12.0, 15.0, 18.0, 20.0],
            'ft2_diff' => [100.0, 95.0, 90.0, 85.0, 80.0]
        ];
        
        $analysis = $this->controller->getDetailedAnalysis($testData);
        
        echo "Detailed analysis:\n";
        foreach ($analysis as $field => $details) {
            echo "  {$field}:\n";
            echo "    Values: " . implode(', ', $details['values']) . "\n";
            echo "    Trend: " . $details['trend'] . "\n";
            echo "    Rate of change: " . $details['rate_of_change'] . "\n";
            echo "    Percentage change: " . round($details['percentage_change'], 2) . "%\n";
            echo "    Final score: " . round($details['final_score'], 4) . "\n";
        }
        echo "\n";
    }

    /**
     * Test edge cases
     */
    private function testEdgeCases()
    {
        echo "Test 4: Edge Cases\n";
        echo "------------------\n";
        
        // Test with zeros
        $zeroData = [
            'ft1_diff' => [0.0, 1.0, 2.0],
            'ft2_diff' => [1.0, 0.0, -1.0]
        ];
        
        $result = $this->controller->predictFromRatioChanges($zeroData);
        echo "Test with zeros:\n";
        echo "  Prediction: " . ($result['prediction'] ?? 'null') . "\n";
        echo "  Confidence: " . ($result['confidence'] ?? 0) . "\n";
        
        // Test with empty data
        $emptyData = [];
        $result = $this->controller->predictFromRatioChanges($emptyData);
        echo "\nTest with empty data:\n";
        echo "  Error: " . ($result['error'] ?? 'none') . "\n";
        
        // Test with single value
        $singleValueData = [
            'ft1_diff' => [5.0]
        ];
        $result = $this->controller->predictFromRatioChanges($singleValueData);
        echo "\nTest with single value:\n";
        echo "  Error: " . ($result['error'] ?? 'none') . "\n";
        
        echo "\n";
    }
}

// Run tests if this file is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $test = new BetOddsPredictionTest();
    $test->runTests();
}

?>