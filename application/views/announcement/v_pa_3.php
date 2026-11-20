<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HMS - PA System V2</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
            rel="stylesheet">
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                background: #f4f6f9;
                font-family: 'Segoe UI', Tahoma, sans-serif
            }

            .card {
                border: 0;
                border-radius: 10px;
                box-shadow: 0 4px 8px #0000000d
            }

            #pesanInput {
                min-height: 110px
            }

            .mono {
                font-family: Consolas, monospace
            }

            .status-line {
                min-height: 24px
            }
        </style>
    </head>

    <body>
        <nav class="navbar navbar-dark bg-primary mb-4">
            <div class="container"><span class="navbar-brand fw-bold"><i
                        class="fa-solid fa-industry me-2"></i>HMS - PA System V2</span><span
                    class="badge bg-light text-primary" id="systemStatus">Siap</span></div>
        </nav>
        <div class="container mb-5">
            <div class="card">
                <div
                    class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 text-primary"><i
                            class="fa-solid fa-bullhorn me-2"></i>Panel Broadcast & Preview</h5>
                    <span class="badge bg-info text-dark" id="durationBadge">Estimasi: 0
                        detik</span>
                </div>
                <div class="card-body">
                    <div class="row bg-light p-3 rounded mb-3 border">
                        <div class="col-md-6 mb-2"><label for="esp32Ip"
                                                          class="form-label fw-bold">IP Address ESP32</label><input
                                                          id="esp32Ip" class="form-control" value="192.168.1.195"
                                                          placeholder="192.168.1.195"></div>
                        <div class="col-md-6 mb-2"><label for="esp32Token"
                                                          class="form-label fw-bold">Token Device</label><input
                                                          id="esp32Token" class="form-control" value="GANTI_TOKEN_KUAT"
                                                          autocomplete="off"></div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="pesanInput" class="form-label fw-bold m-0">1. Teks
                                Pengumuman</label>
                            <div><button class="btn btn-sm btn-outline-secondary" type="button"
                                         onclick="tambahJeda('1s')">+ Jeda 1 detik</button> <button
                                         class="btn btn-sm btn-outline-secondary" type="button"
                                         onclick="tambahJeda('2s')">+ Jeda 2 detik</button></div>
                        </div>
                        <textarea id="pesanInput" class="form-control"
                                  oninput="hitungDurasiTotal()">Perhatian kepada seluruh staf. <break time="1s"/> Jadwal pengecekan mesin produksi akan segera dimulai.</textarea><small
                                  class="text-muted">Gunakan SSML, misalnya <code>&lt;break
                                time="1s"/&gt;</code>.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label for="repetition"
                                                          class="form-label fw-bold">2. Pengulangan</label><select
                                                          id="repetition" class="form-select"
                                                          onchange="hitungDurasiTotal()">
                                <option value="1">1x</option>
                                <option value="3">3x</option>
                                <option value="5">5x</option>
                            </select></div>
                        <div class="col-md-4 mb-3"><label for="voiceType"
                                                          class="form-label fw-bold">3. Jenis Suara</label><select
                                                          id="voiceType" class="form-select">
                                <option value="FEMALE">Wanita</option>
                                <option value="MALE">Pria</option>
                            </select></div>
                        <div class="col-md-4 mb-3"><label for="speedSelect"
                                                          class="form-label fw-bold">4. Kecepatan</label><select
                                                          id="speedSelect" class="form-select"
                                                          onchange="hitungDurasiTotal()">
                                <option value="0.8">Lambat (0.8)</option>
                                <option value="1" selected>Normal (1.0)</option>
                            </select></div>
                    </div>
                    <div class="mb-3"><label class="form-label fw-bold">5. Efek
                            Lonceng</label>
                        <div class="form-check"><input class="form-check-input"
                                                       type="checkbox" id="useChime" checked
                                                       onchange="hitungDurasiTotal()"><label class="form-check-label"
                                                       for="useChime">Opening & closing chime</label></div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between"><label
                                class="form-label small text-muted">Progres audio
                                sebenarnya</label><span class="small text-muted"
                                                    id="progressText">0%</span></div>
                        <div class="progress" style="height:10px">
                            <div id="broadcastProgress"
                                 class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                 style="width:0%"></div>
                        </div>
                    </div>
                    <div class="status-line small text-muted mb-3" id="detailStatus">Siap
                        menerima pengumuman.</div>
                    <hr>
                    <div
                        class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary btn-lg"
                                onclick="previewSound()"><i
                                class="fa-solid fa-headphones me-1"></i>Preview PC</button>
                        <div class="d-flex gap-2"><button type="button"
                                                          class="btn btn-outline-danger btn-lg" id="stopButton"
                                                          onclick="stopBroadcast()" disabled><i
                                    class="fa-solid fa-stop me-1"></i>STOP</button><button
                                type="button" class="btn btn-success btn-lg px-4"
                                id="broadcastButton" onclick="triggerBroadcast()"><i
                                    class="fa-solid fa-tower-broadcast me-1"></i>Broadcast</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning mt-3 small"><b>Catatan keamanan:</b> jangan
                menaruh Google API key aktif di file HTML produksi. Panggilan TTS
                sebaiknya dipindahkan ke backend HMS (PHP) dan dibatasi di server. Versi
                ini memakai placeholder API key agar key lama yang pernah tertanam tidak
                disalin kembali.</div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
                                    // Isi sementara untuk pengujian saja. Produksi: pindahkan request TTS ke endpoint backend HMS.
                                    const GOOGLE_API_KEY = 'AIzaSyAbT_luJzC1H08Zf8Kh-6QiWgP9PlJyKLk';
                                    let currentAudio = null,
                                            wsESP32 = null,
                                            progressTimer = null;
                                    let broadcastState = 'IDLE';

                                    function setStatus(text, cls = 'bg-secondary') {
                                        document.getElementById('systemStatus').className = 'badge ' + cls;
                                        document.getElementById('systemStatus').textContent = text;
                                        document.getElementById('detailStatus').textContent = text;
                                    }

                                    function setProgress(p) {
                                        const v = Math.max(0, Math.min(100, p));
                                        document.getElementById('broadcastProgress').style.width = v + '%';
                                        document.getElementById('progressText').textContent = Math.round(v) + '%';
                                    }

                                    function tambahJeda(durasi) {
                                        const el = document.getElementById('pesanInput'),
                                                a = el.selectionStart,
                                                b = el.selectionEnd;
                                        el.value = el.value.slice(0, a) + ` <break time="${durasi}"/> ` + el.value
                                                .slice(b);
                                        el.focus();
                                        hitungDurasiTotal();
                                    }

                                    function hitungDurasiTotal() {
                                        const raw = document.getElementById('pesanInput').value,
                                                speed = +document.getElementById('speedSelect').value || 1,
                                                rep = +document.getElementById('repetition').value || 1;
                                        let breaks = 0;
                                        for (const m of raw.matchAll(/<break\s+time="(\d+)(ms|s)"\s*\/?>/g))
                                            breaks += +m[1] * (m[2] === 's' ? 1000 : 1);
                                        const clean = raw.replace(/<[^>]*>/g, ' ').trim();
                                        const seconds = ((clean.length / 14) / speed + breaks / 1000) * rep + (
                                                rep - 1) * 1.5 + (document.getElementById('useChime').checked ? 4.2 :
                                                0);
                                        document.getElementById('durationBadge').textContent = 'Estimasi: ' + Math
                                                .ceil(seconds) + ' detik';
                                        return seconds;
                                    }

                                    function decodeBase64(b64) {
                                        const bin = atob(b64),
                                                out = new Uint8Array(bin.length);
                                        for (let i = 0; i < bin.length; i++)
                                            out[i] = bin.charCodeAt(i);
                                        return out;
                                    }

                                    function readU32(v, o) {
                                        return v.getUint32(o, true);
                                    }

                                    function parseLinear16Wav(bytes) {
                                        // Accept a WAV/RIFF response and locate its data chunk; also tolerate raw PCM as fallback.
                                        if (bytes.length >= 12 && String.fromCharCode(...bytes.subarray(0, 4)) ===
                                                'RIFF') {
                                            const view = new DataView(bytes.buffer, bytes.byteOffset, bytes
                                                    .byteLength);
                                            let offset = 12,
                                                    channels = 1,
                                                    sampleRate = 16000,
                                                    bits = 16,
                                                    format = 1,
                                                    dataOffset = -1,
                                                    dataSize = 0;
                                            while (offset + 8 <= bytes.length) {
                                                const id = String.fromCharCode(...bytes.subarray(offset, offset + 4)),
                                                        size = readU32(view, offset + 4),
                                                        start = offset + 8;
                                                if (start + size > bytes.length)
                                                    throw new Error(
                                                            'Chunk WAV tidak valid.');
                                                if (id === 'fmt ') {
                                                    format = view.getUint16(start, true);
                                                    channels = view.getUint16(start + 2, true);
                                                    sampleRate = view.getUint32(start + 4, true);
                                                    bits = view.getUint16(start + 14, true);
                                                }
                                                if (id === 'data') {
                                                    dataOffset = start;
                                                    dataSize = size;
                                                    break;
                                                }
                                                offset = start + size + (size & 1);
                                            }
                                            if (dataOffset < 0)
                                                throw new Error('Chunk data WAV tidak ditemukan.');
                                            if (format !== 1 || bits !== 16 || sampleRate !== 16000)
                                                throw new Error(
                                                        `Format TTS tidak sesuai: format=${format}, ${bits}-bit, ${sampleRate}Hz.`
                                                        );
                                            if (channels !== 1 && channels !== 2)
                                                throw new Error(
                                                        'Jumlah channel TTS tidak didukung.');
                                            const samples = dataSize / 2;
                                            if (samples % channels !== 0)
                                                throw new Error(
                                                        'Ukuran data PCM tidak valid.');
                                            const src = new Int16Array(bytes.buffer, bytes.byteOffset + dataOffset,
                                                    dataSize / 2);
                                            if (channels === 1)
                                                return new Int16Array(src);
                                            const mono = new Int16Array(src.length / 2);
                                            for (let i = 0; i < mono.length; i++)
                                                mono[i] = Math.round((src[i * 2] +
                                                        src[i * 2 + 1]) / 2);
                                            return mono;
                                        }
                                        if (bytes.length % 2)
                                            throw new Error(
                                                    'Raw PCM memiliki jumlah byte ganjil.');
                                        return new Int16Array(bytes.buffer.slice(bytes.byteOffset, bytes
                                                .byteOffset + bytes.byteLength));
                                    }

                                    function makeWavBlob(mono) {
                                        const dataBytes = mono.length * 2,
                                                buf = new ArrayBuffer(44 + dataBytes),
                                                v = new DataView(buf);

                                        function str(o, s) {
                                            for (let i = 0; i < s.length; i++)
                                                v.setUint8(o + i, s.charCodeAt(i));
                                        }
                                        str(0, 'RIFF');
                                        v.setUint32(4, 36 + dataBytes, true);
                                        str(8, 'WAVE');
                                        str(12, 'fmt ');
                                        v.setUint32(16, 16, true);
                                        v.setUint16(20, 1, true);
                                        v.setUint16(22, 1, true);
                                        v.setUint32(24, 16000, true);
                                        v.setUint32(28, 32000, true);
                                        v.setUint16(32, 2, true);
                                        v.setUint16(34, 16, true);
                                        str(36, 'data');
                                        v.setUint32(40, dataBytes, true);
                                        new Int16Array(buf, 44).set(mono);
                                        return new Blob([buf], {
                                            type: 'audio/wav'
                                        });
                                    }

                                    function fetchGoogleTTS() {
                                        return new Promise((resolve, reject) => {
                                            const raw = document.getElementById('pesanInput').value.trim();
                                            if (!raw)
                                                return reject(new Error('Teks pengumuman masih kosong.'));
                                            if (!GOOGLE_API_KEY || GOOGLE_API_KEY.startsWith('ISI_'))
                                                return reject(new Error(
                                                        'Isi GOOGLE_API_KEY untuk uji coba, atau arahkan request ke backend HMS.'
                                                        ));
                                            setStatus('Menghubungi Google Cloud TTS...',
                                                    'bg-warning text-dark');
                                            const payload = {
                                                input: {
                                                    ssml: `<speak>${raw}</speak>`
                                                },
                                                voice: {
                                                    languageCode: 'id-ID',
                                                    ssmlGender: document.getElementById('voiceType').value
                                                },
                                                audioConfig: {
                                                    audioEncoding: 'MP3',
                                                    sampleRateHertz: 16000,
                                                    speakingRate: +document.getElementById('speedSelect').value
                                                }
                                            };
                                            $.ajax({
                                                url: `https://texttospeech.googleapis.com/v1/text:synthesize?key=${encodeURIComponent(GOOGLE_API_KEY)}`,
                                                method: 'POST',
                                                contentType: 'application/json',
                                                data: JSON.stringify(payload),
                                                success: r => r.audioContent ? resolve(parseLinear16Wav(
                                                            decodeBase64(r.audioContent))) : reject(new Error(
                                                            'Respons TTS tidak berisi audioContent.')),
                                                error: x => reject(new Error(x.responseJSON?.error?.message ||
                                                            'Google TTS gagal.'))
                                            });
                                        });
                                    }

                                    function playTone(ctx, freq, start, duration) {
                                        const osc = ctx.createOscillator(),
                                                gain = ctx.createGain();
                                        osc.type = 'sine';
                                        osc.frequency.setValueAtTime(freq, ctx.currentTime + start);
                                        gain.gain.setValueAtTime(.001, ctx.currentTime + start);
                                        gain.gain.linearRampToValueAtTime(.25, ctx.currentTime + start + .05);
                                        gain.gain.exponentialRampToValueAtTime(.0001, ctx.currentTime + start +
                                                duration);
                                        osc.connect(gain);
                                        gain.connect(ctx.destination);
                                        osc.start(ctx.currentTime + start);
                                        osc.stop(ctx.currentTime + start + duration);
                                    }

                                    function chimeMono(opening) {
                                        if (!document.getElementById('useChime').checked)
                                            return new Int16Array(
                                                    0);
                                        const sr = 16000,
                                                freqs = opening ? [523.25, 659.25, 783.99, 1046.5] : [1046.5, 783.99,
                                            659.25, 523.25
                                        ],
                                                dur = .3,
                                                note = Math.floor(sr * dur),
                                                out = new Int16Array(note * 4 + Math.floor(sr * .3));
                                        for (let n = 0; n < 4; n++)
                                            for (let i = 0; i < note; i++) {
                                                const env = Math.exp(-3 * i / note);
                                                out[n * note + i] = Math.round(Math.sin(2 * Math.PI * freqs[n] * i /
                                                        sr) * env * 12000);
                                            }
                                        return out;
                                    }

                                    function monoToStereoBytes(mono) {
                                        const out = new Int16Array(mono.length * 2);
                                        for (let i = 0; i < mono.length; i++) {
                                            const s = Math.round(mono[i] * .6);
                                            out[i * 2] = s;
                                            out[i * 2 + 1] = s;
                                        }
                                        return new Uint8Array(out.buffer);
                                    }

                                    function silenceBytes(ms) {
                                        return new Uint8Array(ms * 64);
                                    }

                                    function buildMasterBuffer(ttsMono) {
                                        const rep = +document.getElementById('repetition').value,
                                                use = document.getElementById('useChime').checked,
                                                parts = [];
                                        if (use) {
                                            parts.push(monoToStereoBytes(chimeMono(true)), silenceBytes(1000));
                                        }
                                        const tts = monoToStereoBytes(ttsMono);
                                        for (let i = 0; i < rep; i++) {
                                            parts.push(tts);
                                            if (i < rep - 1)
                                                parts.push(silenceBytes(1500));
                                        }
                                        if (use)
                                            parts.push(silenceBytes(1000), monoToStereoBytes(chimeMono(
                                                    false)));
                                        parts.push(silenceBytes(500));
                                        const total = parts.reduce((n, p) => n + p.length, 0),
                                                master = new Uint8Array(total);
                                        let off = 0;
                                        for (const p of parts) {
                                            master.set(p, off);
                                            off += p.length;
                                        }
                                        return master;
                                    }

                                    function previewSound() {
                                        if (currentAudio) {
                                            currentAudio.pause();
                                            URL.revokeObjectURL(currentAudio.src);
                                            currentAudio = null;
                                        }
                                        fetchGoogleTTS().then(mono => {
                                            const url = URL.createObjectURL(makeWavBlob(mono)),
                                                    rep = +document.getElementById('repetition').value;
                                            let count = 0;
                                            const play = () => {
                                                if (count >= rep) {
                                                    setStatus('Preview selesai.', 'bg-secondary');
                                                    URL.revokeObjectURL(url);
                                                    return;
                                                }
                                                count++;
                                                currentAudio = new Audio(url);
                                                currentAudio.onended = () => setTimeout(play, 1000);
                                                currentAudio.play().catch(e => setStatus('Preview gagal: ' + e
                                                            .message, 'bg-danger'));
                                            };
                                            playOpeningPreview(play);
                                        }).catch(e => {
                                            alert(e.message);
                                            setStatus(e.message, 'bg-danger');
                                        });
                                    }

                                    function playOpeningPreview(next) {
                                        if (!document.getElementById('useChime').checked)
                                            return next();
                                        try {
                                            const c = new (window.AudioContext || window.webkitAudioContext)();
                                            [523.25, 659.25, 783.99, 1046.5].forEach((f, i) => playTone(c, f, i *
                                                        .3, 1.5));
                                            setTimeout(next, 2200);
                                        } catch (e) {
                                            next();
                                        }
                                    }

                                    function setBusy(b) {
                                        document.getElementById('broadcastButton').disabled = b;
                                        document.getElementById('stopButton').disabled = !b;
                                    }

                                    function closeSocket() {
                                        if (wsESP32) {
                                            try {
                                                wsESP32.close();
                                            } catch (e) {
                                            }
                                            wsESP32 = null;
                                        }
                                    }

                                    function stopBroadcast() {
                                        if (wsESP32 && wsESP32.readyState === WebSocket.OPEN)
                                            wsESP32.send(
                                                    'STOP');
                                        closeSocket();
                                        broadcastState = 'IDLE';
                                        setBusy(false);
                                        setStatus('STOP dikirim. Audio buffer software dibersihkan.',
                                                'bg-danger');
                                    }
                                    
                                        async function triggerBroadcast() {
                                        if (broadcastState !== 'IDLE')
                                            return;
                                        const ip = document.getElementById('esp32Ip').value.trim(),
                                                token = document.getElementById('esp32Token').value.trim();
                                        if (!ip || !token) {
                                            alert('IP ESP32 dan token wajib diisi.');
                                            return;
                                        }
                                        setBusy(true);
                                        broadcastState = 'PREPARING';
                                        setProgress(0);
                                        try {
                                            const mono = await fetchGoogleTTS(),
                                                    master = buildMasterBuffer(mono);
                                            if (master.length === 0)
                                                throw new Error('Audio kosong.');
                                            const durationMs = master.length / 64000;
                                            document.getElementById('durationBadge').textContent =
                                                    'Durasi audio: ' + Math.ceil(durationMs) + ' detik';
                                            setStatus('Audio siap: ' + (master.length / 1024).toFixed(1) + ' KB',
                                                    'bg-info text-dark');
                                            //          console.log(master);
                                            //          return;
                                            await streamToESP32(ip, token, master, durationMs);
                                        } catch (e) {
                                            alert(e.message);
                                            setStatus('Gagal: ' + e.message, 'bg-danger');
                                            closeSocket();
                                            broadcastState = 'IDLE';
                                            setBusy(false);
                                        }
                                    }


                                    function streamToESP32(ip, token, master, durationMs) {
                                        return new Promise((resolve, reject) => {
                                            const ws = new WebSocket(`ws://${ip}`);
                                            ws.binaryType = 'arraybuffer';
                                            wsESP32 = ws;

                                            const CHUNK = 8192;
                                            const WINDOW_SIZE = 4;
                                            const ACK_TIMEOUT = 1500;
                                            const MAX_RETRIES = 8;
                                            const BUSY_RETRY_DELAY = 150;

                                            const PACKET_INTERVAL_MS = 128;
                                            let nextPacketSendAt = 0;

                                            const totalChunks = Math.ceil(master.length / CHUNK);
                                            const inflight = new Map();
                                            let nextSeqToSend = 0;
                                            let ackedBytes = 0;
                                            let windowLimit = WINDOW_SIZE;
                                            let recoverySeq = null;
                                            let recoveryTimer = null;
                                            let progressTimer = null;
                                            let progressStart = 0;
                                            let endSent = false;
                                            let finished = false;

                                            function cleanup() {
                                                clearTimeout(recoveryTimer);
                                                clearInterval(progressTimer);

                                                for (const item of inflight.values()) {
                                                    clearTimeout(item.timer);
                                                    clearTimeout(item.sendTimer);
                                                }

                                                inflight.clear();

                                                if (ws.readyState === WebSocket.OPEN) {
                                                    ws.close();
                                                }

                                                if (wsESP32 === ws)
                                                    wsESP32 = null;
                                            }

                                            function fail(message) {
                                                if (finished)
                                                    return;

                                                finished = true;
                                                cleanup();

                                                broadcastState = 'IDLE';
                                                setBusy(false);
                                                reject(new Error(message));
                                            }

                                            function makeFrame(seq) {
                                                const offset = seq * CHUNK;
                                                const end = Math.min(offset + CHUNK, master.length);

                                                const pcm = master.subarray(offset, end);
                                                const alignedLength = pcm.length - (pcm.length % 4);

                                                if (alignedLength <= 0) {
                                                    throw new Error('Panjang paket PCM tidak valid.');
                                                }

                                                const body = pcm.subarray(0, alignedLength);
                                                const frame = new Uint8Array(4 + body.length);

                                                new DataView(frame.buffer).setUint32(0, seq, true);
                                                frame.set(body, 4);

                                                return {
                                                    seq,
                                                    frame,
                                                    bytes: body.length,
                                                    retries: 0,
                                                    timer: null
                                                };
                                            }

                                            function armTimeout(item) {
                                                clearTimeout(item.timer);

                                                item.timer = setTimeout(() => {
                                                    if (finished || !inflight.has(item.seq))
                                                        return;

                                                    if (item.retries >= MAX_RETRIES) {
                                                        fail(
                                                                'ACK timeout pada paket ' + item.seq +
                                                                '. Periksa koneksi WiFi ESP32.'
                                                                );
                                                        return;
                                                    }

                                                    item.retries++;

                                                    setStatus(
                                                            'ACK timeout, retry paket ' + item.seq +
                                                            ' (' + item.retries + '/' + MAX_RETRIES + ')',
                                                            'bg-warning text-dark'
                                                            );

                                                    try {
                                                        ws.send(item.frame);
                                                        armTimeout(item);
                                                    } catch (e) {
                                                        fail('Gagal mengirim ulang paket: ' + e.message);
                                                    }
                                                }, ACK_TIMEOUT);
                                            }

                                            function sendPacket(seq) {
                                                let item;

                                                try {
                                                    item = makeFrame(seq);
                                                    item.sendTimer = null;
                                                    chunks.push(item.frame);
                                                    inflight.set(seq, item);

                                                    const now = performance.now();
                                                    const sendAt = Math.max(now, nextPacketSendAt);

                                                    nextPacketSendAt = sendAt + PACKET_INTERVAL_MS;

                                                    const delay = Math.max(0, sendAt - now);

                                                    item.sendTimer = setTimeout(() => {
                                                        item.sendTimer = null;

                                                        if (finished || !inflight.has(seq))
                                                            return;

                                                        if (ws.readyState !== WebSocket.OPEN) {
                                                            fail('WebSocket terputus saat mengirim paket.');
                                                            return;
                                                        }

                                                        try {
                                                            ws.send(item.frame);

                                                            armTimeout(item);
                                                        } catch (e) {
                                                            fail('Gagal mengirim paket: ' + e.message);
                                                        }
                                                    }, delay);

                                                } catch (e) {
                                                    if (item) {
                                                        clearTimeout(item.sendTimer);
                                                        inflight.delete(seq);
                                                    }

                                                    fail('Pengiriman paket gagal: ' + e.message);
                                                }
                                            }

                                            function updateProgress() {
                                                const percent = Math.min(
                                                        99,
                                                        (ackedBytes / master.length) * 100
                                                        );

                                                setProgress(percent);
                                            }

                                            const chunks = [];

                                            function fillWindow() {
                                                if (finished || endSent)
                                                    return;
                                                if (ws.readyState !== WebSocket.OPEN)
                                                    return;
                                                if (recoveryTimer !== null)
                                                    return;

                                                while (
                                                        nextSeqToSend < totalChunks &&
                                                        inflight.size < windowLimit
                                                        ) {
                                                    const seq = nextSeqToSend++;

                                                    sendPacket(seq);

                                                    if (finished)
                                                        return;
                                                }

                                                if (
                                                        nextSeqToSend >= totalChunks &&
                                                        inflight.size === 0 &&
                                                        !endSent
                                                        ) {
                                                    endSent = true;
                                                    broadcastState = 'DRAINING';

                                                    ws.send('END');

                                                    setStatus(
                                                            'Semua paket diterima ESP32. Menunggu audio selesai diputar...',
                                                            'bg-info text-dark'
                                                            );
                                                }
                                            }

                                            function clearFromSequence(expected) {
                                                for (const [seq, item] of inflight.entries()) {
                                                    if (seq >= expected) {
                                                        clearTimeout(item.timer);
                                                        clearTimeout(item.sendTimer);
                                                        inflight.delete(seq);
                                                    }
                                                }

                                                nextSeqToSend = expected;
                                                nextPacketSendAt = performance.now();
                                                recoverySeq = expected;
                                                windowLimit = 1;

                                                clearTimeout(recoveryTimer);
                                                recoveryTimer = setTimeout(() => {
                                                    recoveryTimer = null;
                                                    fillWindow();
                                                }, BUSY_RETRY_DELAY);
                                            }

                                            function handleAck(seq) {
                                                const item = inflight.get(seq);

                                                // ACK duplikat atau ACK paket yang sudah diproses: abaikan.
                                                if (!item)
                                                    return;

                                                clearTimeout(item.timer);
                                                inflight.delete(seq);

                                                ackedBytes += item.bytes;
                                                updateProgress();

                                                // Setelah paket pemulihan diterima, kembali ke window 4.
                                                if (recoverySeq === seq) {
                                                    recoverySeq = null;
                                                    windowLimit = WINDOW_SIZE;
                                                }

                                                fillWindow();
                                            }

                                            function handleBusy(seq) {
                                                if (seq < 0 || seq >= totalChunks) {
                                                    fail('Sequence BUSY tidak valid: ' + seq);
                                                    return;
                                                }

                                                setStatus(
                                                        'Buffer ESP32 penuh. Menunggu ruang untuk paket ' + seq + '...',
                                                        'bg-warning text-dark'
                                                        );

                                                // ESP32 belum menerima paket ini.
                                                // Hentikan pengiriman paket berikutnya dan ulangi sequence ini.
                                                clearFromSequence(seq);
                                            }

                                            function handleNack(expected) {
                                                if (expected < 0 || expected >= totalChunks) {
                                                    fail('Sequence NACK tidak valid: ' + expected);
                                                    return;
                                                }

                                                setStatus(
                                                        'ESP32 meminta ulang paket mulai sequence ' + expected,
                                                        'bg-warning text-dark'
                                                        );

                                                // Sinkronkan kembali browser dengan sequence ESP32.
                                                clearFromSequence(expected);
                                            }

                                            function concatUint8Arrays(arrays) {
                                                // 1. Calculate total length
                                                const totalLength = arrays.reduce((acc, arr) => acc + arr.length, 0);

                                                // 2. Create a new fixed-length array
                                                const result = new Uint8Array(totalLength);

                                                // 3. Copy each array into the result
                                                let offset = 0;
                                                for (const arr of arrays) {
                                                    result.set(arr, offset);
                                                    offset += arr.length;
                                                }

                                                return result;
                                            }

                                            function _download(audioData) {
                                                const blob = new Blob([audioData], {type: 'audio/wav'});
                                                const url = URL.createObjectURL(blob);
                                                const a = document.createElement('a');
                                                a.href = url;
                                                a.download = 'my-audio-file.mp3'; // The name the file will save as
                                                document.body.appendChild(a);
                                                a.click();

// Clean up memory
                                                document.body.removeChild(a);
                                                URL.revokeObjectURL(url);

                                            }

                                            ws.onopen = () => {
                                                setStatus(
                                                        'WebSocket terhubung, autentikasi...',
                                                        'bg-warning text-dark'
                                                        );

                                                ws.send('AUTH:' + token);
                                            };

                                            ws.onmessage = e => {
                                                const msg = String(e.data);

                                                if (msg === 'AUTH_OK') {
                                                    ws.send('START');
                                                    return;
                                                }

                                                if (msg === 'READY') {
                                                    broadcastState = 'STREAMING';
                                                    progressStart = Date.now();

                                                    setStatus(
                                                            'ESP32 siap. Mengirim audio dengan sliding window 4 paket...',
                                                            'bg-success'
                                                            );

                                                    progressTimer = setInterval(() => {
                                                        // Progress utama dihitung dari ACK, bukan waktu.
                                                        updateProgress();
                                                    }, 200);

                                                    fillWindow();
                                                    return;
                                                }

                                                if (msg.startsWith('ACK:')) {
                                                    const seq = Number(msg.substring(4));

                                                    if (Number.isInteger(seq)) {
                                                        handleAck(seq);
                                                    }

                                                    return;
                                                }

                                                if (msg.startsWith('BUSY:')) {
                                                    const seq = Number(msg.substring(5));

                                                    if (Number.isInteger(seq)) {
                                                        handleBusy(seq);
                                                    }

                                                    return;
                                                }

                                                if (msg.startsWith('NACK:')) {
                                                    const expected = Number(msg.substring(5));

                                                    if (Number.isInteger(expected)) {
                                                        handleNack(expected);
                                                    }

                                                    return;
                                                }

                                                if (msg === 'DRAINING') {
                                                    broadcastState = 'DRAINING';
                                                    return;
                                                }

                                                if (msg === 'FINISHED') {
                                                    finished = true;
                                                    cleanup();

                                                    setProgress(100);

                                                    broadcastState = 'IDLE';
                                                    setBusy(false);

                                                    setStatus(
                                                            'Broadcast selesai. Audio telah selesai diproses ESP32.',
                                                            'bg-primary'
                                                            );
//                                                    _download(concatUint8Arrays(chunks));
                                                    resolve();
                                                    return;
                                                }

                                                if (msg === 'STOPPED') {
                                                    setStatus('Broadcast dihentikan.', 'bg-danger');
                                                    return;
                                                }

                                                if (msg === 'AUTH_FAIL') {
                                                    fail('Token ESP32 salah.');
                                                    return;
                                                }

                                                if (msg.startsWith('ERROR:')) {
                                                    fail('ESP32: ' + msg);
                                                }
                                            };

                                            ws.onerror = () => {
                                                fail(
                                                        'Gagal terhubung ke ESP32. Periksa IP, WiFi, dan port 8080.'
                                                        );
                                            };

                                            ws.onclose = () => {
                                                if (!finished && broadcastState !== 'IDLE') {
                                                    fail(
                                                            'WebSocket terputus sebelum ESP32 mengirim FINISHED.'
                                                            );
                                                }
                                            };
                                        });
                                    }

                                    $(document).ready(() => hitungDurasiTotal());
        </script>
    </body>

</html>