<?php

/**
 * BetOddsPredictionController
 * 
 * Handles betting odds predictions with stable algorithm using fixed window size
 * Replaces volatile trend-based metrics with window averages for better stability
 */
class BetOddsPredictionController {
    
    // Fixed window size for stable predictions (instead of user input)
    private const WINDOW_SIZE = 10;
    
    private $matchModel;
    
    public function __construct() {
        $this->matchModel = new MatchModel();
    }
    
    /**
     * Main prediction page
     */
    public function index() {
        // Get recent matches for prediction
        $recentMatches = $this->matchModel->getRecentMatches(100); // Get more data for better analysis
        
        // Calculate predictions for all betting categories
        $predictions = $this->calculatePredictions($recentMatches);
        
        // Prepare data for view
        $matches = $this->prepareMatchesForView($recentMatches);
        $avgResultTrends = $this->calculateAvgResultTrends($recentMatches);
        $avgFirstHalfTrends = $this->calculateAvgFirstHalfTrends($recentMatches);
        
        // Load view
        $this->loadView('bet_odds_prediction', [
            'matches' => $matches,
            'avgResultTrends' => $avgResultTrends,
            'avgFirstHalfTrends' => $avgFirstHalfTrends,
            'predictions' => $predictions,
            'windowSize' => self::WINDOW_SIZE
        ]);
    }
    
    /**
     * Calculate stable predictions using window-based averages
     * 
     * @param array $matches Array of match data
     * @return array Predictions for all betting categories
     */
    private function calculatePredictions($matches) {
        $predictions = [
            'match_result' => $this->calculateMatchResultPredictions($matches),
            'over_under' => $this->calculateOverUnderPredictions($matches),
            'btts' => $this->calculateBttsPredictions($matches), // Both teams to score
            'first_half' => $this->calculateFirstHalfPredictions($matches),
            'second_half' => $this->calculateSecondHalfPredictions($matches),
            'ht_ft' => $this->calculateHtFtPredictions($matches) // Half-time/Full-time
        ];
        
        return $predictions;
    }
    
    /**
     * Calculate match result predictions (Home/Draw/Away) using window averages
     */
    private function calculateMatchResultPredictions($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $homeWins = 0;
        $draws = 0;
        $awayWins = 0;
        
        foreach ($windowData as $match) {
            if ($match['home_score'] > $match['away_score']) {
                $homeWins++;
            } elseif ($match['home_score'] == $match['away_score']) {
                $draws++;
            } else {
                $awayWins++;
            }
        }
        
        $total = count($windowData);
        
        return [
            'home_win_probability' => $total > 0 ? round(($homeWins / $total) * 100, 2) : 0,
            'draw_probability' => $total > 0 ? round(($draws / $total) * 100, 2) : 0,
            'away_win_probability' => $total > 0 ? round(($awayWins / $total) * 100, 2) : 0,
            'confidence_score' => $this->calculateConfidenceScore($windowData, 'result')
        ];
    }
    
    /**
     * Calculate over/under predictions using window averages
     */
    private function calculateOverUnderPredictions($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $totalGoals = 0;
        $over25Count = 0;
        $over15Count = 0;
        
        foreach ($windowData as $match) {
            $matchTotalGoals = $match['home_score'] + $match['away_score'];
            $totalGoals += $matchTotalGoals;
            
            if ($matchTotalGoals > 2.5) $over25Count++;
            if ($matchTotalGoals > 1.5) $over15Count++;
        }
        
        $total = count($windowData);
        $avgGoals = $total > 0 ? $totalGoals / $total : 0;
        
        return [
            'avg_goals_per_match' => round($avgGoals, 2),
            'over_25_probability' => $total > 0 ? round(($over25Count / $total) * 100, 2) : 0,
            'over_15_probability' => $total > 0 ? round(($over15Count / $total) * 100, 2) : 0,
            'confidence_score' => $this->calculateConfidenceScore($windowData, 'goals')
        ];
    }
    
