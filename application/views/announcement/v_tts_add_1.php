<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            :root {
                --bg-app: #f8fafc;
                --card-main: #ffffff;
                --border-color: #e2e8f0;
                --text-main: #0f172a;
                --primary-blue: #0284c7;
                --card-light: #ffffff;
                --header-height: 85px;
                --table-header-height: 55px;
                --accent-blue: #0d6efd;
                --muted-text: #6c757d;
            }
            .d-flex {
                display: flex !important;
            }

            .justify-content-between {
                justify-content-content: space-between !important;
            }
            .gap-3 {
                gap: 1rem !important;
            }
            .menu-drag {
                border-radius: 14px;
                padding: 0 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            }
            .draggable {
                cursor: -webkit-grab;
                cursor: grab;
            }

            .draggable:active, .dragging {
                cursor: -webkit-grabbing;
                cursor: grabbing;
            }

            legend {
                font-size: 15px;
            }
            #systemStatus{
                font-size: 1rem;
            }

            .d-flex-wrap-bs3 {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                padding: 10px;
                /* PENTING: Membuat item otomatis turun ke bawah saat penuh */
                -ms-flex-wrap: wrap;
                flex-wrap: wrap;

                /* Mengatur jarak antar item agar rapi (Gap) */
                gap: 15px;
            }

            /* Mengatur ukuran & responsivitas item di dalamnya */
            .d-flex-wrap-bs3 .item-drag {
                text-align: center;

                /* Opsi A: Ukuran item fleksibel mengikuti teks di dalamnya */
                -ms-flex-preferred-size: auto;
                flex-basis: auto;

                /* Opsi B: Buka komentar di bawah ini jika ingin membuat grid tetap (misal: 4 item per baris) */
                /* 
                width: calc(25% - 12px); 
                */
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu-new.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar-new.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar-new.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <?php $this->load->view("admin/_partials/statusbar-new.php") ?>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Tambah</h3>
                        </div>
                        <div class="box-body">
                            <div class="row align-items-center">
                                <div class="menu-drag col-md-4 border-end border-light">
                                    <div class="d-flex-wrap-bs3" >
                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text="" data-format='<say-as interpret-as="characters">{text}</say-as>' >
                                            Interpret Characters
                                        </button>
<!--                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text="" data-format='<say-as interpret-as="cardinal">{text}</say-as>' >
                                            Bilangan Suara
                                        </button>-->
                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text="" data-format='<say-as interpret-as="digits">{text}</say-as>' >
                                            Interpret Digits
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text='<break time="1s"/>' draggable="true" >Jeda Suara 1detik</button>

                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text='<break time="3s"/>' draggable="true" >Jeda Suara 3detik</button>

                                    </div>
                                </div>


                                <div class="menu-drag col-md-8 border-end border-light">
                                    <form class="form-horizontal" name="form-tts" id="form-tts" method="POST" action="<?= base_url("announcement/tts/save") ?>">
                                        <div class="field-group">
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <textarea id="myTextarea" name="text_pesan" rows="5" cols="40" style="width: 100%" class="form-control"></textarea>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">Pengulangan</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm" name="perulangan" id="perulangan">
                                                            <option value="1">1x (Sekali)</option>
                                                            <option value="3">3x (Tiga kali)</option>
                                                            <option value="5">5x (Lima kali)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--                                            <div class="form-group">
                                                                                            <div class="col-xs-12">
                                                                                                <div class="col-xs-4"><label class="form-label">Display Time</label></div>
                                                                                                <div class="col-xs-8 col-md-8">
                                                                                                    <select class="form-control input-sm" name="display" id="display">
                                                                                                        <option value="15">15 Detik</option>
                                                                                                        <option value="30">30 Detik</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>-->
                                            <input type="hidden" name="display" id="display" value="0">
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">Jenis Suara</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm" name="jenis_suara" id="jenis_suara">
                                                            <option value="male">Laki - Laki</option>
                                                            <option value="female">Perempuan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">Kecepatan Bicara</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm" name="speed_suara" id="speed_suara">
                                                            <option value="normal">Normal</option>
                                                            <option value="slow">Slow / Lambat</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">Efek Lonceng </label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <label>
                                                            <input type="checkbox" class="form-label" value="1" name="efek" id="efek" checked> Use Opening & Closing
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <span class="badge bg-light text-primary" id="systemStatus">Siap</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between">
                                                    <label class="form-label small text-muted" id="progressLabel">Progres Transfer / Preview:</label>
                                                    <span class="small fw-bold text-primary" id="progressPercent">&nbsp;0%</span>
                                                </div>
                                                <div class="progress" style="height: 12px;">
                                                    <div id="broadcastProgress" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-xs-6">
                                                    <button type="button" id="btnPreview" class="btn btn-default btn-sm" onClick="previewSound();">Preview Sound</button>
                                                </div>
                                                <div class="col-xs-6">
                                                    <button type="submit" id="btnBroadcast" class="btn btn-success btn-sm">Mulai Broadcast</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>


            //TTS Google
            let currentAudio = null;
            const GOOGLE_API_KEY = "AIzaSyAbT_luJzC1H08Zf8Kh-6QiWgP9PlJyKLk";


            function playOpeningChime(callback) {
                if (!document.getElementById('efek').checked) {
                    if (callback)
                        callback();
                    return;
                }
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

                    playTone(audioCtx, 523.25, 0.0, 1.5); // C5
                    playTone(audioCtx, 659.25, 0.3, 1.5); // E5
                    playTone(audioCtx, 783.99, 0.6, 1.8); // G5
                    playTone(audioCtx, 1046.50, 0.9, 2.2); // C6

                    setTimeout(() => {
                        if (callback)
                            callback();
                    }, 2200);
                } catch (e) {
                    if (callback)
                        callback();
                }
            }
            function playClosingChime(callback) {
                if (!document.getElementById('efek').checked) {
                    if (callback)
                        callback();
                    return;
                }
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

                    playTone(audioCtx, 1046.50, 0.0, 1.5); // C6
                    playTone(audioCtx, 783.99, 0.3, 1.5); // G5
                    playTone(audioCtx, 659.25, 0.6, 1.5); // E5
                    playTone(audioCtx, 523.25, 0.9, 2.0); // C5

                    setTimeout(() => {
                        if (callback)
                            callback();
                    }, 2200);
                } catch (e) {
                    if (callback)
                        callback();
                }
            }

            function playTone(audioCtx, frequency, startTime, duration) {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(frequency, audioCtx.currentTime + startTime);

                gain.gain.setValueAtTime(0.001, audioCtx.currentTime + startTime);
                gain.gain.linearRampToValueAtTime(0.25, audioCtx.currentTime + startTime + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + startTime + duration);

                osc.connect(gain);
                gain.connect(audioCtx.destination);

                osc.start(audioCtx.currentTime + startTime);
                osc.stop(audioCtx.currentTime + startTime + duration);
            }

            // Helper: Menambahkan Header WAV standar agar PC bisa memutar Preview
            function createWavBlobFromPcm(base64Pcm) {
                const binaryString = window.atob(base64Pcm);
                const len = binaryString.length;
                const bytes = new Uint8Array(len);
                for (let i = 0; i < len; i++) {
                    bytes[i] = binaryString.charCodeAt(i);
                }
                return new Blob([bytes.buffer], {type: 'audio/wav'});
            }


            function fetchGoogleTTS(callback) {
                const rawText = $('#myTextarea').val();
                const gender = $('#jenis_suara').val();
                const speed = parseFloat($('#speed_suara').val());

                if (!rawText.trim()) {
//                    alert_notify('fa fa-close', "Teks tidak boleh kosong!", 'danger', function () {});
                    callback(null);
                    return;
                }

                if (GOOGLE_API_KEY === "API_KEY") {
                    alert_notify('fa fa-close', "Harap masukkan Google Cloud API Key Anda pada variabel GOOGLE_API_KEY.", 'danger', function () {});
                    return;

                }

                const endpoint = `https://texttospeech.googleapis.com/v1/text:synthesize?key=${GOOGLE_API_KEY}`;
                const ssmlContent = `<speak>${rawText}</speak>`;

                const payload = {
                    input: {ssml: ssmlContent},
                    voice: {languageCode: 'id-ID',
                        ssmlGender: gender,
                        name: (gender === 'male') ? 'id-ID-Standard-C' : 'id-ID-Standard-A'},
                    audioConfig: {
                        audioEncoding: 'LINEAR16',
                        sampleRateHertz: 16000,
                        speakingRate: speed
                    }
                };
                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function (response) {
                        if (response.audioContent) {
                            updateStatus('Siap Diputar', 'bg-secondary');

                            if (callback)
                                callback(response.audioContent);
                        } else {
                            alert_notify('fa fa-close', "Gagal mendapatkan respons audio dari Google.", 'danger', function () {});
//                            alert('Gagal mendapatkan respons audio dari Google.');
//                            updateStatus('Error', 'bg-danger');
                        }
                    },
                    error: function (xhr) {
                        alert_notify('fa fa-close', 'Error Google API: ' + (xhr.responseJSON?.error?.message || 'Terjadi kesalahan'), 'danger', function () {});
//                        alert('Error Google API: ' + (xhr.responseJSON?.error?.message || 'Terjadi kesalahan'));
//                        updateStatus('Error', 'bg-danger');
                    }
                }
                );
            }

            function updateStatus(text, bgClass) {
                $('#systemStatus').attr('class', 'badge ' + bgClass).text(text);
            }
            function previewSound() {
                if (currentAudio)
                    currentAudio.pause();
                const minDisplayTime = parseInt($('#display').val()) * 1000;
                const rawText = $('#myTextarea').val();
                if (rawText.trim() === "")
                    alert_notify('fa fa-close', "Teks tidak boleh kosong!", 'danger', function () {});
                fetchGoogleTTS(function (base64Pcm) {
                    const wavBlob = createWavBlobFromPcm(base64Pcm);
                    const audioUrl = URL.createObjectURL(wavBlob);
                    const repetition = parseInt($('#perulangan').val());
                    let currentCount = 0;

                    playOpeningChime(() => {
                        function loop() {
                            if (currentCount >= repetition) {
                                playClosingChime(() => updateStatus('Preview Selesai', 'bg-secondary'));
                                setButtonsDisabled(false);
                                return;
                            }
                            currentCount++;
                            currentAudio = new Audio(audioUrl);
                            currentAudio.play();
                            currentAudio.onended = () => setTimeout(loop, 1000);
                        }
                        loop();
                    });
                });

            }

            function setProgress(percent, labelText) {
                percent = Math.max(0, Math.min(100, percent));

                $('#broadcastProgress').css(
                        'width',
                        percent + '%'
                        );

                $('#progressPercent').text(` ${percent}%`);

                if (labelText) {
                    $('#progressLabel').text(labelText);
                }
            }

            let activeTimer = null;
            function triggerBroadcast() {

                const espIp = "<?= $ip_esp ?>";
                const espToken = "<?= $token_esp ?>";
                const useChime = document.getElementById('efek').checked;
                const rawText = $('#myTextarea').val();

                if (!espIp) {
                    alert_notify('fa fa-close', "Alamat ESP tidak ditemukan", 'danger', function () {});
                    return;
                }

                setButtonsDisabled(true);
                fetchGoogleTTS(function (base64Audio) {
                    let ws = null;
                    let masterBuffer = null;

                    let currentOffset = 0;
                    let waitingWindowAck = false;
                    let windowExpectedBytes = 0;

                    let stopSent = false;
                    let completed = false;
                    let failed = false;

                    let ackTimer = null;
                    let stage = 'CONNECTING';

                    // Konfigurasi transfer
                    const CHUNK_SIZE = 4096;
                    const WINDOW_PACKETS = 8;
                    const ACK_TIMEOUT_MS = 15000;

                    function failBroadcast(message) {
                        if (failed || completed)
                            return;

                        failed = true;
                        clearTimeout(ackTimer);

                        console.error('[PA ERROR]', message);
                        console.error('[PA STAGE]', stage);
                        console.error('[PA OFFSET]', currentOffset);

                        updateStatus('Broadcast gagal', 'bg-danger');

                        setProgress(
                                masterBuffer
                                ? Math.round(
                                        currentOffset / masterBuffer.byteLength * 100
                                        )
                                : 0,
                                message
                                );

                        alert(message);
                        setButtonsDisabled(false);

                        if (ws && ws.readyState < WebSocket.CLOSING) {
                            ws.close();
                        }
                    }

                    // =========================================
                    // KIRIM 1 WINDOW = MAKSIMAL 8 PAKET
                    // =========================================

                    function sendWindow() {
                        if (failed || completed || waitingWindowAck || stopSent) {
                            return;
                        }

                        if (!ws || ws.readyState !== WebSocket.OPEN) {
                            failBroadcast('WebSocket ESP32 terputus.');
                            return;
                        }

                        // Seluruh data sudah mendapatkan ACK.
                        if (currentOffset >= masterBuffer.byteLength) {
                            stopSent = true;
                            stage = 'WAIT_PLAYBACK';

                            updateStatus(
                                    'Upload selesai. Memutar audio...',
                                    'bg-primary'
                                    );

                            setProgress(
                                    100,
                                    'Data lengkap. Menunggu playback ESP32...'
                                    );

                            ws.send('STOP');
                            return;
                        }

                        let packetCount = 0;
                        windowExpectedBytes = 0;

                        waitingWindowAck = true;
                        stage = 'WAIT_WINDOW_ACK';

                        // Kirim maksimal 8 paket tanpa menunggu ACK satu per satu.
                        while (
                                packetCount < WINDOW_PACKETS &&
                                currentOffset < masterBuffer.byteLength
                                ) {
                            const endOffset = Math.min(
                                    currentOffset + CHUNK_SIZE,
                                    masterBuffer.byteLength
                                    );

                            const chunk = masterBuffer.subarray(
                                    currentOffset,
                                    endOffset
                                    );

                            ws.send(chunk);

                            const sentBytes = endOffset - currentOffset;

                            currentOffset = endOffset;
                            windowExpectedBytes += sentBytes;
                            packetCount++;
                        }

                        const percent = Math.round(
                                currentOffset / masterBuffer.byteLength * 100
                                );

                        setProgress(
                                percent,
                                `Mengirim: ${(currentOffset / 1000).toLocaleString()} / ` +
                                `${(masterBuffer.byteLength / 1000).toLocaleString()} KB ` +
                                `| Window: ${packetCount} paket`
                                );

                        console.log(
                                '[PA TX] Window:',
                                packetCount,
                                'paket |',
                                windowExpectedBytes,
                                'byte'
                                );

                        // Jika paket terakhir kurang dari 8,
                        // minta ESP32 mengirim ACK untuk sisa paket.
                        if (packetCount < WINDOW_PACKETS) {
                            ws.send('WINDOW_END');
                        }

                        clearTimeout(ackTimer);

                        ackTimer = setTimeout(() => {
                            if (waitingWindowAck && !failed && !completed) {
                                failBroadcast(
                                        'Timeout menunggu ACK window dari ESP32. ' +
                                        'Transfer dihentikan.'
                                        );
                            }
                        }, ACK_TIMEOUT_MS);
                    }

                    // =========================================
                    // PERSIAPAN AUDIO
                    // =========================================

                    try {
                        updateStatus(
                                'Menyiapkan audio broadcast...',
                                'bg-warning text-dark'
                                );

                        masterBuffer = createMasterAudio(base64Audio);

                        console.log('[PA] Total audio:', masterBuffer.byteLength);
                        console.log('[PA] Chunk:', CHUNK_SIZE);
                        console.log('[PA] Window:', WINDOW_PACKETS);

                    } catch (error) {
                        console.error(error);

                        alert('Gagal memproses audio: ' + error.message);

                        updateStatus('Format audio error', 'bg-danger');
                        setButtonsDisabled(false);
                        return;
                    }

                    // =========================================
                    // WEBSOCKET
                    // =========================================

                    try {
                        stage = 'CONNECTING';

                        updateStatus(
                                'Menghubungkan ESP32...',
                                'bg-warning text-dark'
                                );

                        ws = new WebSocket(`ws://${espIp}`);
                        ws.binaryType = 'arraybuffer';

                        wsESP32 = ws;

                    } catch (error) {
                        failBroadcast('Tidak dapat membuat koneksi WebSocket.');
                        return;
                    }

                    // =========================================
                    // OPEN
                    // =========================================

                    ws.onopen = function () {
                        stage = 'AUTH';

                        console.log('[PA] WebSocket connected.');

                        updateStatus('Otentikasi ESP32...', 'bg-warning text-dark');

                        ws.send('AUTH:' + espToken);
                    };

                    // =========================================
                    // MESSAGE
                    // =========================================

                    ws.onmessage = function (event) {
                        const msg = event.data;

                        console.log('[PA RX]', msg);

                        if (msg === 'AUTH_OK') {
                            stage = 'START';

                            updateStatus('Menyiapkan MicroSD...', 'bg-info text-dark');

                            ws.send('START');
                            return;
                        }

                        if (msg === 'RECORDING_READY') {
                            stage = 'TRANSFER';

                            currentOffset = 0;
                            waitingWindowAck = false;

                            updateStatus(
                                    'Mengirim audio ke MicroSD...',
                                    'bg-info text-dark'
                                    );

                            sendWindow();
                            return;
                        }

                        // =====================================
                        // ACK WINDOW
                        // =====================================

                        if (typeof msg === 'string' &&
                                msg.startsWith('ACK_WINDOW:')) {

                            if (!waitingWindowAck) {
                                console.warn('[PA] ACK window tidak terduga.');
                                return;
                            }

                            const confirmedBytes = parseInt(
                                    msg.substring('ACK_WINDOW:'.length),
                                    10
                                    );

                            clearTimeout(ackTimer);

                            if (confirmedBytes !== windowExpectedBytes) {
                                failBroadcast(
                                        `Konfirmasi data tidak cocok. ` +
                                        `Diharapkan ${windowExpectedBytes} byte, ` +
                                        `diterima ${confirmedBytes} byte.`
                                        );
                                return;
                            }

                            console.log(
                                    '[PA] Window confirmed:',
                                    confirmedBytes,
                                    'byte'
                                    );

                            waitingWindowAck = false;
                            windowExpectedBytes = 0;

                            sendWindow();
                            return;
                        }

                        if (msg === 'PLAYING_DONE') {
                            completed = true;
                            stage = 'DONE';

                            clearTimeout(ackTimer);

                            updateStatus(
                                    'Siaran Selesai Diputar di TOA',
                                    'bg-success'
                                    );

                            setProgress(100, 'Siaran berhasil diputar.');
                            alert_notify('fa fa-check', "Siap Diputar", 'success', function () {});
                            setButtonsDisabled(false);

                            if (ws.readyState === WebSocket.OPEN) {
                                ws.close();
                            }

                            return;
                        }

                        if (msg === 'AUTH_FAIL') {
                            failBroadcast('Token autentikasi ESP32 salah.');
                            return;
                        }

                        if (msg === 'SD_ERROR' || msg === 'SD_WRITE_ERR') {
                            failBroadcast('ESP32 melaporkan kesalahan MicroSD.');
                            return;
                        }

                        if (msg === 'FILE_INVALID') {
                            failBroadcast(
                                    'Ukuran file MicroSD tidak valid.'
                                    );
                            return;
                        }

                        if (msg === 'PLAYBACK_ERROR') {
                            failBroadcast(
                                    'Audio tersimpan tetapi playback ESP32 gagal.'
                                    );
                            return;
                        }

                        if (msg === 'BUSY') {
                            failBroadcast('ESP32 sedang digunakan.');
                            return;
                        }

                        if (msg === 'NOT_RECORDING') {
                            failBroadcast('ESP32 tidak sedang recording.');
                            return;
                        }

                        if (msg === 'WINDOW_PENDING') {
                            failBroadcast(
                                    'ESP32 masih memiliki paket yang belum dikonfirmasi.'
                                    );
                            return;
                        }
                    };

                    // =========================================
                    // ERROR
                    // =========================================

                    ws.onerror = function (error) {
                        console.error('[PA WS ERROR]', error);

                        if (!completed) {
                            failBroadcast(
                                    'WebSocket error. Periksa IP ESP32 dan jaringan.'
                                    );
                        }
                    };

                    // =========================================
                    // CLOSE
                    // =========================================

                    ws.onclose = function (event) {
                        clearTimeout(ackTimer);

                        console.log(
                                '[PA] WebSocket closed:',
                                event.code,
                                event.reason
                                );

                        if (!completed && !failed) {
                            failBroadcast(
                                    `ESP32 terputus. Tahap: ${stage}. ` +
                                    `Terkirim: ${currentOffset.toLocaleString()} byte. ` +
                                    `Kode: ${event.code}.`
                                    );
                        }

                        setButtonsDisabled(false);
                    };
                });
            }

            function base64ToBytes(base64) {
                const binary = atob(base64);
                const bytes = new Uint8Array(binary.length);

                for (let i = 0; i < binary.length; i++) {
                    bytes[i] = binary.charCodeAt(i);
                }

                return bytes;
            }

            function decodeGoogleLinear16Wav(base64Audio) {
                const bytes = base64ToBytes(base64Audio);

                if (bytes.length < 44) {
                    throw new Error(
                            `Audio WAV terlalu kecil: ${bytes.length} byte`
                            );
                }

                const view = new DataView(bytes.buffer);

                function fourCC(offset) {
                    return String.fromCharCode(
                            bytes[offset],
                            bytes[offset + 1],
                            bytes[offset + 2],
                            bytes[offset + 3]
                            );
                }

                if (fourCC(0) !== 'RIFF' || fourCC(8) !== 'WAVE') {
                    throw new Error('Format WAV tidak valid.');
                }

                let offset = 12;
                let audioFormat = 0;
                let channels = 0;
                let sampleRate = 0;
                let bitsPerSample = 0;
                let dataOffset = -1;
                let dataLength = 0;

                while (offset + 8 <= bytes.length) {
                    const chunkId = fourCC(offset);
                    const chunkSize = view.getUint32(offset + 4, true);
                    const chunkData = offset + 8;

                    if (chunkData + chunkSize > bytes.length) {
                        throw new Error('Struktur WAV terpotong.');
                    }

                    if (chunkId === 'fmt ') {
                        if (chunkSize < 16) {
                            throw new Error('Format WAV tidak lengkap.');
                        }

                        audioFormat = view.getUint16(chunkData, true);
                        channels = view.getUint16(chunkData + 2, true);
                        sampleRate = view.getUint32(chunkData + 4, true);
                        bitsPerSample = view.getUint16(chunkData + 14, true);

                    } else if (chunkId === 'data') {
                        dataOffset = chunkData;
                        dataLength = chunkSize;
                        break;
                    }

                    offset = chunkData + chunkSize + (chunkSize & 1);
                }

                if (dataOffset < 0 || dataLength <= 0) {
                    throw new Error('Data PCM tidak ditemukan.');
                }

                if (audioFormat !== 1) {
                    throw new Error('Audio bukan PCM integer.');
                }

                if (sampleRate !== 16000) {
                    throw new Error(
                            `Sample rate ${sampleRate} Hz, harus 16000 Hz.`
                            );
                }

                if (bitsPerSample !== 16) {
                    throw new Error(
                            `Bit depth ${bitsPerSample}, harus 16-bit.`
                            );
                }

                if (channels !== 1 && channels !== 2) {
                    throw new Error(
                            `Channel tidak didukung: ${channels}`
                            );
                }

                const blockAlign = channels * 2;
                dataLength -= dataLength % blockAlign;

                const sampleCount = dataLength / 2;
                const pcm = new Int16Array(sampleCount);

                for (let i = 0; i < sampleCount; i++) {
                    pcm[i] = view.getInt16(dataOffset + (i * 2), true);
                }

                return {
                    pcm,
                    channels,
                    sampleRate,
                    bitsPerSample,
                    sourceBytes: bytes.length,
                    pcmBytes: dataLength
                };
            }

            function generateChimeStereoBytes(isOpening, enabled) {
                if (!enabled) {
                    return new Uint8Array(0);
                }

                const sampleRate = 16000;

                const frequencies = isOpening
                        ? [523.25, 659.25, 783.99, 1046.50]
                        : [1046.50, 783.99, 659.25, 523.25];

                const noteDuration = 0.30;
                const samplesPerNote = Math.floor(
                        sampleRate * noteDuration
                        );

                const mono = new Int16Array(
                        samplesPerNote * frequencies.length
                        );

                let position = 0;

                frequencies.forEach(freq => {
                    for (let i = 0; i < samplesPerNote; i++) {
                        const t = i / sampleRate;
                        const envelope = Math.exp(
                                -3 * (i / samplesPerNote)
                                );

                        mono[position + i] = Math.round(
                                Math.sin(2 * Math.PI * freq * t) *
                                envelope *
                                12000
                                );
                    }

                    position += samplesPerNote;
                });

                return pcmToStereoBytes(mono, 1);
            }

            function pcmToStereoBytes(pcm, channels) {
                if (channels === 2) {
                    return new Uint8Array(
                            pcm.buffer.slice(
                                    pcm.byteOffset,
                                    pcm.byteOffset + pcm.byteLength
                                    )
                            );
                }

                const stereo = new Int16Array(pcm.length * 2);

                for (let i = 0; i < pcm.length; i++) {
                    const sample = Math.max(
                            -32768,
                            Math.min(32767, Math.round(pcm[i] * 0.60))
                            );

                    stereo[i * 2] = sample;
                    stereo[i * 2 + 1] = sample;
                }

                return new Uint8Array(stereo.buffer);
            }

            function createMasterAudio(base64Audio) {
                const rawText = $('#myTextarea').val();
                const useChime =
                        document.getElementById('efek').checked;

                const repetition = Math.max(
                        1,
                        parseInt($('#perulangan').val(), 10) || 1
                        );
                var decoded = null;
                var ttsStereo = null;
                if (rawText != "") {
                    decoded = decodeGoogleLinear16Wav(base64Audio);

                    ttsStereo = pcmToStereoBytes(
                            decoded.pcm,
                            decoded.channels
                            );
                }
                const opening = generateChimeStereoBytes(
                        true,
                        useChime
                        );

                const closing = generateChimeStereoBytes(
                        false,
                        useChime
                        );

                const sequence = [];

                if (opening.byteLength > 0) {
                    sequence.push(opening);
                    sequence.push(createPcmSilence(1000));
                }

                for (let r = 0; r < repetition; r++) {
                    if (rawText != "")
                        sequence.push(ttsStereo);

                    if (r < repetition - 1) {
                        sequence.push(createPcmSilence(1500));
                    }
                }

                if (closing.byteLength > 0) {
                    sequence.push(createPcmSilence(1000));
                    sequence.push(closing);
                }

                sequence.push(createPcmSilence(500));

                const totalLength = sequence.reduce(
                        (sum, item) => sum + item.byteLength,
                        0
                        );

                if (!totalLength || totalLength % 4 !== 0) {
                    throw new Error(
                            `Ukuran PCM tidak valid: ${totalLength} byte`
                            );
                }

                const masterBuffer = new Uint8Array(totalLength);

                let position = 0;

                for (const item of sequence) {
                    masterBuffer.set(item, position);
                    position += item.byteLength;
                }

                console.log('========== HMS PA AUDIO ==========');
//		console.log('WAV source       :', decoded.sourceBytes, 'bytes');
//		console.log('PCM source       :', decoded.pcmBytes, 'bytes');
//		console.log('Sample rate      :', decoded.sampleRate);
//		console.log('Channel sumber   :', decoded.channels);
//		console.log('TTS stereo       :', ttsStereo.byteLength, 'bytes');
                console.log('Total audio      :', masterBuffer.byteLength, 'bytes');
                console.log('Durasi estimasi  :',
                        (masterBuffer.byteLength / 64000).toFixed(2),
                        'detik'
                        );
                console.log('===================================');

                return masterBuffer;
            }

            function createPcmSilence(durationMs) {
                // 16 kHz x 16-bit x 2 channel = 64 byte/ms

                const byteLength = Math.round(durationMs * 64);

                return new Uint8Array(byteLength);
            }

            function setButtonsDisabled(disabled) {
                $('#btnPreview').prop('disabled', disabled);
                $('#btnBroadcast').prop('disabled', disabled);
            }

            $(function () {


                const formdo = document.forms.namedItem("form-tts");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    event.preventDefault();
                    const useChime = document.getElementById('efek').checked;
                    const rawText = $('#myTextarea').val();
                    if (rawText.trim() === "") {
                        if (useChime)
                            triggerBroadcast();
                        else
                            alert_notify('fa fa-close', "Teks tidak boleh koso", 'danger', function () {});
                        return;
                    }


                    please_wait(function () {});
                    request("form-tts").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200) {
                                    unblockUI(function () {});
                                    //mulai broadcast
                                    if (broadcastSocketOpen) {
                                        const dt = {text: response.data.text, timeDisplay: response.data.timeDisplay, type: "BC"};
                                        broadcastSocket.send(JSON.stringify(dt));
                                        showBC(dt);
                                    }
                                    triggerBroadcast();

                                }
                            }

                    ).finally(() => {
//                                            $(`${idModal} [data-bs-dismiss="modal"]`).click();
                        unblockUI(function () {});
                    });

                }, false
                        );

                $(".item-drag").on("click", function () {
                    var textarea = document.getElementById("myTextarea");
                    let text = $(this).data("text");
                    if (text === "") {
                        var format = $(this).data("format");
                        bootbox.prompt({
                            title: "Masukan Huruf",
                            required: true,
                            callback: function (result) {
                                // 'result' will be null if the user cancelled or closed the modal
                                if (result === null) {
                                    alert_notify('fa fa-close', "Wajib Diisi", 'danger', function () {});
                                } else {
                                    text = format.replaceAll("{text}", result);
                                    textarea.setRangeText(text, textarea.selectionStart, textarea.selectionEnd, 'end');
                                    textarea.focus();
//                                    textarea.value += ` ${text} `;
                                }
                            }
                        });
                    }
                    textarea.setRangeText(text, textarea.selectionStart, textarea.selectionEnd, 'end');
                    textarea.focus();
                });
            });
        </script>
    </body>
</html>