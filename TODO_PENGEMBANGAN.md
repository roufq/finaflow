TODO PENGEMBANGAN

- Migrasi UI ke Tailwind v4 sesuai stack; rapikan hirarki informasi dashboard (insight/aksi cepat di atas, detail di bawah), tambah filter periode dan pencarian cepat. [PROGRESS: filter periode + blok prioritas/aksi cepat di dashboard]
- Insight terarah: alert burn rate > X% income, kekurangan emergency fund, rasio utang vs income, anomali pengeluaran; sertakan rekomendasi aksi (transfer dana, potong langganan, batasi kategori boros). [DONE: blok Prioritas Finansial + alert & CTA cepat + badge status risiko di dashboard]
- Integrasi data nyata: impor CSV/OFX/API, auto-kategorisasi berbasis rules/ML sederhana, deduplikasi transaksi, dan refresh saldo/riwayat akun secara periodik.
- Skenario & simulasi: stress test cash flow (income drop/kenaikan biaya), simulasi debt payoff snowball/avalanche, runway konservatif/baseline/agresif dengan slider asumsi.
- Keamanan: aktifkan 2FA, manajemen sesi/device, audit log untuk aksi sensitif (transfer, hapus transaksi), enkripsi kolom kritis (tokens/integration keys).
- Health score & rasio: tampilkan rasio tabungan, debt-to-income, expense-to-income, alokasi portofolio vs target; checklist aksi mingguan/bulanan.
- Langganan & reward: ringkasan biaya berulang dengan reminder pembatalan, rekomendasi downgrade/upgrade paket, kalkulator optimasi poin/kartu kredit berdasar pola belanja.
- Kolaborasi keluarga: peran/izin per anggota, approval workflow untuk shared expenses, notifikasi real-time.
- Observabilitas: event tracking dasar (tanpa PII) untuk validasi fitur, logging error terstruktur, alert kegagalan integrasi.

## RENCANA TERSTRUKTUR (Prioritas → Dasar)

1) PRIORITAS TINGGI
   - UI/UX: Migrasi Tailwind v4, layout ulang dashboard (insight/aksi cepat, filter periode, pencarian), konsistensi komponen.
   - Insight & alert: Burn rate, emergency fund, DTI/ETI, anomali pengeluaran; rekomendasi aksi cepat (transfer, potong langganan, batas kategori). [DONE]
   - Integrasi data: Impor CSV/OFX/API, dedup, auto-kategori, refresh saldo/riwayat akun terjadwal. [PROGRESS: CSV import dedup + auto kategori + mapping akun + sinkronisasi terjadwal]
   - Keamanan: 2FA, manajemen sesi/device, audit log aksi sensitif, enkripsi kolom kredensial integrasi. [DONE: TOTP 2FA + remember device, audit log, enkripsi api_key]

2) PRIORITAS MENENGAH
   - Skenario & simulasi: Stress test cash flow, slider asumsi runway konservatif/baseline/agresif, simulasi payoff snowball/avalanche dengan jadwal aksi.
   - Health score & rasio: KPI personal (savings rate, DTI, ETI, alokasi portofolio vs target), checklist aksi mingguan/bulanan. [DONE: kartu health score + expense/income + debt/income di dashboard]
   - Langganan & reward: Ringkasan biaya berulang, reminder pembatalan, rekomendasi downgrade/upgrade, optimasi poin/kartu kredit.
   - Observabilitas produk: Event tracking dasar (tanpa PII), logging terstruktur, alert kegagalan integrasi.

3) DASAR / FUNDAMENTAL
   - Fondasi arsitektur: Standarisasi komponen UI Tailwind, utilitas format uang/tanggal, guardrails input (validasi, sanitasi).
   - Otomasi dev: CI lint/test minimal, seed data relevan untuk tiap modul utama, skrip sample impor CSV.
   - Dokumentasi singkat: Alur impor data, alur alert/insight, dan kebijakan keamanan (2FA, audit log) untuk memudahkan onboarding tim.
