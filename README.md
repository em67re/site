# Bahis Oranları Tahmin Sistemi (Betting Odds Prediction System)

## Overview
Bu proje, futbol maçları için stabil ve güvenilir bahis tahminleri üreten bir sistemdir. Trend tabanlı ve volatil metriklerin yerine pencere ortalaması tabanlı algoritma kullanır.

This project provides stable and reliable betting predictions for football matches using window-based averaging algorithms instead of volatile trend-based metrics.

## Key Features ✨

### Algorithm Improvements
- **Fixed Window Size**: Sabit 10 maç pencere boyutu (kullanıcı girişi kaldırıldı)
- **Stable Predictions**: Trend, latestChange, percentChange gibi volatil metrikler yerine pencere ortalamaları
- **Confidence Scores**: Her tahmin için veri tutarlılığına dayalı güven skorları
- **Multiple Categories**: 6 farklı bahis kategorisi için tahmin

### Prediction Categories
1. **Maç Sonucu** (Match Result): Ev sahibi/Beraberlik/Deplasman
2. **Alt/Üst** (Over/Under): Toplam gol tahminleri
3. **Karşılıklı Gol** (BTTS): Her iki takımın gol atma durumu
4. **İlk Yarı** (First Half): İlk yarı tahminleri
5. **İkinci Yarı** (Second Half): İkinci yarı analizleri
6. **İlk Yarı/Maç Sonu** (HT/FT): Kombinasyon tahminleri

## Technical Implementation 🔧

### Architecture
```
├── index.php                 # Ana giriş noktası
├── controllers/
│   └── BetOddsPredictionController.php  # Ana tahmin kontrolcüsü
├── models/
│   └── MatchModel.php        # Maç verisi yönetimi
├── views/
│   └── bet_odds_prediction.php  # Kullanıcı arayüzü
└── tests/
    └── BetOddsPredictionTest.php  # Algoritma testleri
```

### Key Algorithm Changes

#### Before (Volatile)
- User-defined window size
- Trend-based calculations
- Latest change sensitivity
- Percentage change volatility

#### After (Stable)
- Fixed window size (10 matches)
- Window-based averages
- Confidence scoring
- Stable prediction methodology

### Core Methods

#### Stable Prediction Calculation
```php
private function calculatePredictions($matches) {
    // Uses fixed WINDOW_SIZE = 10
    // Calculates averages over window data
    // Returns stable predictions for all categories
}
```

#### Confidence Score Algorithm
```php
private function calculateConfidenceScore($windowData, $category) {
    // Calculates standard deviation for consistency
    // Higher consistency = Higher confidence
    // Normalizes to 0-100% scale
}
```

## Installation & Usage 🚀

### Requirements
- PHP 8.0+
- Web server (Apache/Nginx) or PHP built-in server

### Quick Start
1. Clone the repository
2. Start PHP development server:
   ```bash
   php -S localhost:8000
   ```
3. Open browser: `http://localhost:8000`

### Running Tests
```bash
php tests/BetOddsPredictionTest.php
```

## API Endpoints 🌐

### Main Prediction Page
- **URL**: `/` or `/?controller=BetOddsPrediction&action=index`
- **Method**: GET
- **Description**: Ana tahmin sayfası (Main prediction page)

### Data Structure

#### Preserved View Variables (as required)
- `matches`: Görüntülenecek maç listesi
- `avgResultTrends`: Ortalama sonuç trendleri
- `avgFirstHalfTrends`: Ortalama ilk yarı trendleri

#### New Prediction Structure
```php
[
    'match_result' => [
        'home_win_probability' => float,
        'draw_probability' => float,
        'away_win_probability' => float,
        'confidence_score' => float
    ],
    'over_under' => [...],
    'btts' => [...],
    'first_half' => [...],
    'second_half' => [...],
    'ht_ft' => [...]
]
```

## Configuration ⚙️

### Window Size
Sabit pencere boyutu `BetOddsPredictionController::WINDOW_SIZE = 10` olarak ayarlanmıştır.

The fixed window size is set to `BetOddsPredictionController::WINDOW_SIZE = 10`.

### Confidence Thresholds
- **High Confidence**: ≥70% (Yeşil/Green)
- **Medium Confidence**: 50-69% (Turuncu/Orange)  
- **Low Confidence**: <50% (Kırmızı/Red)

## Testing Results ✅

All algorithm tests pass:
- ✓ Fixed window size validation
- ✓ All 6 prediction categories implemented
- ✓ Stable prediction methodology
- ✓ Confidence score calculations
- ✓ Preserved view data structure
- ✓ Window-based averaging correctness

## Problem Statement Compliance ✔️

### Requirements Met:
1. **WindowSize parametresi kaldırıldı** ✓ - Kullanıcı girişi yerine sabit değer
2. **Stabil tahmin algoritması** ✓ - Pencere ortalaması tabanlı hesaplama
3. **Volatil metrikler değiştirildi** ✓ - Trend, latestChange, percentChange yerine ortalamalar
4. **Tüm gruplar için tahmin** ✓ - 6 kategori için pencere ortalaması
5. **View değişkenleri korundu** ✓ - matches, avgResultTrends, avgFirstHalfTrends
6. **Sabit pencere boyutu** ✓ - 10 maç sabiti

## Future Enhancements 🔮

- Database integration for real match data
- Historical data analysis
- Machine learning improvements
- REST API endpoints
- Real-time odds integration
- Mobile-responsive design improvements

## Contributing 🤝

This implementation follows the exact requirements specified in the problem statement, providing stable predictions through window-based averaging while preserving the required view data structure.

---
**Last Updated**: September 11, 2025
**Version**: 1.0.0
**Algorithm**: Window-based averaging with fixed window size