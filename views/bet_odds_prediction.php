<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bahis Oranları Tahmin Sistemi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .prediction-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .prediction-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: #fafafa;
        }
        .prediction-card h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .metric {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px;
            background-color: white;
            border-radius: 4px;
        }
        .metric-label {
            font-weight: bold;
            color: #555;
        }
        .metric-value {
            color: #2980b9;
            font-weight: bold;
        }
        .confidence-score {
            background-color: #e8f5e8;
            border-left: 4px solid #27ae60;
            margin-top: 10px;
        }
        .info-section {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .matches-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .matches-table th,
        .matches-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .matches-table th {
            background-color: #34495e;
            color: white;
        }
        .matches-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .trend-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .trend-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        .window-size-info {
            background-color: #d5e8d4;
            border: 1px solid #82b366;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
        }
        .percentage {
            color: #e74c3c;
            font-weight: bold;
        }
        .high-confidence {
            color: #27ae60;
        }
        .medium-confidence {
            color: #f39c12;
        }
        .low-confidence {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Bahis Oranları Tahmin Sistemi</h1>
        
        <div class="window-size-info">
            📊 Sabit Pencere Boyutu: <?= $windowSize ?> maç - Stabil tahminler için pencere ortalaması kullanılıyor
        </div>
        
        <h2>📈 Tahmin Analizleri</h2>
        
        <div class="prediction-grid">
            <!-- Match Result Predictions -->
            <div class="prediction-card">
                <h3>🏆 Maç Sonucu Tahminleri</h3>
                <div class="metric">
                    <span class="metric-label">Ev Sahibi Galibiyet:</span>
                    <span class="metric-value percentage"><?= $predictions['match_result']['home_win_probability'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Beraberlik:</span>
                    <span class="metric-value percentage"><?= $predictions['match_result']['draw_probability'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Deplasman Galibiyet:</span>
                    <span class="metric-value percentage"><?= $predictions['match_result']['away_win_probability'] ?>%</span>
                </div>
                <div class="metric confidence-score">
                    <span class="metric-label">Güven Skoru:</span>
                    <span class="metric-value <?= $predictions['match_result']['confidence_score'] >= 70 ? 'high-confidence' : ($predictions['match_result']['confidence_score'] >= 50 ? 'medium-confidence' : 'low-confidence') ?>">
                        <?= $predictions['match_result']['confidence_score'] ?>%
                    </span>
                </div>
            </div>
            
            <!-- Over/Under Predictions -->
            <div class="prediction-card">
                <h3>⚽ Alt/Üst Tahminleri</h3>
                <div class="metric">
                    <span class="metric-label">Ortalama Gol:</span>
                    <span class="metric-value"><?= $predictions['over_under']['avg_goals_per_match'] ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">2.5 Üst Olasılık:</span>
                    <span class="metric-value percentage"><?= $predictions['over_under']['over_25_probability'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">1.5 Üst Olasılık:</span>
                    <span class="metric-value percentage"><?= $predictions['over_under']['over_15_probability'] ?>%</span>
                </div>
                <div class="metric confidence-score">
                    <span class="metric-label">Güven Skoru:</span>
                    <span class="metric-value <?= $predictions['over_under']['confidence_score'] >= 70 ? 'high-confidence' : ($predictions['over_under']['confidence_score'] >= 50 ? 'medium-confidence' : 'low-confidence') ?>">
                        <?= $predictions['over_under']['confidence_score'] ?>%
                    </span>
                </div>
            </div>
            
            <!-- BTTS Predictions -->
            <div class="prediction-card">
                <h3>🎯 Karşılıklı Gol (BTTS)</h3>
                <div class="metric">
                    <span class="metric-label">BTTS Evet:</span>
                    <span class="metric-value percentage"><?= $predictions['btts']['btts_yes_probability'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">BTTS Hayır:</span>
                    <span class="metric-value percentage"><?= $predictions['btts']['btts_no_probability'] ?>%</span>
                </div>
                <div class="metric confidence-score">
                    <span class="metric-label">Güven Skoru:</span>
                    <span class="metric-value <?= $predictions['btts']['confidence_score'] >= 70 ? 'high-confidence' : ($predictions['btts']['confidence_score'] >= 50 ? 'medium-confidence' : 'low-confidence') ?>">
                        <?= $predictions['btts']['confidence_score'] ?>%
                    </span>
                </div>
            </div>
            
            <!-- First Half Predictions -->
            <div class="prediction-card">
                <h3>🕐 İlk Yarı Tahminleri</h3>
                <div class="metric">
                    <span class="metric-label">Ort. İlk Yarı Gol:</span>
                    <span class="metric-value"><?= $predictions['first_half']['avg_first_half_goals'] ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Ev Sahibi Önde:</span>
                    <span class="metric-value percentage"><?= $predictions['first_half']['home_leading_probability'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">İlk Yarı Beraberlik:</span>
                    <span class="metric-value percentage"><?= $predictions['first_half']['first_half_draw_probability'] ?>%</span>
                </div>
                <div class="metric confidence-score">
                    <span class="metric-label">Güven Skoru:</span>
                    <span class="metric-value <?= $predictions['first_half']['confidence_score'] >= 70 ? 'high-confidence' : ($predictions['first_half']['confidence_score'] >= 50 ? 'medium-confidence' : 'low-confidence') ?>">
                        <?= $predictions['first_half']['confidence_score'] ?>%
                    </span>
                </div>
            </div>
            
            <!-- Second Half Predictions -->
            <div class="prediction-card">
                <h3>🕐 İkinci Yarı Tahminleri</h3>
                <div class="metric">
                    <span class="metric-label">Ort. İkinci Yarı Gol:</span>
                    <span class="metric-value"><?= $predictions['second_half']['avg_second_half_goals'] ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">İkinci Yarıda Daha Çok Gol:</span>
                    <span class="metric-value percentage"><?= $predictions['second_half']['more_goals_second_half_probability'] ?>%</span>
                </div>
                <div class="metric confidence-score">
                    <span class="metric-label">Güven Skoru:</span>
                    <span class="metric-value <?= $predictions['second_half']['confidence_score'] >= 70 ? 'high-confidence' : ($predictions['second_half']['confidence_score'] >= 50 ? 'medium-confidence' : 'low-confidence') ?>">
                        <?= $predictions['second_half']['confidence_score'] ?>%
                    </span>
                </div>
            </div>
            
            <!-- HT/FT Predictions -->
            <div class="prediction-card">
                <h3>🔄 İlk Yarı/Maç Sonu (HT/FT)</h3>
                <?php foreach ($predictions['ht_ft']['patterns'] as $pattern => $probability): ?>
                    <div class="metric">
                        <span class="metric-label"><?= $pattern ?>:</span>
                        <span class="metric-value percentage"><?= $probability ?>%</span>
                    </div>
                <?php endforeach; ?>
                <div class="metric confidence-score">
                    <span class="metric-label">Güven Skoru:</span>
                    <span class="metric-value <?= $predictions['ht_ft']['confidence_score'] >= 70 ? 'high-confidence' : ($predictions['ht_ft']['confidence_score'] >= 50 ? 'medium-confidence' : 'low-confidence') ?>">
                        <?= $predictions['ht_ft']['confidence_score'] ?>%
                    </span>
                </div>
            </div>
        </div>
        
        <div class="trend-summary">
            <div class="trend-card">
                <h3>📊 Ortalama Sonuç Trendleri</h3>
                <div class="metric">
                    <span class="metric-label">Ev Sahibi Galibiyet Ort.:</span>
                    <span class="metric-value percentage"><?= $avgResultTrends['home_win_avg'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Beraberlik Ort.:</span>
                    <span class="metric-value percentage"><?= $avgResultTrends['draw_avg'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Deplasman Galibiyet Ort.:</span>
                    <span class="metric-value percentage"><?= $avgResultTrends['away_win_avg'] ?>%</span>
                </div>
            </div>
            
            <div class="trend-card">
                <h3>🕐 Ortalama İlk Yarı Trendleri</h3>
                <div class="metric">
                    <span class="metric-label">Ortalama Gol:</span>
                    <span class="metric-value"><?= $avgFirstHalfTrends['avg_goals'] ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Ev Sahibi Önde Oran:</span>
                    <span class="metric-value percentage"><?= $avgFirstHalfTrends['home_leading_rate'] ?>%</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Beraberlik Oran:</span>
                    <span class="metric-value percentage"><?= $avgFirstHalfTrends['draw_rate'] ?>%</span>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>ℹ️ Sistem Bilgileri</h3>
            <p><strong>Algoritma:</strong> Pencere tabanlı ortalama hesaplama kullanılarak stabil tahminler üretilmektedir.</p>
            <p><strong>Pencere Boyutu:</strong> <?= $windowSize ?> maç verisi analiz edilerek trend ve değişken metriklerin yerine güvenilir ortalamalar kullanılır.</p>
            <p><strong>Güven Skoru:</strong> Veri tutarlılığına dayalı olarak hesaplanır. Yüksek skor daha güvenilir tahminleri ifade eder.</p>
            <p><strong>Son Güncelleme:</strong> <?= date('d.m.Y H:i:s') ?></p>
        </div>
        
        <h3>📋 Son Maç Sonuçları (Pencere Verisi)</h3>
        <table class="matches-table">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Ev Sahibi</th>
                    <th>Skor</th>
                    <th>Deplasman</th>
                    <th>İlk Yarı</th>
                    <th>Liga</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($matches, 0, $windowSize) as $match): ?>
                <tr>
                    <td><?= date('d.m.Y', strtotime($match['match_date'])) ?></td>
                    <td><?= htmlspecialchars($match['home_team']) ?></td>
                    <td><strong><?= $match['home_score'] ?> - <?= $match['away_score'] ?></strong></td>
                    <td><?= htmlspecialchars($match['away_team']) ?></td>
                    <td><?= $match['first_half_home'] ?> - <?= $match['first_half_away'] ?></td>
                    <td><?= htmlspecialchars($match['league']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>