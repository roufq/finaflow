<?php

return [
    'title' => 'Keuangan Keluarga',
    'dashboard_title' => 'Dasbor Keuangan Keluarga',
    'overview' => [
        'members' => 'Anggota Keluarga',
        'shared_expenses' => 'Pengeluaran Bersama Terbaru',
        'goals' => 'Tujuan Keluarga Aktif',
        'events' => 'Acara Hadiah Mendatang',
    ],
    'buttons' => [
        'add_member' => 'Tambah Anggota',
        'add_expense' => 'Tambah Pengeluaran Bersama',
        'add_goal' => 'Tambah Tujuan Keluarga',
        'add_event' => 'Tambah Acara Hadiah',
        'save' => 'Simpan',
        'cancel' => 'Batal',
        'view_all' => 'Lihat Semua',
    ],
    'members' => [
        'title' => 'Anggota Keluarga',
        'name' => 'Nama',
        'relationship' => 'Hubungan',
        'allowance' => 'Uang Saku Bulanan',
        'balance' => 'Saldo Saat Ini',
    ],
    'shared_expenses' => [
        'title' => 'Pengeluaran Bersama',
        'name' => 'Nama Pengeluaran',
        'category' => 'Kategori',
        'amount' => 'Total Biaya',
        'date' => 'Tanggal Pengeluaran',
        'participants' => 'Peserta',
        'split_method' => 'Metode Pembagian',
        'status' => 'Status',
    ],
    'goals' => [
        'title' => 'Tujuan Keluarga',
        'name' => 'Nama Tujuan',
        'target_amount' => 'Target Dana',
        'target_date' => 'Tanggal Target',
        'contributors' => 'Kontributor',
    ],
    'events' => [
        'title' => 'Acara Hadiah',
        'name' => 'Nama Acara',
        'type' => 'Jenis Acara',
        'date' => 'Tanggal Acara',
        'budget' => 'Anggaran',
        'recipients' => 'Penerima',
    ],
    'empty_states' => [
        'members' => [
            'title' => 'Belum ada anggota keluarga.',
            'description' => 'Tambahkan anggota pertama untuk mulai melacak uang saku.',
            'cta' => 'Tambah Anggota Pertama',
        ],
        'expenses' => [
            'title' => 'Belum ada pengeluaran bersama.',
            'description' => 'Catat pengeluaran bersama agar semua tetap sinkron.',
            'cta' => 'Tambah Pengeluaran Bersama',
        ],
        'goals' => [
            'title' => 'Belum ada tujuan keluarga.',
            'description' => 'Buat tujuan bersama agar lebih termotivasi.',
            'cta' => 'Tambah Tujuan Pertama',
        ],
        'events' => [
            'title' => 'Belum ada acara hadiah.',
            'description' => 'Rencanakan perayaan pertama untuk menyiapkan anggaran.',
            'cta' => 'Rencanakan Acara Pertama',
        ],
    ],
    'statuses' => [
        'settled' => 'Lunas',
        'pending' => 'Menunggu',
    ],
    'messages' => [
        'member_created' => 'Anggota keluarga berhasil ditambahkan.',
        'member_updated' => 'Anggota keluarga berhasil diperbarui.',
        'expense_created' => 'Pengeluaran bersama berhasil ditambahkan.',
        'goal_created' => 'Tujuan keluarga berhasil ditambahkan.',
        'event_created' => 'Acara hadiah berhasil ditambahkan.',
    ],
];
