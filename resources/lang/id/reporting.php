<?php

return [
    'title' => 'Laporan Kustom',
    'subtitle' => 'Bangun dashboard pribadi, ekspor insight, dan atur jadwal pengiriman.',
    'dashboard' => [
        'title' => 'Dasbor Pelaporan',
        'subtitle' => 'Pantau laporan tersimpan dan kelola pustaka widget.',
        'create_quick' => 'Buat Laporan Cepat',
        'reports' => 'Laporan Tersimpan',
        'widgets' => 'Widget',
        'recent_activity' => 'Aktivitas Terbaru',
        'empty_reports' => 'Belum ada laporan kustom yang dibuat.',
        'empty_widgets' => 'Tambahkan widget di builder untuk menampilkan data.',
    ],
    'metrics' => [
        'income' => 'Total Pemasukan',
        'expenses' => 'Total Pengeluaran',
        'net_flow' => 'Arus Kas Bersih',
        'goals' => 'Tujuan Aktif',
    ],
    'builder' => [
        'title' => 'Report Builder',
        'subtitle' => 'Seret & letakkan widget untuk mendesain dashboard ideal.',
        'available_widgets' => 'Widget Tersedia',
        'layout_preview' => 'Pratinjau Tata Letak',
        'empty' => 'Belum ada widget. Gunakan pustaka widget untuk memulai.',
        'instructions' => 'Seret widget antar zona atau ubah ukurannya sesuai kebutuhan.',
    ],
    'forms' => [
        'name' => 'Nama Laporan',
        'schedule' => 'Jadwal Pengiriman',
        'format' => 'Format Default',
        'description' => 'Deskripsi atau filter',
        'submit' => 'Buat Laporan',
        'widget_type' => 'Jenis Widget',
        'widget_title' => 'Judul Widget',
        'widget_size' => 'Ukuran Widget',
        'add_widget' => 'Tambahkan Widget',
        'filters' => 'Filter',
        'notes' => 'Catatan',
        'report' => 'Laporan',
        'choose_report' => 'Pilih Laporan',
    ],
    'widgets' => [
        'cash_flow' => [
            'title' => 'Arus Kas',
            'description' => 'Pantau pemasukan vs pengeluaran dalam periode tertentu.',
        ],
        'spending_category' => [
            'title' => 'Pengeluaran per Kategori',
            'description' => 'Visualisasikan pengeluaran per kategori untuk melihat tren.',
        ],
        'goal_progress' => [
            'title' => 'Progres Tujuan',
            'description' => 'Awasi status setiap tujuan finansial dan sisa target.',
        ],
        'budget_health' => [
            'title' => 'Kesehatan Anggaran',
            'description' => 'Soroti anggaran yang aman maupun yang rawan overspending.',
        ],
        'net_worth' => [
            'title' => 'Tren Kekayaan Bersih',
            'description' => 'Lacak pergerakan kekayaan bersih dan proyeksinya.',
        ],
        'custom' => [
            'title' => 'Widget Kustom',
            'description' => 'Buat tile data manual dengan catatan atau KPI spesifik.',
        ],
    ],
    'messages' => [
        'report_created' => 'Laporan berhasil dibuat.',
        'widget_created' => 'Widget berhasil ditambahkan.',
        'widget_updated' => 'Widget berhasil diperbarui.',
        'widget_deleted' => 'Widget berhasil dihapus.',
        'layout_saved' => 'Tata letak diperbarui.',
    ],
    'buttons' => [
        'open_builder' => 'Buka Builder',
        'export' => 'Ekspor',
        'generate' => 'Bangun',
        'manage_widgets' => 'Kelola Widget',
    ],
    'exports' => [
        'title' => 'Ringkasan Laporan',
        'summary' => 'Ringkasan Metrik',
        'transactions' => 'Transaksi Terbaru',
        'widgets' => 'Widget',
    ],
    'schedules' => [
        'daily' => 'Harian',
        'weekly' => 'Mingguan',
        'monthly' => 'Bulanan',
        'quarterly' => 'Triwulanan',
        'yearly' => 'Tahunan',
    ],
    'formats' => [
        'pdf' => 'PDF',
        'excel' => 'Excel',
        'csv' => 'CSV',
    ],
];