    /**
     * Calculate both teams to score (BTTS) predictions
     */
    private function calculateBttsPredictions($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $bttsCount = 0;
        
        foreach ($windowData as $match) {
            if ($match['home_score'] > 0 && $match['away_score'] > 0) {
                $bttsCount++;
            }
        }
        
        $total = count($windowData);
        
        return [
            'btts_yes_probability' => $total > 0 ? round(($bttsCount / $total) * 100, 2) : 0,
            'btts_no_probability' => $total > 0 ? round((($total - $bttsCount) / $total) * 100, 2) : 0,
            'confidence_score' => $this->calculateConfidenceScore($windowData, 'btts')
        ];
    }
    
    /**
     * Calculate first half predictions
     */
    private function calculateFirstHalfPredictions($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $firstHalfGoals = 0;
        $homeLeadingCount = 0;
        $drawCount = 0;
        
        foreach ($windowData as $match) {
            $fhGoals = ($match['first_half_home'] ?? 0) + ($match['first_half_away'] ?? 0);
            $firstHalfGoals += $fhGoals;
            
            if (($match['first_half_home'] ?? 0) > ($match['first_half_away'] ?? 0)) {
                $homeLeadingCount++;
            } elseif (($match['first_half_home'] ?? 0) == ($match['first_half_away'] ?? 0)) {
                $drawCount++;
            }
        }
        
        $total = count($windowData);
        
        return [
            'avg_first_half_goals' => $total > 0 ? round($firstHalfGoals / $total, 2) : 0,
            'home_leading_probability' => $total > 0 ? round(($homeLeadingCount / $total) * 100, 2) : 0,
            'first_half_draw_probability' => $total > 0 ? round(($drawCount / $total) * 100, 2) : 0,
            'confidence_score' => $this->calculateConfidenceScore($windowData, 'first_half')
        ];
    }
    
    /**
     * Calculate second half predictions
     */
    private function calculateSecondHalfPredictions($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $secondHalfGoals = 0;
        $moreGoalsSecondHalf = 0;
        
        foreach ($windowData as $match) {
            $fhGoals = ($match['first_half_home'] ?? 0) + ($match['first_half_away'] ?? 0);
            $totalGoals = $match['home_score'] + $match['away_score'];
            $shGoals = $totalGoals - $fhGoals;
            
            $secondHalfGoals += $shGoals;
            
            if ($shGoals > $fhGoals) {
                $moreGoalsSecondHalf++;
            }
        }
        
        $total = count($windowData);
        
        return [
            'avg_second_half_goals' => $total > 0 ? round($secondHalfGoals / $total, 2) : 0,
            'more_goals_second_half_probability' => $total > 0 ? round(($moreGoalsSecondHalf / $total) * 100, 2) : 0,
            'confidence_score' => $this->calculateConfidenceScore($windowData, 'second_half')
        ];
    }
    
    /**
     * Calculate half-time/full-time predictions
     */
    private function calculateHtFtPredictions($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $htFtPatterns = [];
        
        foreach ($windowData as $match) {
            $htResult = $this->getMatchResult($match['first_half_home'] ?? 0, $match['first_half_away'] ?? 0);
            $ftResult = $this->getMatchResult($match['home_score'], $match['away_score']);
            
            $pattern = $htResult . '/' . $ftResult;
            $htFtPatterns[$pattern] = ($htFtPatterns[$pattern] ?? 0) + 1;
        }
        
        $total = count($windowData);
        $predictions = [];
        
        foreach ($htFtPatterns as $pattern => $count) {
            $predictions[$pattern] = $total > 0 ? round(($count / $total) * 100, 2) : 0;
        }
        
        return [
            'patterns' => $predictions,
            'confidence_score' => $this->calculateConfidenceScore($windowData, 'ht_ft')
        ];
    }
    
