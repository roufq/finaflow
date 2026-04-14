@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Voice-to-Text Transaction Entry</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integrations.voice-entry.store') }}">
                        @csrf
                        <div class="form-group">
                            <tags>Transcribed Text</tags>
                            <textarea name="voice_text" id="voice_text" class="form-control @error('voice_text') is-invalid @enderror" rows="5">{{ old('voice_text') }}</textarea>
                            @error('voice_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <button type="button" id="startRecording" class="btn btn-outline-primary">
                                <i class="fas fa-microphone"></i> Start Recording
                            </button>
                            <button type="button" id="stopRecording" class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-stop"></i> Stop
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Transaction</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Example phrases:</p>
                    <ul>
                        <li>"Expense 125000 on groceries at Indomaret"</li>
                        <li>"Income 5000000 salary payment"</li>
                        <li>"Pay electricity bill 350000 due tomorrow"</li>
                    </ul>
                    @if(session('parsed'))
                        <hr>
                        <h6>Last Entry</h6>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Amount:</strong> Rp {{ number_format(session('parsed.amount'), 0, ',', '.') }}</li>
                            <li><strong>Type:</strong> {{ ucfirst(session('parsed.type')) }}</li>
                            <li><strong>Description:</strong> {{ session('parsed.description') }}</li>
                            <li><strong>Date:</strong> {{ session('parsed.date') }}</li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
if ('webkitSpeechRecognition' in window) {
    const recognition = new webkitSpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    const startBtn = document.getElementById('startRecording');
    const stopBtn = document.getElementById('stopRecording');
    const textarea = document.getElementById('voice_text');

    startBtn.addEventListener('click', () => {
        recognition.start();
        startBtn.disabled = true;
        stopBtn.disabled = false;
    });

    stopBtn.addEventListener('click', () => {
        recognition.stop();
        startBtn.disabled = false;
        stopBtn.disabled = true;
    });

    recognition.onresult = event => {
        let transcript = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
            transcript += event.results[i][0].transcript + ' ';
        }
        textarea.value = transcript.trim();
    };
}
</script>
@endsection
