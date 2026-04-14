@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Integrations Telegram Bot</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-gradient-primary py-4 border-0" style="background: linear-gradient(135deg, #0088cc 0%, #00acee 100%);">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-white rounded-circle mr-3 shadow-sm">
                            <i class="fab fa-telegram-plane fa-2x" style="color: #0088cc;"></i>
                        </div>
                        <div>
                            <h5 class="m-0 font-weight-bold text-white">FinaFlow Chat Assistant</h5>
                            <p class="text-white-50 mb-0 small">Catat transactions lebih cepat via Telegram</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-7">
                            <h6 class="font-weight-bold text-dark mb-3">Apa itu Telegram Bot Integration?</h6>
                            <p class="text-muted small leading-relaxed">
                                Fitur ini memungkinkan Anda untuk mencatat expense harian langsung melalui aplikasi Telegram tanpa perlu membuka dashboard FinaFlow. 
                                Cukup submit pesan singkat, dan sistem AI kami akan memprosesnya secara otomatis.
                            </p>
                            <ul class="list-unstyled small text-muted">
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Gratis & Cepat</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Deteksi Category Otomatis</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Aman & Terenkripsi</li>
                            </ul>
                        </div>
                        <div class="col-md-5 text-center p-3 bg-light rounded" style="border: 1px dashed #cbd5e1;">
                            <div class="mb-3 px-3 py-1 bg-white shadow-sm rounded-pill d-inline-block small font-weight-bold text-primary">Contoh Pesan:</div>
                            <div class="text-left bg-white p-2 rounded shadow-sm mb-2" style="font-family: monospace;">
                                <span class="text-primary font-weight-bold">You:</span> Nasi Goreng 25000
                            </div>
                            <div class="text-left bg-white p-2 rounded shadow-sm" style="font-family: monospace;">
                                <span class="text-info font-weight-bold">Bot:</span> 💰 Transactions Berhasil!
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-4">
                        <h6 class="font-weight-bold text-dark mb-3">Status Koneksi Anda</h6>
                        @if(!$isBotConfigured)
                            <div class="alert alert-warning border-0 shadow-sm py-3 px-4 rounded-lg">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mr-3 text-warning"></i>
                                    <div>
                                        <h6 class="font-weight-bold mb-1">Bot Belum Dikonfigurasi!</h6>
                                        <p class="mb-0 small">Silakan hubungi Administrator atau buka file <code>.env</code> untuk menyetel <code>TELEGRAM_BOT_TOKEN</code> dan <code>TELEGRAM_BOT_NAME</code>.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($user->telegram_id)
                            <div class="d-flex align-items-center p-3 bg-success-light rounded" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="icon-circle bg-success text-white mr-3">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <span class="font-weight-bold text-success">Terhubung!</span>
                                    <p class="mb-0 text-dark small">ID Telegram: <strong>{{ $user->telegram_id }}</strong></p>
                                </div>
                                <div class="ml-auto d-flex" style="gap: 10px;">
                                    <a href="https://t.me/{{ $telegramBotName }}" target="_blank" class="btn btn-success btn-sm px-3 rounded-pill">Buka Bot</a>
                                    <form action="{{ route('integrations.telegram-bot.disconnect') }}" method="POST" onsubmit="return confirm('Are you sure ingin memutuskan koneksi Telegram?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-pill">Putuskan</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 px-3 bg-light rounded" style="border: 1px solid #e2e8f0;">
                                <p class="text-muted mb-4">Account Anda belum terhubung dengan bot Telegram.</p>
                                <a href="https://t.me/{{ $telegramBotName }}?start={{ $telegramToken }}" target="_blank" class="btn btn-primary px-5 py-2 font-weight-bold shadow mb-3" style="background-color: #0088cc; border-radius: 12px; border: none;">
                                    Hubungkan Telegram
                                </a>
                                <p class="small text-muted"><strong>Penting:</strong> Setelah masuk ke Telegram, klik tombol <strong>START</strong> untuk mengaktifkan sinkronisasi.</p>
                                
                                <div class="mt-4 pt-3 border-top">
                                    <p class="small text-uppercase font-weight-bold text-muted mb-2">Gagal redirect? Cara Manual:</p>
                                    <div class="bg-white p-2 border rounded small text-left">
                                        1. Buka Telegram & search: <strong>@ {{ $telegramBotName }}</strong><br>
                                        2. Submit pesan: <code>/start {{ $telegramToken }}</code>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
                    <i class="fas fa-robot text-primary mr-2"></i>
                    <h6 class="m-0 font-weight-bold text-dark">Gunakan Bot Pribadi (BYOB)</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Ingin menggunakan bot Telegram buatan sendiri? Masukkan token dari <strong>@BotFather</strong> di sini.</p>
                    
                    <form action="{{ route('integrations.telegram-bot.settings') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <tags class="small font-weight-bold">Bot API Token</tags>
                            <input type="password" name="telegram_bot_token" class="form-control form-control-sm" placeholder="Contoh: 123456:ABC-DEF..." value="{{ $user->telegram_bot_token }}">
                        </div>
                        <div class="form-group mb-3">
                            <tags class="small font-weight-bold">Bot Username</tags>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">@</span>
                                </div>
                                <input type="text" name="telegram_bot_username" class="form-control" placeholder="finaflow_bot" value="{{ $user->telegram_bot_username }}">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <tags class="small font-weight-bold">Bridge URL (Optional)</tags>
                            <input type="text" name="telegram_proxy_url" class="form-control form-control-sm" placeholder="https://my-proxy.workers.dev/bot-webhook-jembatan" value="{{ $user->telegram_proxy_url }}">
                            <small class="text-muted" style="font-size: 0.75rem;">Gunakan jika server Anda no bisa menerima koneksi langsung dari Telegram.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-block rounded-pill">Save Settings</button>
                    </form>

                    @if($user->telegram_bot_token)
                        <div class="mt-3 pt-3 border-top">
                            <h6 class="small font-weight-bold text-dark mb-2">Sinkronisasi Webhook</h6>
                            <p class="small text-muted mb-3">Klik tombol di bawah agar bot Anda bisa mulai menerima data dari FinaFlow.</p>
                            <form action="{{ route('integrations.telegram-bot.webhook.set') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-info btn-sm btn-block rounded-pill">
                                    <i class="fas fa-sync-alt mr-1"></i> Daftarkan Webhook
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-dark">Panduan Penggunaan</h6>
                </div>
                <div class="card-body">
                    <div class="timeline small">
                        <div class="d-flex mb-4">
                            <div class="mr-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px;">1</div>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1">Hubungkan Sesi</h6>
                                <p class="text-muted mb-0">Klik tombol Hubungkan (Start) untuk mengaktifkan bot.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="mr-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px;">2</div>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1">Submit Transactions</h6>
                                <p class="text-muted mb-0">Ketik [Name Item] spasi [Nominal]. Contoh: "Bensin 20000".</p>
                            </div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="mr-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px;">3</div>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1">Multi-Account</h6>
                                <p class="text-muted mb-0">Tambahkan name account untuk memilih sumber dana. Contoh: "Bensin 20000 Mandiri".</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info border-0 shadow-sm mt-3 py-2 px-3 small">
                        <i class="fas fa-info-circle mr-1"></i> Bot akan menggunakan <strong>Account Utama Aktif</strong> jika name account no disertakan.
                    </div>
                </div>
            </div>

            <!-- Bot History Activity -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
                    <i class="fas fa-history text-secondary mr-2"></i>
                    <h6 class="m-0 font-weight-bold text-dark">History Aktivitas Bot</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4">Aktivitas</th>
                                    <th class="border-0">Keterangan</th>
                                    <th class="border-0 px-4 text-right">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities ?? [] as $activity)
                                    <tr>
                                        <td class="px-4">
                                            @if(Str::contains($activity->action, 'Connected'))
                                                <span class="badge badge-success text-white px-2 py-1"><i class="fas fa-link mr-1"></i> Connected</span>
                                            @elseif(Str::contains($activity->action, 'Disconnected'))
                                                <span class="badge badge-danger text-white px-2 py-1"><i class="fas fa-unlink mr-1"></i> Disconnected</span>
                                            @elseif(Str::contains($activity->action, 'Conflict'))
                                                <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Conflict</span>
                                            @else
                                                <span class="badge badge-info text-white px-2 py-1"><i class="fas fa-edit mr-1"></i> Updated</span>
                                            @endif
                                        </td>
                                        <td class="text-dark">{{ $activity->description }}</td>
                                        <td class="px-4 text-muted text-right">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada aktivitas tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background-color: #4e73df;
        background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        background-size: cover;
    }
    .leading-relaxed {
        line-height: 1.6;
    }
</style>
@endsection
