<?php

/**
 * BetOddsPredictionController
 * 
 * Implements an improved prediction algorithm based on ratio changes.
 * This controller calculates predictions using trend analysis, rate of change,
 * and percentage changes to provide more realistic betting odds predictions.
 * 
 * Algorithm:
 * - Analyzes ratio changes for different fields (ft1_diff, ft2_diff, etc.)
 * - Calculates weighted scores: trend * 0.5 + rate_of_change * 0.3 + percentage_change * 0.2
 * - Selects the option with the highest score as prediction
 * - Calculates confidence as the ratio of max score to sum of all scores
 */
class BetOddsPredictionController
{
    /**
     * Weighted scoring coefficients
     */
    const TREND_WEIGHT = 0.5;
    const RATE_WEIGHT = 0.3;
    const PERCENTAGE_WEIGHT = 0.2;

    /**
     * Available prediction fields for analysis
     */
    private $predictionFields = [
        'ft1_diff',
        'ft2_diff',
        'ft3_diff',
        'ht1_diff',
        'ht2_diff'
    ];

    /**
     * Calculate prediction based on ratio changes
     * 
     * @param array $data Array of historical data points for each field
     * @return array Prediction result with option and confidence
     */
    public function predictFromRatioChanges($data)
    {
        $fieldScores = [];
        
        // Process each prediction field
        foreach ($this->predictionFields as $field) {
            if (!isset($data[$field]) || count($data[$field]) < 2) {
                continue; // Skip fields with insufficient data
            }
            
            $fieldValues = $data[$field];
            $score = $this->calculateFieldScore($fieldValues);
            
            if ($score !== null) {
                $fieldScores[$field] = $score;
            }
        }
        
        if (empty($fieldScores)) {
            return [
                'prediction' => null,
                'confidence' => 0,
                'error' => 'Insufficient data for prediction'
            ];
        }
        
        // Find the field with highest score
        $maxScore = max($fieldScores);
        $totalScore = array_sum($fieldScores);
        $predictedField = array_search($maxScore, $fieldScores);
        
        // Calculate confidence as ratio of max score to total scores
        $confidence = $totalScore > 0 ? ($maxScore / $totalScore) : 0;
        
        return [
            'prediction' => $predictedField,
            'confidence' => round($confidence, 4),
            'scores' => $fieldScores,
            'max_score' => $maxScore,
            'total_score' => $totalScore
        ];
    }

    /**
     * Calculate weighted score for a single field based on ratio changes
     * 
     * @param array $values Array of numerical values for the field
     * @return float|null Calculated score or null if calculation fails
     */
    private function calculateFieldScore($values)
    {
        if (count($values) < 2) {
            return null;
        }

        // Calculate trend (last value - first value)
        $trend = $this->calculateTrend($values);
        
        // Calculate rate of change (difference between last two values)
        $rateOfChange = $this->calculateRateOfChange($values);
        
        // Calculate percentage change
        $percentageChange = $this->calculatePercentageChange($values);
        
        // Handle edge cases where calculations might fail
        if ($trend === null || $rateOfChange === null || $percentageChange === null) {
            return null;
        }
        
        // Apply weighted scoring formula
        $score = ($trend * self::TREND_WEIGHT) + 
                 ($rateOfChange * self::RATE_WEIGHT) + 
                 ($percentageChange * self::PERCENTAGE_WEIGHT);
        
        return abs($score); // Use absolute value to ensure positive scoring
    }

    /**
     * Calculate trend: difference between last and first values
     * 
     * @param array $values Array of numerical values
     * @return float|null Trend value or null if calculation fails
     */
    private function calculateTrend($values)
    {
        if (count($values) < 2) {
            return null;
        }
        
        $firstValue = reset($values);
        $lastValue = end($values);
        
        return $lastValue - $firstValue;
    }

    /**
     * Calculate rate of change: difference between last two values
     * 
     * @param array $values Array of numerical values
     * @return float|null Rate of change or null if calculation fails
     */
    private function calculateRateOfChange($values)
    {
        if (count($values) < 2) {
            return null;
        }
        
        $lastValue = end($values);
        $secondLastValue = prev($values);
        
        return $lastValue - $secondLastValue;
    }

    /**
     * Calculate percentage change between first and last values
     * 
     * @param array $values Array of numerical values
     * @return float|null Percentage change or null if calculation fails
     */
    private function calculatePercentageChange($values)
    {
        if (count($values) < 2) {
            return null;
        }
        
        $firstValue = reset($values);
        $lastValue = end($values);
        
        // Avoid division by zero
        if ($firstValue == 0) {
            return $lastValue; // Return absolute change if first value is zero
        }
        
        return (($lastValue - $firstValue) / abs($firstValue)) * 100;
    }

    /**
     * Normalize values to a specific range (optional enhancement)
     * 
     * @param array $values Array of values to normalize
     * @param float $min Minimum value for normalization range
     * @param float $max Maximum value for normalization range
     * @return array Normalized values
     */
    private function normalizeValues($values, $min = 0, $max = 1)
    {
        if (empty($values)) {
            return [];
        }
        
        $minVal = min($values);
        $maxVal = max($values);
        
        // Handle case where all values are the same
        if ($maxVal == $minVal) {
            return array_fill(0, count($values), ($min + $max) / 2);
        }
        
        $normalized = [];
        foreach ($values as $value) {
            $normalized[] = $min + (($value - $minVal) / ($maxVal - $minVal)) * ($max - $min);
        }
        
        return $normalized;
    }

    /**
     * Get detailed analysis for debugging and monitoring
     * 
     * @param array $data Array of historical data points for each field
     * @return array Detailed analysis including individual calculations
     */
    public function getDetailedAnalysis($data)
    {
        $analysis = [];
        
        foreach ($this->predictionFields as $field) {
            if (!isset($data[$field]) || count($data[$field]) < 2) {
                continue;
            }
            
            $fieldValues = $data[$field];
            
            $analysis[$field] = [
                'values' => $fieldValues,
                'trend' => $this->calculateTrend($fieldValues),
                'rate_of_change' => $this->calculateRateOfChange($fieldValues),
                'percentage_change' => $this->calculatePercentageChange($fieldValues),
                'final_score' => $this->calculateFieldScore($fieldValues)
            ];
        }
        
        return $analysis;
    }

    /**
     * Validate input data format
     * 
     * @param array $data Input data to validate
     * @return array Validation result
     */
    public function validateInputData($data)
    {
        $errors = [];
        
        if (!is_array($data)) {
            $errors[] = 'Input data must be an array';
            return ['valid' => false, 'errors' => $errors];
        }
        
        $validFields = 0;
        foreach ($this->predictionFields as $field) {
            if (isset($data[$field])) {
                if (!is_array($data[$field])) {
                    $errors[] = "Field {$field} must be an array";
                } elseif (count($data[$field]) < 2) {
                    $errors[] = "Field {$field} must contain at least 2 values";
                } else {
                    // Check if all values are numeric
                    foreach ($data[$field] as $value) {
                        if (!is_numeric($value)) {
                            $errors[] = "Field {$field} contains non-numeric values";
                            break;
                        }
                    }
                    $validFields++;
                }
            }
        }
        
        if ($validFields === 0) {
            $errors[] = 'No valid prediction fields found';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'valid_fields_count' => $validFields
        ];
    }
}

?>