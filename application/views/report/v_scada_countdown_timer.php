<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WARPING DASAR - MONITORING BEAM TRICOT</title>
        <link href="<?= base_url('dist/css/bs5/bs5.css'); ?>" rel="stylesheet">
        <link href="<?= base_url('dist/css/fontaws6.5.css'); ?>" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <link rel="shortcut icon"  href="<?php echo base_url('dist/img/favicon_heksa.ico') ?>">
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

            html, body {
                background-color: var(--bg-app);
                color: var(--text-main);
                font-family: 'Plus Jakarta Sans', sans-serif;
                margin: 0;
                padding: 0;
                height: 100vh;
                width: 100vw;
                overflow: hidden;
            }

            /* 1. HEADER ATAS */
            .fixed-top-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: calc(var(--header-height) + var(--table-header-height));
                background-color: var(--bg-app);
                z-index: 1000;
                padding: 12px 24px 0 24px;
            }

            .main-header {
                height: var(--header-height);
                border: 1px solid var(--border-color);
                background-color: var(--card-main);
                border-radius: 14px;
                padding: 0 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            }

            .dashboard-title {
                font-weight: 600;
                font-size: 1.6rem;
                letter-spacing: -0.5px;
                color: #0f172a;
            }

            .live-clock-wrapper {
                background-color: #f1f5f9;
                border: 1px solid #cbd5e1;
                border-radius: 10px;
                padding: 6px 20px;
                display: inline-block;
            }

            .live-clock-time {
                font-family: 'JetBrains Mono', monospace;
                font-size: 2rem;
                font-weight: 800;
                color: #0369a1;
                line-height: 1;
            }

            /* 2. TABEL HEADER */
            .fixed-table-header {
                height: var(--table-header-height);
                background-color: var(--muted-text);
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
                overflow: hidden;
                margin-top: 12px;
            }

            .table-master {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                table-layout: fixed;
                margin: 0;
            }

            .table-master th {
                height: var(--table-header-height);
                color: #ffff !important;
                font-size: 0.85rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                text-align: center;
                vertical-align: middle;
                padding: 0 10px;
                border: none;
            }

            /* 3. CONTAINER DATA DISPLAY */
            .scrollable-data-container {
                margin-top: calc(var(--header-height) + var(--table-header-height) + 80px);
                height: calc(100vh - var(--header-height) - var(--table-header-height) - 32px);
                overflow-y: auto;
                padding: 0 24px 24px 24px;
            }

            .scrollable-data-container::-webkit-scrollbar {
                display: none;
            }
            .scrollable-data-container {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .table-card-body {
                background: var(--card-main);
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
                border: 1px solid var(--border-color);
                border-top: none;
                overflow: hidden;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03);
            }

            .table-master tbody tr {
                height: 95px;
                transition: background-color 0.3s ease;
            }

            /* STYLES ROW */
            .row-darurat {
                background-color: #fff1f2 !important;
                border-left: 8px solid #e11d48;
            }

            .row-awas {
                background-color: #fffbe2 !important;
                border-left: 8px solid #d97706;
            }

            .row-aman {
                background-color: #ffffff !important;
                border-left: 8px solid #10b981;
            }

            .table-master td {
                vertical-align: middle;
                border-bottom: 1px solid var(--border-color);
                font-size: 1.05rem;
                text-align: center;
                padding-top: 12px;
                /*font-weight: 700;*/
                color: #1e293b;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            /* LEBAR KOLOM */
            .col-prio {
                width: 6%;
            }
            .col-countdown {
                width: 17%;
            }
            .col-mesin {
                width: 10%;
            }
            .col-mo {
                width: 15%;
            }
            .col-artikel {
                width: 28%;
            }
            .col-target {
                width: 11%;
            }
            .col-urgensi {
                width: 13%;
            }

            .mono {
                font-family: 'JetBrains Mono', monospace;
            }

            /* TIMER BOX STYLING (DIAM TANPA ANIMASI) */
            .timer-box {
                font-size: 1.45rem;
                font-weight: 800;
                padding: 8px 14px;
                border-radius: 8px;
                background-color: #0f172a;
                color: #ffff;
                display: inline-block;
                box-shadow: 0 2px 4px rgba(0,0,0,0.06);
                letter-spacing: -0.5px;
            }

            .timer-expired {
                background-color: #e11d48 !important;
                color: #ffffff !important;
            }
            .timer-siaga {
                background-color: #b45309 !important;
                color: #ffffff !important;
            }

            /* BADGE URGENCY */
            .urgency-badge {
                font-weight: 800;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 0.85rem;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                display: inline-block;
                width: 100%;
                text-align: center;
            }
            .badge-aman {
                background-color: #d1fae5;
                color: #047857;
            }
            .badge-awas {
                background-color: #fef3c7;
                color: #b45309;
            }
            .badge-darurat {
                background-color: #ffe4e6;
                color: #be123c;
            }

            .mo-block {
                border: 1px solid #cbd5e1;
                background-color: #f8fafc;
                border-radius: 6px;
                padding: 6px 8px;
                text-align: left;
            }
            .mo-label {
                font-size: 0.7rem;
                text-transform: uppercase;
                font-weight: 800;
                color: #64748b;
                display: block;
                line-height: 1;
                margin-bottom: 2px;
            }

            .spec-item {
                font-size: 0.9rem;
                color: #0f172a;
                /*font-weight: 700;*/
                line-height: 1.3;
            }
            .reff-item {
                font-size: 0.9rem;
                color: #334155;
                font-weight: 700;
            }
            .live-bar {
                background: var(--card-light);
                border-bottom: 1px solid #dee2e6;
                padding: 8px 25px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .timestamp {
                font-family: 'JetBrains Mono', monospace;
                color: var(--accent-blue);
                font-size: 1.1rem;
            }
            .brand {
                /*font-size: 1rem;*/
                letter-spacing: 2px;
                font-weight: 600;
                color: var(--muted-text);
            }
            .counter-pill {
                background: #f8f9fa;
                border-radius: 4px;
                /*padding: 8px 15px;*/
                border-left: 4px solid #dee2e6;
                text-align: center;
            }
            .c-num {
                font-family: 'JetBrains Mono', monospace;
                font-size: 1.6rem;
                font-weight: 800;
                line-height: 1;
            }
            .c-lab {
                font-size: 0.65rem;
                font-weight: 700;
                color: var(--muted-text);
                margin-top: 2px;
            }

            .status-list-mesin{
                overflow: auto;
                white-space: nowrap;
            }
            .status-list-mesin::-webkit-scrollbar {
                display: none;
            }
            .data-list {
                height: 100vh;
                padding: 1rem;
                overflow-y: hidden;
                border: 2px solid #222;
                border-radius: 5px;
                padding-left: 0;
                margin-left: 0;
                list-style-position: inside;
                list-style-type: inherit; /* Removes the bullet points */
            }
            .table-row {
                display: grid;
                grid-template-columns: 6% 17% 10% 15% 28% 11% 13%; /* 3 equal columns */
                list-style: none; /* Removes default bullets */
                padding: 1rem;
                /*height: 100px;*/
            }
        </style>
    </head>
    <body>

        <!-- FIXED HEADER -->
        <div class="fixed-top-wrapper">
            <div class="main-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="dashboard-title m-0">COUNTDOWN TIMER - WARPING DASAR</h3>
                    <div class="brand small">MONITORING SISA WAKTU BEAM MESIN TRICOT HILIR</div>
                </div>

                <div class="timestamp" id="realtime-clock">LOADING...</div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 border-end border-light">
                    <div class="main-header d-flex align-items-center justify-content-between">
                        <div class="counter-pill" style="color: #e11d48 ;border-color: #e11d48 ">
                            <div class="c-num stt-habis">0</div><div class="c-lab">Beam Habis</div>
                        </div>
                        <div class="counter-pill" style="color: #e11d48 ;border-color: #e11d48 ">
                            <div class="c-num stt-warning">0</div><div class="c-lab">Warning</div>
                        </div>
                        <div class="counter-pill" style="color: #b45309;border-color: #b45309">
                            <div class="c-num stt-siaga">0</div><div class="c-lab">Siaga</div>
                        </div>
                        <div class="counter-pill" style="color: #047857;border-color: #047857">
                            <div class="c-num stt-aman">0</div><div class="c-lab">Aman</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 border-end border-light">
                    <div class="main-header status-list-mesin d-flex align-items-center justify-content-between" id="scrollListMesin">
                        <?php
                        foreach ($msn as $key => $value) {
                            ?>
                                                                                                    <!--                            <div class="counter-pill stt-msn-<?= $value->devid_esp ?>"  style="border-color: rgb(0, 0, 128) ">
                                                                                                                                    <div class="c-num">MC<?= $value->no_mesin ?></div>
                                                                                                                                </div>-->

                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- TABEL HEADER -->
            <div class="fixed-table-header">
                <table class="table-master">
                    <thead>
                        <tr>
                            <th class="col-prio">PRIO</th>
                            <th class="col-countdown">Sisa Waktu (Countdown)</th>
                            <th class="col-mesin">Mesin Hilir</th>
                            <th class="col-mo">MO Hilir</th>
                            <th class="text-start col-artikel">Spesifikasi BD</th>
                            <th class="col-target">Reff</th>
                            <th class="col-urgensi">Urgensi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- DATA DISPLAY CONTAINER -->
        <div class="scrollable-data-container" id="scrollContainer">
            <div class="table-card-body">
                <table class="table-master">
                    <tbody id="timer-table-body">
                        <!-- Data dimasukkan via JS -->
                    </tbody>
                </table>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script src="<?= base_url('dist/js/bs5/bs5.bundle.min.js') ?>"></script>
        <script src="<?= base_url('dist/js/jquery.autoscroll.js') ?>"></script>
        <script>
            const nowMs = Date.now();
            let refSec = 0;
            let machineData = [];
            let tempRefresh = "";
            let refresh = false;

            function updateClock() {
                const now = new Date();
                const options = {weekday: 'short', day: 'numeric', month: 'short'};
                document.getElementById('realtime-clock').textContent =
                        `${now.toLocaleDateString('id-ID', options).toUpperCase()} | ${now.toLocaleTimeString('id-ID', {hour12: false})}`;
                refSec++;
                if (refresh) {
                    refSec = 0;
                    refresh = false;
//                    getData();
                    initTableDOM();
                }
                if (refSec > 300)
                    refresh = true;
            }

            const getDatas = (() => {
                return $.ajax({
                    url: "<?php echo base_url(); ?>report/scadacountdown/list_data",
                    type: "GET",
                    data: {
                        dept: "<?= $dept ?>"
                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });



            async function initTableDOM() {
                const tbody = document.getElementById('timer-table-body');
                await getDatas().then((res) => {
                    machineData = [];
                    var dt = res.data;
                    dt.forEach(e => {
                        if (e.nama_produk === null) {
                            return;
                        }
                        let targetTime = moment(e.estimasi_tgl_habis);
                        var dt = {
                            id: e.kode,
                            mesin: `MC${e.no_mesin}`,
                            mo: e.kode,
                            bd: e.nama_produk.split(","),
                            reff: e.reff_note.split(","),
                            targetTime: targetTime.toDate(),
                            devid_esp: e.devid_esp,
                            sisa: targetTime.diff(moment(), "seconds"),
                            nama_mesin: e.nama_mesin
                        };

                        machineData.push(dt);
                    });
                });
                machineData.sort((a, b) => a.sisa - b.sisa);
//                console.log(machineData);
                tbody.innerHTML = '';
                machineData.forEach((item, index) => {
                    const trHtml = `
                        <tr id="row-${item.id}" class="row-aman table-row">
                            <td class="mono fs-5 fw-bold text-muted">#${index + 1}</td>
                            <td class="">
                                <div id="timer-${item.id}" class="timer-box mono">00:00:00</div>
                            </td>
                            <td style="font-size : 20px !important; overflow-wrap: break-word !important; " class="fs-3 fw-extrabold stt-msn-${item.devid_esp}">${item.nama_mesin}</td>
                            <td class="">
                                <div class="mo-block">
                                    <span class="mo-label">MO TRICOT</span>
                                    <span class="mono text-dark fw-bold fs-6">${item.mo}</span>
                                </div>
                            </td>
                            <td class="text-start">
                                ${item.bd.map(bdName => `<div class="spec-item">${bdName}</div>`).join('')}
                            </td>
                            <td class="">
                                ${item.reff.map(reffName => `<div class="reff-item mono">${reffName}</div>`).join('')}
                            </td>
                            <td class="">
                                <span id="badge-${item.id}" class="urgency-badge badge-aman">AMAN</span>
                            </td>
                        </tr>
                    `;
//                                    console.log(trHtml);
                    tbody.innerHTML += trHtml;
                });
            }

            function updateTimerValues() {
                const currentTime = Date.now();
                let beamHabis = 0;
                let criticalCount = 0;
                let warningCount = 0;
                let saveCount = 0;
                let tempr = "";
                machineData.forEach((item) => {
                    const rowElem = document.getElementById(`row-${item.id}`);
                    const timerElem = document.getElementById(`timer-${item.id}`);
                    const badgeElem = document.getElementById(`badge-${item.id}`);

                    if (!timerElem)
                        return;

                    const totalSeconds = moment(item.targetTime).diff(moment(), "seconds");

                    const isNegative = totalSeconds < 0;
                    const absoluteSeconds = Math.abs(totalSeconds);

                    const hours = Math.floor(absoluteSeconds / 3600);
                    const minutes = Math.floor((absoluteSeconds % 3600) / 60);
                    const seconds = absoluteSeconds % 60;

                    const sign = isNegative ? "-" : "";
                    const formattedTime = `${sign}${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    if (isNegative) {
                        rowElem.className = 'row-darurat table-row';
                        badgeElem.className = 'urgency-badge badge-darurat';
                        badgeElem.innerText = 'BEAM HABIS';
                        timerElem.className = 'timer-box mono timer-expired';
                        timerElem.innerText = `${formattedTime}`;
                        beamHabis++;
                    } else if (hours < 3) {
                        rowElem.className = 'row-darurat table-row';
                        badgeElem.className = 'urgency-badge badge-darurat';
                        badgeElem.innerText = 'Warning';
                        timerElem.className = 'timer-box mono timer-expired';
                        timerElem.innerText = `${formattedTime}`;
                        criticalCount++;
                    } else if (hours <= 6) {
                        rowElem.className = 'row-awas table-row';
                        badgeElem.className = 'urgency-badge badge-awas';
                        badgeElem.innerText = 'SEGERA SIAPKAN';
                        timerElem.className = 'timer-box mono timer-siaga';
                        timerElem.innerText = formattedTime;
                        warningCount++;
                    } else {
                        rowElem.className = 'row-aman table-row';
                        badgeElem.className = 'urgency-badge badge-aman';
                        badgeElem.innerText = 'SIAGA';
                        timerElem.className = 'timer-box mono';
                        timerElem.innerText = formattedTime;
                        saveCount++;
                    }
                    tempr = `${criticalCount}-${warningCount}-${saveCount}`;
                });
                $(".stt-warning").html(criticalCount);
                $(".stt-siaga").html(warningCount);
                $(".stt-aman").html(saveCount);
                $(".stt-habis").html(beamHabis);
                if (tempRefresh !== "" && tempRefresh !== tempr)
                    refresh = true;
                tempRefresh = tempr;

//                document.getElementById('total-critical-badge').innerText = `âš ï¸ ${criticalCount} MESIN STOP`;
            }




//            var ipSocket = "<?= $ip_socket ?>";
//            const socket = new WebSocket(`${ipSocket}`);
//            socket.onopen = function () {
//                console.log("Connected to server");
//            };
//            var stateMesin = JSON.parse('<?= json_encode($state) ?>');
//            socket.onmessage = async function (event) {
//                var data = JSON.parse(event.data);
//                if (data["version"] != undefined && data["version"] == 2) {
//                    data["data"].forEach(dtt => {
//                        $(`.stt-msn-${dtt.devid}`).css("color", stateMesin[dtt.state]["warna"]);
//                    });
//                }
//            };


            function startAutoScroll() {
                const container = document.getElementById('scrollContainer');
                let currentScroll = 0; // Variabel penampung nilai desimal
                let scrollSpeed = 0.3; // Kecepatan scroll (0.3 sekarang berjalan sempurna)
                let isPaused = true;

                // Jeda 5 detik di awal sebelum mulai scroll
                setTimeout(() => {
                    isPaused = false;
                }, 5000);

                function scrollAnimation() {
                    if (!isPaused) {
                        // Cek jika posisi scroll sudah mencapai batas bawah
                        if ((container.scrollTop + container.clientHeight) >= container.scrollHeight - 2) {
                            isPaused = true;
                            setTimeout(() => {
                                currentScroll = 0;
                                container.scrollTop = 0; // Reset ke posisi paling atas

                                // Mulai scroll lagi setelah jeda 5 detik di atas
                                setTimeout(() => {
                                    isPaused = false;
                                }, 5000);
                            }, 5000);
                        } else {
                            currentScroll += scrollSpeed;
                            container.scrollTop = currentScroll; // Terapkan nilai akumulasi desimal
                        }
                    }
                    requestAnimationFrame(scrollAnimation);
                }

                requestAnimationFrame(scrollAnimation);
            }

            $(function () {
//                getData();
//                initTableDOM();
            });

            window.onload = function () {
//                setInterval(, 1000);
//                getData();
                initTableDOM();
                updateTimerValues();
                setInterval(() => {
                    updateClock();
                    updateTimerValues();

                }, 1000);
                setTimeout(() => {
                    startAutoScroll();
                }, 5000);

            };
        </script>
    </body>
</html>