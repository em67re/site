<?php

/**
 * MatchModel
 * 
 * Handles match data operations for betting predictions
 */
class MatchModel {
    
    /**
     * Get recent matches for analysis
     * In a real application, this would connect to a database
     * For demo purposes, we'll generate sample data
     */
    public function getRecentMatches($limit = 50) {
        // Sample data for demonstration
        // In production, this would fetch from database
        $matches = [];
        
        $teams = [
            'Arsenal', 'Chelsea', 'Liverpool', 'Man City', 'Man United', 
            'Tottenham', 'Newcastle', 'Brighton', 'Aston Villa', 'West Ham',
            'Fulham', 'Brentford', 'Crystal Palace', 'Wolves', 'Everton',
            'Nottingham Forest', 'Bournemouth', 'Sheffield United', 'Burnley', 'Luton'
        ];
        
        for ($i = 0; $i < $limit; $i++) {
            $homeTeam = $teams[array_rand($teams)];
            $awayTeam = $teams[array_rand($teams)];
            
            // Ensure different teams
            while ($awayTeam === $homeTeam) {
                $awayTeam = $teams[array_rand($teams)];
            }
            
            // Generate realistic match data
            $homeScore = $this->generateRealisticScore();
            $awayScore = $this->generateRealisticScore();
            
            // Generate first half scores (typically lower)
            $firstHalfHome = rand(0, min($homeScore, 2));
            $firstHalfAway = rand(0, min($awayScore, 2));
            
            $matches[] = [
                'id' => $i + 1,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'first_half_home' => $firstHalfHome,
                'first_half_away' => $firstHalfAway,
                'match_date' => date('Y-m-d', strtotime("-{$i} days")),
                'league' => 'Premier League',
                'odds_home' => round(1.5 + rand(0, 400) / 100, 2),
                'odds_draw' => round(2.8 + rand(0, 300) / 100, 2),
                'odds_away' => round(1.5 + rand(0, 400) / 100, 2)
            ];
        }
        
        return $matches;
    }
    
    /**
     * Generate realistic football scores
     * Based on actual football score distributions
     */
    private function generateRealisticScore() {
        $scoreDistribution = [
            0 => 25,  // 25% chance of 0 goals
            1 => 35,  // 35% chance of 1 goal
            2 => 25,  // 25% chance of 2 goals
            3 => 10,  // 10% chance of 3 goals
            4 => 4,   // 4% chance of 4 goals
            5 => 1    // 1% chance of 5 goals
        ];
        
        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($scoreDistribution as $score => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $score;
            }
        }
        
        return 0; // fallback
    }
    
    /**
     * Get matches by team
     */
    public function getMatchesByTeam($teamName, $limit = 20) {
        $allMatches = $this->getRecentMatches(100);
        $teamMatches = array_filter($allMatches, function($match) use ($teamName) {
            return $match['home_team'] === $teamName || $match['away_team'] === $teamName;
        });
        
        return array_slice(array_values($teamMatches), 0, $limit);
    }
    
    /**
     * Get head-to-head matches between two teams
     */
    public function getHeadToHeadMatches($team1, $team2, $limit = 10) {
        $allMatches = $this->getRecentMatches(200);
        $h2hMatches = array_filter($allMatches, function($match) use ($team1, $team2) {
            return ($match['home_team'] === $team1 && $match['away_team'] === $team2) ||
                   ($match['home_team'] === $team2 && $match['away_team'] === $team1);
        });
        
        return array_slice(array_values($h2hMatches), 0, $limit);
    }
}
?>