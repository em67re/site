# BetOddsPredictionController

## Yeni Tahmin Algoritması / New Prediction Algorithm

Bu repository'de, bahis oranları tahmini için geliştirilmiş yeni bir algoritma bulunmaktadır. Algoritma, sadece oran değişimlerini kullanarak daha gerçekçi tahminler yapmaktadır.

This repository contains an improved algorithm for betting odds prediction. The algorithm uses only ratio changes to make more realistic predictions.

### Algorithm Features / Algoritma Özellikleri

- **Sadece oran değişimleri kullanır** / Uses only ratio changes
- **Üç metrik hesaplar** / Calculates three metrics:
  - Trend (son değer - ilk değer) / Trend (last value - first value)
  - Değişim hızı (son iki değer farkı) / Rate of change (difference between last two values)
  - Yüzdesel değişim / Percentage change
- **Ağırlıklı skorlama** / Weighted scoring: `trend × 0.5 + değişim_hızı × 0.3 + yüzdesel_değişim × 0.2`
- **En yüksek skor tahmini** / Highest score prediction
- **Güven seviyesi hesabı** / Confidence calculation: `max_skor / toplam_skorlar`

### Usage / Kullanım

```php
<?php
require_once 'BetOddsPredictionController.php';

$predictor = new BetOddsPredictionController();

$data = [
    'ft1_diff' => [1.50, 1.55, 1.60, 1.58, 1.62, 1.65],
    'ft2_diff' => [2.10, 2.05, 2.00, 2.02, 1.98, 1.95],
    // ... other fields
];

$result = $predictor->predictFromRatioChanges($data);

echo "Prediction: " . $result['prediction'] . "\n";
echo "Confidence: " . ($result['confidence'] * 100) . "%\n";
?>
```

### Files / Dosyalar

- `BetOddsPredictionController.php` - Ana algoritma dosyası / Main algorithm file
- `test_prediction.php` - Test dosyası / Test file
- `example_usage.php` - Kullanım örneği / Usage example

### Algorithm Requirements Met / Karşılanan Algoritma Gereksinimleri

✅ Önceki ağırlıklı toplam yaklaşımı kaldırıldı / Previous weighted sum approach removed  
✅ Sadece oran değişimi skorları kullanılıyor / Only ratio change scores used  
✅ Trend ve değişim hızına dayalı ağırlıklandırma / Weighting based on trend and rate of change  
✅ Belirtilen metodoloji uygulandı / Specified methodology implemented  
✅ Açıklama satırları eklendi / Explanatory comments added  
✅ Güncellenmiş fonksiyonlar hazırlandı / Updated functions prepared  

### Testing / Test

Run tests with / Testleri çalıştır:
```bash
php test_prediction.php
php example_usage.php
```