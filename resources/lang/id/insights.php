<?php

return [
    'title' => 'Wawasan AI',
    'generate_insights' => 'Hasilkan Wawasan',
    'messages' => [
        'insights_generated' => 'Wawasan AI telah berhasil dihasilkan.',
        'recommendation_marked_read' => 'Rekomendasi ditandai sebagai sudah dibaca.',
        'anomaly_resolved' => 'Anomali ditandai sebagai teratasi.',
    ],

    'recommendations' => [
        'title' => 'Rekomendasi',
        'type' => 'Jenis',
        'content' => 'Konten',
        'priority' => 'Prioritas',
        'status' => 'Status',
        'read' => 'Dibaca',
        'unread' => 'Belum Dibaca',
        'mark_as_read' => 'Tandai sebagai Dibaca',
        'priority_1' => 'Rendah',
        'priority_2' => 'Sedang',
        'priority_3' => 'Tinggi',
        'low_savings_rate' => 'Tingkat tabungan Anda di bawah 10%. Pertimbangkan untuk meningkatkan tabungan untuk membangun keamanan finansial.',
        'build_emergency_fund' => 'Bangun dana darurat yang mencakup setidaknya 3 bulan pengeluaran.',
        'high_debt_ratio' => 'Rasio hutang terhadap pendapatan Anda tinggi. Pertimbangkan untuk melunasi hutang atau meningkatkan pendapatan.',
        'overspending' => 'Anda membelanjakan berlebihan bulan ini. Tinjau anggaran dan kebiasaan belanja Anda.',
    ],

    'anomalies' => [
        'title' => 'Anomali',
        'type' => 'Jenis',
        'description' => 'Deskripsi',
        'severity' => 'Tingkat Keparahan',
        'status' => 'Status',
        'detected_at' => 'Terdeteksi Pada',
        'resolved' => 'Teratasi',
        'unresolved' => 'Belum Teratasi',
        'mark_resolved' => 'Tandai Teratasi',
        'unusual_amount' => 'Jumlah transaksi tidak biasa sebesar :amount dalam :category',
        'duplicate_transaction' => 'Transaksi duplikat: :description (:count kali)',
        'severity_low' => 'Rendah',
        'severity_medium' => 'Sedang',
        'severity_high' => 'Tinggi',
    ],

    'predictions' => [
        'title' => 'Prediksi',
        'category' => 'Kategori',
        'predicted_amount' => 'Jumlah Prediksi',
        'confidence' => 'Keyakinan',
        'period' => 'Periode',
        'prediction_date' => 'Tanggal Prediksi',
        'expected_amount' => 'Diharapkan :amount :period',
    ],

    'no_recommendations' => 'Tidak ada rekomendasi tersedia.',
    'no_anomalies' => 'Tidak ada anomali yang terdeteksi.',
    'no_predictions' => 'Tidak ada prediksi tersedia.',
    'generate_recommendations_hint' => 'Hasilkan wawasan untuk mendapatkan rekomendasi yang dipersonalisasi.',
    'generate_anomalies_hint' => 'Hasilkan wawasan untuk mendeteksi pola pengeluaran yang tidak biasa.',
    'generate_predictions_hint' => 'Hasilkan wawasan untuk melihat prediksi pengeluaran.',
];
