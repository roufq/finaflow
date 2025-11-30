# TODO: Perbaikan Responsif (Mobile–Desktop)

## Tujuan
Menjadikan seluruh halaman nyaman dipakai di HP hingga layar besar tanpa overflow horizontal, scroll terjebak, atau elemen terlalu kecil/besar.

## Pekerjaan Prioritas
- [ ] **Layout scroll & body** — Hapus pola `body/#content-wrapper height: 100vh + overflow: hidden` dan izinkan scroll normal; kunci scroll hanya saat sidebar mobile terbuka.
- [ ] **Sidebar mobile** — Ubah lebar tetap 260px menjadi lebar dinamis (mis. `min(88vw, 260px)`), pastikan overlay menutup, dan transisi tidak memotong konten.
- [ ] **Header filter dashboard** — Hilangkan `style="width: 110px"` dan `min-width: 260px`; gunakan utilitas responsif (`w-100 w-sm-auto`, `flex-column flex-sm-row`) supaya form tidak overflow di layar <360px.
- [ ] **Filter halaman transaksi** — Ganti min-width 240px dan kontrol sejajar menjadi layout bertingkat di mobile (grid/stack), tombol reset/link tetap di baris yang sama di desktop.
- [ ] **Tombol aksi cepat** — Jadikan tombol header/aksi cepat (dashboard, transaksi) full-width di mobile dan beri gap konsisten; pastikan ukuran sentuh ≥44px.
- [ ] **Chart container** — Ganti tinggi tetap (280px/300px) menjadi `clamp(220px, 30vh, 420px)` dan set Chart.js `maintainAspectRatio: false` + legend kondisional untuk mobile.
- [ ] **Tabel lebar** — Tambahkan tampilan kartu untuk transaksi (dan tabel lain berkolom banyak), tampilkan tabel penuh hanya di ≥md; hilangkan teks terpotong di user-agent activity log.
- [ ] **Inline style fixed width/height** — Inventarisasi dan ubah inline width/height yang memicu overflow (progress bars ok), gunakan kelas responsif Bootstrap/Tailwind yang sudah ada di proyek.

## Verifikasi
- [ ] Cek di viewport 320px, 375px, 414px, 768px, 1024px: tidak ada horizontal scroll; sidebar aman dibuka-tutup.
- [ ] Uji form/filter: semua kontrol terbaca dan dapat disentuh dengan nyaman di HP.
- [ ] Chart dan tabel tidak keluar layar; loading skeleton tetap proporsional.
- [ ] Navigasi dan CTA tetap mudah diakses setelah perubahan.
