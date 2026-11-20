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
                                            Karakter Suara
                                        </button>
<!--                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text="" data-format='<say-as interpret-as="cardinal">{text}</say-as>' >
                                            Bilangan Suara
                                        </button>-->
                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text="" data-format='<say-as interpret-as="digits">{text}</say-as>' >
                                            Digit Suara
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text='<break time="1s"/>' draggable="true" >Jeda Suara 1detik</button>

                                        <button type="button" class="btn btn-default btn-sm  item-drag" data-text='<break time="3s"/>' draggable="true" >Jeda Suara 3detik</button>

                                    </div>
                                </div>


                                <div class="menu-drag col-md-8 border-end border-light">
                                    <form class="form-horizontal" name="form-tts" id="form-tts" method="POST" action="<?= base_url("setting/tts/save") ?>">
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
                                                <div class="col-xs-6">
                                                    <button type="button" class="btn btn-default btn-sm" onClick="previewSound();">Preview Sound</button>
                                                </div>
                                                <div class="col-xs-6">
                                                    <button type="submit" class="btn btn-success btn-sm">Mulai Broadcast</button>
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
            function drag(ev) {
                var data = ev.target.dataset;
                ev.dataTransfer.setData('text', JSON.stringify(data));
            }
            function drop(ev) {
                ev.preventDefault();
                var data = ev.dataTransfer.getData("text");
                if (JSON.parse(data)) {
                    var data = JSON.parse(data);
                    var textarea = document.getElementById("myTextarea");
                    if (data.text === "") {
                        bootbox.prompt({
                            title: "Masukan Huruf",
                            required: true,
                            callback: function (result) {
                                // 'result' will be null if the user cancelled or closed the modal
                                if (result === null) {
                                    alert_notify('fa fa-close', "Wajib Diisi", 'danger', function () {});
                                } else {
                                    data.text = data.format.replaceAll("{text}", result);
                                    textarea.value += ` ${data.text} `;
                                }
                            }
                        });
                    }
                    textarea.value += ` ${data.text} `;
                }

            }
            function allowDrop(ev) {
                ev.preventDefault(); // Required to allow dropping
            }

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

            function newFetchGoogleTTS() {
                return new Promise((resolve, reject) => {
                    const raw = document.getElementById('myTextarea').value.trim();
                    if (!raw)
                        return reject(new Error('Teks pengumuman masih kosong.'));
                    if (!GOOGLE_API_KEY || GOOGLE_API_KEY.startsWith('ISI_'))
                        return reject(new Error(
                                'Isi GOOGLE_API_KEY untuk uji coba, atau arahkan request ke backend HMS.'
                                ));
                    updateStatus('Menghubungi Google Cloud TTS...',
                            'bg-warning text-dark');
                    const payload = {
                        input: {
                            ssml: `<speak>${raw}</speak>`
                        },
                        voice: {
                            languageCode: 'id-ID',
                            ssmlGender: document.getElementById('jenis_suara').value
                        },
                        audioConfig: {
                            audioEncoding: 'LINEAR16',
                            sampleRateHertz: 16000,
                            speakingRate: +document.getElementById('speed_suara').value
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
                            alert_notify('fa fa-close', "Siap Diputar", 'success', function () {});
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

            async function triggerBroadcast(is = "save") {
                const espIp = "<?= $ip_esp ?>";
                const espToken = "<?= $token_esp ?>";
                const useChime = document.getElementById('efek').checked;
                const rawText = $('#myTextarea').val();

                if (!espIp) {
                    alert_notify('fa fa-close', "Alamat ESP tidak ditemukan", 'danger', function () {});
                    return;
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

                function chimeMono(opening) {
                    if (!document.getElementById('efek').checked)
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

                function buildMasterBuffer(ttsMono) {
                    const rep = +document.getElementById('perulangan').value,
                            use = document.getElementById('efek').checked,
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

                const mono = await newFetchGoogleTTS(),
                        master = buildMasterBuffer(mono);


// 3. Trigger Download File .PCM ke Komputer/Server Lokal
                const blob = new Blob([master], {type: 'application/octet-stream'});
                const formData = new FormData();
                formData.append("fl", blob, "example.pcm");
                fetch("<?php echo base_url(); ?>setting/tts/save_file", {
                    method: "POST",
                    body: formData
                            // Note: Do NOT manually set the 'Content-Type' header here. 
                            // The browser automatically assigns the correct multipart/form-data boundary.
                })
                        .then(response => response.json())
                        .then(data => console.log("Success:", data))
                        .catch(error => console.error("Error:", error))
//                        const url = URL.createObjectURL(blob);
//                        const a = document.createElement('a');
//                        a.href = url;
//                        a.download = `broadcast_${Date.now()}.pcm`;
//                        document.body.appendChild(a);
//                        a.click();
//                        document.body.removeChild(a);
//                        URL.revokeObjectURL(url);

                updateStatus('File .pcm berhasil disimpan!', 'bg-success');

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
                                        triggerBroadcast();
                                    }

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
                                    textarea.value += ` ${text} `;
                                }
                            }
                        });
                    }
                    textarea.value += ` ${text} `;
                });
            });
        </script>
    </body>
</html>