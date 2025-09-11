<?php

/**
 * Basic tests for BetOddsPredictionController
 * Tests the core functionality and algorithm requirements
 */

require_once __DIR__ . '/../controllers/BetOddsPredictionController.php';
require_once __DIR__ . '/../models/MatchModel.php';

class BetOddsPredictionTest {
    
    private $controller;
    private $testsPassed = 0;
    private $testsTotal = 0;
    
    public function __construct() {
        $this->controller = new BetOddsPredictionController();
    }
    
    public function runAllTests() {
        echo "🧪 Running BetOddsPrediction Tests\n";
        echo "==================================\n\n";
        
        $this->testWindowSizeIsFixed();
        $this->testPredictionCategories();
        $this->testStabilityMetrics();
        $this->testConfidenceScores();
        $this->testViewDataStructure();
        
        echo "\n==================================\n";
        echo "Tests Summary: {$this->testsPassed}/{$this->testsTotal} passed\n";
        
        if ($this->testsPassed === $this->testsTotal) {
            echo "✅ All tests passed! Requirements met.\n";
            return true;
        } else {
            echo "❌ Some tests failed.\n";
            return false;
        }
    }
    
    private function testWindowSizeIsFixed() {
        echo "🔹 Testing fixed window size...\n";
        
        // Use reflection to access private constant
        $reflection = new ReflectionClass('BetOddsPredictionController');
        $windowSize = $reflection->getConstant('WINDOW_SIZE');
        
        $this->assert($windowSize === 10, "Window size should be fixed at 10");
        $this->assert(is_int($windowSize), "Window size should be an integer");
        
        echo "   ✓ Window size is fixed at {$windowSize}\n\n";
    }
    
    private function testPredictionCategories() {
        echo "🔹 Testing all prediction categories...\n";
        
        // Mock data for testing
        $mockMatches = $this->generateMockMatches(15);
        
        // Test that all categories are calculated
        $predictions = $this->invokePrivateMethod($this->controller, 'calculatePredictions', [$mockMatches]);
        
        $expectedCategories = [
            'match_result', 'over_under', 'btts', 
            'first_half', 'second_half', 'ht_ft'
        ];
        
        foreach ($expectedCategories as $category) {
            $this->assert(isset($predictions[$category]), "Category '{$category}' should exist");
        }
        
        // Test match result predictions structure
        $matchResult = $predictions['match_result'];
        $this->assert(isset($matchResult['home_win_probability']), "Home win probability should exist");
        $this->assert(isset($matchResult['draw_probability']), "Draw probability should exist");
        $this->assert(isset($matchResult['away_win_probability']), "Away win probability should exist");
        $this->assert(isset($matchResult['confidence_score']), "Confidence score should exist");
        
        // Test that probabilities sum to ~100%
        $total = $matchResult['home_win_probability'] + $matchResult['draw_probability'] + $matchResult['away_win_probability'];
        $this->assert(abs($total - 100) < 1, "Probabilities should sum to ~100%, got {$total}%");
        
        echo "   ✓ All 6 prediction categories are implemented\n";
        echo "   ✓ Match result probabilities sum to 100%\n\n";
    }
    
    private function testStabilityMetrics() {
        echo "🔹 Testing stability metrics (window-based averages)...\n";
        
        // Generate consistent test data
        $stableMatches = [];
        for ($i = 0; $i < 10; $i++) {
            $stableMatches[] = [
                'home_score' => 2,
                'away_score' => 1,
                'first_half_home' => 1,
                'first_half_away' => 0
            ];
        }
        
        $predictions = $this->invokePrivateMethod($this->controller, 'calculatePredictions', [$stableMatches]);
        
        // With consistent data, home win should be 100%
        $this->assert($predictions['match_result']['home_win_probability'] == 100, 
                     "With consistent home wins, probability should be 100%");
        
        // Test BTTS with consistent data (both teams score 2-1, so BTTS Yes should be 100%)
        $this->assert($predictions['btts']['btts_yes_probability'] == 100, 
                     "With consistent BTTS-Yes data, probability should be 100%");
        
        echo "   ✓ Stable data produces stable predictions\n";
        echo "   ✓ Window-based averages working correctly\n\n";
    }
    
    private function testConfidenceScores() {
        echo "🔹 Testing confidence score calculation...\n";
        
        $mockMatches = $this->generateMockMatches(10);
        $predictions = $this->invokePrivateMethod($this->controller, 'calculatePredictions', [$mockMatches]);
        
        foreach ($predictions as $category => $data) {
            $this->assert(isset($data['confidence_score']), "Confidence score should exist for {$category}");
            $this->assert($data['confidence_score'] >= 0 && $data['confidence_score'] <= 100, 
                         "Confidence score should be between 0-100 for {$category}");
        }
        
        echo "   ✓ All categories have confidence scores\n";
        echo "   ✓ Confidence scores are within valid range (0-100)\n\n";
    }
    
    private function testViewDataStructure() {
        echo "🔹 Testing preserved view data structure...\n";
        
        $mockMatches = $this->generateMockMatches(20);
        
        // Test avgResultTrends
        $avgResultTrends = $this->invokePrivateMethod($this->controller, 'calculateAvgResultTrends', [$mockMatches]);
        $this->assert(isset($avgResultTrends['home_win_avg']), "avgResultTrends should have home_win_avg");
        $this->assert(isset($avgResultTrends['draw_avg']), "avgResultTrends should have draw_avg");
        $this->assert(isset($avgResultTrends['away_win_avg']), "avgResultTrends should have away_win_avg");
        
        // Test avgFirstHalfTrends
        $avgFirstHalfTrends = $this->invokePrivateMethod($this->controller, 'calculateAvgFirstHalfTrends', [$mockMatches]);
        $this->assert(isset($avgFirstHalfTrends['avg_goals']), "avgFirstHalfTrends should have avg_goals");
        $this->assert(isset($avgFirstHalfTrends['home_leading_rate']), "avgFirstHalfTrends should have home_leading_rate");
        $this->assert(isset($avgFirstHalfTrends['draw_rate']), "avgFirstHalfTrends should have draw_rate");
        
        // Test matches preparation
        $matches = $this->invokePrivateMethod($this->controller, 'prepareMatchesForView', [$mockMatches]);
        $this->assert(count($matches) <= 20, "Prepared matches should be limited to 20");
        
        echo "   ✓ avgResultTrends structure preserved\n";
        echo "   ✓ avgFirstHalfTrends structure preserved\n";
        echo "   ✓ matches data structure correct\n\n";
    }
    
    private function generateMockMatches($count) {
        $matches = [];
        for ($i = 0; $i < $count; $i++) {
            $matches[] = [
                'home_score' => rand(0, 4),
                'away_score' => rand(0, 4),
                'first_half_home' => rand(0, 2),
                'first_half_away' => rand(0, 2),
                'home_team' => "Team A{$i}",
                'away_team' => "Team B{$i}",
                'match_date' => date('Y-m-d', strtotime("-{$i} days"))
            ];
        }
        return $matches;
    }
    
    private function invokePrivateMethod($object, $methodName, $args = []) {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }
    
    private function assert($condition, $message) {
        $this->testsTotal++;
        if ($condition) {
            $this->testsPassed++;
        } else {
            echo "   ❌ FAILED: {$message}\n";
        }
    }
}

// Run tests if this file is executed directly
if (php_sapi_name() === 'cli') {
    $test = new BetOddsPredictionTest();
    $success = $test->runAllTests();
    exit($success ? 0 : 1);
}
?>