    /**
     * Get result code (H=Home, D=Draw, A=Away)
     */
    private function getMatchResult($homeScore, $awayScore) {
        if ($homeScore > $awayScore) return 'H';
        if ($homeScore < $awayScore) return 'A';
        return 'D';
    }
    
    /**
     * Calculate confidence score based on data consistency
     */
    private function calculateConfidenceScore($windowData, $category) {
        if (count($windowData) < self::WINDOW_SIZE) {
            return round((count($windowData) / self::WINDOW_SIZE) * 100, 2);
        }
        
        // Calculate standard deviation for consistency measure
        $values = [];
        foreach ($windowData as $match) {
            switch ($category) {
                case 'result':
                    $values[] = $match['home_score'] - $match['away_score'];
                    break;
                case 'goals':
                    $values[] = $match['home_score'] + $match['away_score'];
                    break;
                case 'btts':
                    $values[] = ($match['home_score'] > 0 && $match['away_score'] > 0) ? 1 : 0;
                    break;
                default:
                    $values[] = $match['home_score'] + $match['away_score'];
            }
        }
        
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) { return pow($x - $mean, 2); }, $values)) / count($values);
        $stdDev = sqrt($variance);
        
        // Convert to confidence score (lower standard deviation = higher confidence)
        $maxStdDev = 3.0; // Assumed maximum for normalization
        $confidence = max(0, min(100, 100 - ($stdDev / $maxStdDev) * 100));
        
        return round($confidence, 2);
    }
    
    /**
     * Get last N matches for window analysis
     */
    private function getLastNMatches($matches, $n) {
        return array_slice($matches, 0, $n);
    }
    
    /**
     * Calculate average result trends for view (preserved from original requirements)
     */
    private function calculateAvgResultTrends($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $homeWinRate = 0;
        $drawRate = 0;
        $awayWinRate = 0;
        
        foreach ($windowData as $match) {
            if ($match['home_score'] > $match['away_score']) {
                $homeWinRate++;
            } elseif ($match['home_score'] == $match['away_score']) {
                $drawRate++;
            } else {
                $awayWinRate++;
            }
        }
        
        $total = count($windowData);
        
        return [
            'home_win_avg' => $total > 0 ? round(($homeWinRate / $total) * 100, 1) : 0,
            'draw_avg' => $total > 0 ? round(($drawRate / $total) * 100, 1) : 0,
            'away_win_avg' => $total > 0 ? round(($awayWinRate / $total) * 100, 1) : 0
        ];
    }
    
    /**
     * Calculate average first half trends for view (preserved from original requirements)
     */
    private function calculateAvgFirstHalfTrends($matches) {
        $windowData = $this->getLastNMatches($matches, self::WINDOW_SIZE);
        
        $avgGoals = 0;
        $homeLeading = 0;
        $firstHalfDraw = 0;
        
        foreach ($windowData as $match) {
            $fhHome = $match['first_half_home'] ?? 0;
            $fhAway = $match['first_half_away'] ?? 0;
            
            $avgGoals += $fhHome + $fhAway;
            
            if ($fhHome > $fhAway) {
                $homeLeading++;
            } elseif ($fhHome == $fhAway) {
                $firstHalfDraw++;
            }
        }
        
        $total = count($windowData);
        
        return [
            'avg_goals' => $total > 0 ? round($avgGoals / $total, 1) : 0,
            'home_leading_rate' => $total > 0 ? round(($homeLeading / $total) * 100, 1) : 0,
            'draw_rate' => $total > 0 ? round(($firstHalfDraw / $total) * 100, 1) : 0
        ];
    }
    
    /**
     * Prepare matches data for view display
     */
    private function prepareMatchesForView($matches) {
        return array_slice($matches, 0, 20); // Show last 20 matches in view
    }
    
    /**
     * Load view with data
     */
    private function loadView($viewName, $data = []) {
        extract($data);
        include "views/{$viewName}.php";
    }
}
?>