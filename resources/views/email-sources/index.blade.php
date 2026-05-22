@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Email Parser Sources</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola daftar email yang digunakan untuk memantau transaksi otomatis. Kirim atau teruskan (forward) email notifikasi bank Anda ke sistem, dan FinaFlow akan secara otomatis membacanya dan membuat transaksi.</p>
        </div>
    </div>

    @if (session('success'))
    <div class="rounded-2xl bg-green-50 p-4 border border-green-100">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if (session('error'))
    <div class="rounded-2xl bg-red-50 p-4 border border-red-100">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="rounded-2xl bg-red-50 p-4 border border-red-100">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <div class="ml-3">
                <ul class="list-disc list-inside text-sm font-medium text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add New Email Form -->
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-premium border border-slate-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-primary-50 rounded-bl-full -z-10 opacity-50"></div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fas fa-plus-circle text-primary-500"></i> Tambah Email Baru
                </h3>
                <form action="{{ route('email-sources.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email_address" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email Pemicu</label>
                        <input type="email" name="email_address" id="email_address" required placeholder="contoh: budi@gmail.com" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10 transition-all">
                        <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                            Pastikan email ini adalah email yang akan menerima notifikasi dari Bank (misal BCA, Mandiri) atau email yang Anda gunakan untuk meneruskan (forward) pesan ke FinaFlow.
                        </p>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-500 active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Simpan Email
                    </button>
                </form>
            </div>

            <!-- How it works -->
            <div class="rounded-3xl bg-slate-900 p-6 shadow-premium relative overflow-hidden text-white">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-bl-full -z-10"></div>
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-4">
                    <i class="fas fa-bolt text-yellow-400"></i> Cara Kerja AI Parser
                </h3>
                <ul class="space-y-4 text-xs text-slate-400">
                    <li class="flex gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/10 flex items-center justify-center font-bold text-white">1</div>
                        <p>Daftarkan email yang sering menerima notifikasi bank (e.g. m-BCA).</p>
                    </li>
                    <li class="flex gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/10 flex items-center justify-center font-bold text-white">2</div>
                        <p>Setiap ada email masuk, <strong class="text-white">AI FinaFlow</strong> akan membacanya secara otomatis.</p>
                    </li>
                    <li class="flex gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/10 flex items-center justify-center font-bold text-white">3</div>
                        <p>Sistem akan membedakan tipe <strong class="text-white">Pemasukan / Pengeluaran</strong> berdasarkan konteks kalimat.</p>
                    </li>
                    <li class="flex gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/10 flex items-center justify-center font-bold text-white">4</div>
                        <p>Transaksi dicatat langsung ke dalam buku jurnal akun bank Anda.</p>
                    </li>
                </ul>
            </div>

            <!-- Webhook Connection Info -->
            <div class="rounded-3xl bg-slate-900 p-6 shadow-premium relative overflow-hidden text-white">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-bl-full -z-10"></div>
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-4">
                    <i class="fas fa-link text-blue-400"></i> Endpoint Webhook
                </h3>
                <p class="text-xs text-slate-400 mb-3 leading-relaxed">
                    Sistem sudah <strong class="text-white">Otomatis</strong>. Tidak ada tombol "Koneksikan". Anda cukup menyalin URL di bawah ini ke Zapier/Make. FinaFlow mendeteksi otomatis identitas Anda dari email yang dikirimkan.
                </p>
                
                <div class="rounded-xl bg-black/50 border border-slate-700/50 p-3 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Method</span>
                        <span class="text-[10px] font-bold text-green-400 bg-green-400/10 px-2 py-0.5 rounded">POST</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase block mb-1">URL Endpoint</span>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ url('/api/v1/webhooks/email-parser') }}" class="w-full bg-transparent text-[11px] text-slate-300 border-none p-0 focus:ring-0 cursor-text select-all font-mono" id="webhook-url">
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('webhook-url').value); alert('URL disalin!')" class="text-slate-400 hover:text-white transition-colors flex-shrink-0" title="Salin URL">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email List -->
        <div class="lg:col-span-2">
            <div class="rounded-3xl bg-white p-6 shadow-premium border border-slate-100">
                <h3 class="text-base font-bold text-slate-900 mb-4">Email Terdaftar</h3>
                
                @if($emailSources->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 mb-4">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Belum ada Email</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs">Tambahkan email sumber pertama Anda untuk mulai mengotomatisasi pencatatan transaksi Anda.</p>
                </div>
                @else
                <div class="overflow-hidden rounded-2xl border border-slate-100">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-500">Email Address</th>
                                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-500">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-wider text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($emailSources as $source)
                            <tr class="transition-all hover:bg-slate-50/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm">{{ $source->email_address }}</div>
                                            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                                                @if($source->last_received_at)
                                                    <span class="flex items-center gap-1 text-green-600 font-bold bg-green-50 px-1.5 py-0.5 rounded">
                                                        <span class="relative flex h-2 w-2">
                                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                        </span>
                                                        Terkoneksi
                                                    </span> 
                                                    <span>Terakhir sinkronisasi {{ $source->last_received_at->diffForHumans() }}</span>
                                                @else
                                                    <span class="flex items-center gap-1 text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded">
                                                        <i class="fas fa-circle-notch fa-spin text-[10px]"></i> Menunggu
                                                    </span>
                                                    <span>Belum ada Webhook masuk</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if($source->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Active
                                    </span>
                                    @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                        Inactive
                                    </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('email-sources.toggle', $source) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-500 transition-all hover:bg-slate-200 hover:text-slate-900" title="{{ $source->is_active ? 'Matikan' : 'Aktifkan' }}">
                                                <i class="fas fa-power-off text-xs"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('email-sources.destroy', $source) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus email ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition-all hover:bg-rose-500 hover:text-white" title="Hapus">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
