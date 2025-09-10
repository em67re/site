<?php

/**
 * Example usage of BetOddsPredictionController
 * 
 * This file demonstrates how to use the improved prediction algorithm
 * based on ratio changes for betting odds prediction.
 */

require_once 'BetOddsPredictionController.php';

// Initialize the controller
$predictor = new BetOddsPredictionController();

echo "=== BetOddsPredictionController Usage Example ===\n\n";

// Example 1: Basic prediction with realistic betting data
echo "Example 1: Football Match Odds Prediction\n";
echo "==========================================\n";

$matchData = [
    'ft1_diff' => [1.50, 1.55, 1.60, 1.58, 1.62, 1.65], // Home team odds changes
    'ft2_diff' => [2.10, 2.05, 2.00, 2.02, 1.98, 1.95], // Away team odds changes
    'ft3_diff' => [3.20, 3.25, 3.30, 3.28, 3.35, 3.40], // Draw odds changes
    'ht1_diff' => [2.80, 2.85, 2.90, 2.88, 2.92, 2.95], // Half-time home odds
    'ht2_diff' => [1.90, 1.88, 1.85, 1.87, 1.83, 1.80]  // Half-time away odds
];

echo "Input odds data:\n";
foreach ($matchData as $field => $values) {
    echo sprintf("  %-8s: %s\n", $field, implode(', ', array_map(function($v) { return number_format($v, 2); }, $values)));
}

// Get prediction
$prediction = $predictor->predictFromRatioChanges($matchData);

echo "\nPrediction Results:\n";
echo "  Predicted outcome: " . ($prediction['prediction'] ?? 'N/A') . "\n";
echo "  Confidence level: " . number_format(($prediction['confidence'] ?? 0) * 100, 1) . "%\n";
echo "  Max score: " . number_format($prediction['max_score'] ?? 0, 4) . "\n";
echo "  Total score: " . number_format($prediction['total_score'] ?? 0, 4) . "\n";

if (isset($prediction['scores'])) {
    echo "\nDetailed Scores:\n";
    foreach ($prediction['scores'] as $field => $score) {
        echo "  " . sprintf("%-8s: %s", $field, number_format($score, 4)) . "\n";
    }
}

// Example 2: Detailed analysis
echo "\n\nExample 2: Detailed Analysis\n";
echo "============================\n";

$analysis = $predictor->getDetailedAnalysis($matchData);

foreach ($analysis as $field => $details) {
    echo "\n{$field} Analysis:\n";
    echo "  Trend (last - first): " . number_format($details['trend'], 4) . "\n";
    echo "  Rate of change: " . number_format($details['rate_of_change'], 4) . "\n";
    echo "  Percentage change: " . number_format($details['percentage_change'], 2) . "%\n";
    echo "  Final weighted score: " . number_format($details['final_score'], 4) . "\n";
    echo "  Formula: ({$details['trend']} * 0.5) + ({$details['rate_of_change']} * 0.3) + ({$details['percentage_change']} * 0.2)\n";
}

// Example 3: Input validation
echo "\n\nExample 3: Input Validation\n";
echo "===========================\n";

$validation = $predictor->validateInputData($matchData);
echo "Data validation: " . ($validation['valid'] ? 'PASSED' : 'FAILED') . "\n";
echo "Valid fields: " . $validation['valid_fields_count'] . "\n";

if (!empty($validation['errors'])) {
    echo "Errors:\n";
    foreach ($validation['errors'] as $error) {
        echo "  - {$error}\n";
    }
}

echo "\n=== Algorithm Summary ===\n";
echo "The new prediction algorithm:\n";
echo "1. Analyzes ratio changes for each betting field\n";
echo "2. Calculates three metrics:\n";
echo "   - Trend: difference between last and first values\n";
echo "   - Rate of change: difference between last two values\n";
echo "   - Percentage change: relative change from first to last value\n";
echo "3. Applies weighted scoring: trend×0.5 + rate×0.3 + percentage×0.2\n";
echo "4. Selects the field with highest score as prediction\n";
echo "5. Calculates confidence as ratio of max score to total scores\n";
echo "\nThis approach provides more realistic predictions based on\n";
echo "market movement patterns rather than simple weighted averages.\n";

?>