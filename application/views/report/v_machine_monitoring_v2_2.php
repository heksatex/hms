<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <title>HMS - <?= $departmen->nama ?? "" ?> OEE Dashboard</title>
        <link href="<?= base_url('dist/css/bs5/bs5.css'); ?>" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= base_url('dist/css/slider.css'); ?>" />
        <!-- Menggunakan ECharts CDN -->
        <script src="<?= base_url('dist/js/echarts.min.js'); ?>"></script>
        <style>
            :root {
                --bg-light: #f4f7fa;
                --card-light: #ffffff;
                --primary-text: #212529;
                --muted-text: #6c757d;
                --accent-blue: #0d6efd;
                --status-green: #198754;
                --header-height: 85px;
                --table-header-height: 55px;
                --card-main: #ffffff;
                --border-color: #e2e8f0;
            }

            body {
                background-color: var(--bg-light);
                font-family: 'Inter', sans-serif;
                color: var(--primary-text);
                overflow: hidden;
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
            }
            .brand {
                font-size: 1rem;
                letter-spacing: 2px;
                font-weight: 800;
                color: var(--muted-text);
            }
            .autoplay-progress {
                position: absolute;
                top: 0px;
                z-index: 10;
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: var(--swiper-theme-color);
            }

            .autoplay-progress svg {
                --progress: 0;
                position: absolute;
                left: 0;
                top: 0px;
                z-index: 10;
                stroke-width: 2px;
                stroke: var(--swiper-theme-color);
                fill: none;
                stroke-dashoffset: calc(140% * (1 - var(--progress)));
                stroke-dasharray: 140%;
                /* transform: rotate(-90deg); */
            }

            /*countdown timer*/
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
                font-size: 1.2rem;
                letter-spacing: -0.5px;
                color: #0f172a;
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
                margin-top: calc(var(--header-height) + var(--table-header-height) + 30px);
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
                /*height: 95px;*/
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
            .table-row {
                display: grid;
                grid-template-columns: 6% 17% 10% 15% 28% 11% 13%; /* 3 equal columns */
                list-style: none; /* Removes default bullets */
                padding: 1rem;
                /*height: 100px;*/
            }
            /* sampai sini countdown */



            .summary-wrapper {
                background-color: var(--card-light);
                border-radius: 12px;
                border: 1px solid #dee2e6;
                margin: 12px;
                padding: 15px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.03);
            }
            .oee-value {
                font-size: 3.2rem;
                font-weight: 900;
                line-height: 1;
                color: var(--primary-text);
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
            .br-run {
                border-color: var(--status-green);
                color: var(--status-green);
            }
            .br-creel {
                border-color: #ffc107;
                color: #856404;
            }
            .br-down {
                border-color: #dc3545;
                color: #842029;
            }

            #trend_chart {
                height: 110px;
                width: 100%;
            }
            .chart-card {
                background-color: var(--card-light);
                border-radius: 12px;
                margin: 0 12px;
                padding: 10px;
                border: 1px solid #dee2e6;
                box-shadow: 0 4px 6px rgba(0,0,0,0.03);
            }
            #scroll-container {
                height: calc(100vh - 250px);
                overflow-y: auto;
                position: relative;
                scrollbar-width: none;
                scroll-behavior: smooth;
            }
            #scroll-container::-webkit-scrollbar {
                display: none;
            }

            /* Ukuran container timeline disesuaikan dengan jumlah mesin */
            #timeline_tricot {
                width: 100%;
                height: 1100px;
            }

            .legend-pill {
                padding: 4px 12px;
                border-radius: 3px;
                font-size: 0.8rem;
                font-weight: 700;
                margin-left: 8px;
                border: 1px solid rgba(0,0,0,0.05);
            }


            .d-flex{
                display: flex;
            }
            .justify-content-between {
                justify-content: space-between;
            }
            .align-items-center{
                align-items: center;
            }
            .mb-3{
                margin-bottom: 1rem;
            }

            @keyframes placeHolderShimmer{
                0%{
                    background-position: -468px 0
                }
                100%{
                    background-position: 468px 0
                }
            }
            .linear-background {
                animation-duration: 1s;
                animation-fill-mode: forwards;
                animation-iteration-count: infinite;
                animation-name: placeHolderShimmer;
                animation-timing-function: linear;
                background: #f6f7f8;
                background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
                background-size: 1000px 104px;
                z-index: 9999;
                position: relative;
                overflow: hidden;
            }

            .autoplay-progress {
                position: absolute;
                top: 0px;
                z-index: 10;
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: var(--swiper-theme-color);
            }

            .autoplay-progress svg {
                --progress: 0;
                position: absolute;
                left: 0;
                top: 0px;
                z-index: 10;
                stroke-width: 2px;
                stroke: var(--swiper-theme-color);
                fill: none;
                stroke-dashoffset: calc(140% * (1 - var(--progress)));
                stroke-dasharray: 140%;
                /* transform: rotate(-90deg); */
            }

            .card {
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                transition: 0.3s;
                width: 9%;
                float: left;
                height: 16em;
                margin: 5px 5px 5px 5px;
            }

            /*            .card:hover {
                            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
                        }*/

            .container {
                padding: 8px 8px;
                width: 100%;
                text-align: center;
            }
            @media screen and (max-width:1200px) {
                .card {
                    width: 10%;
                }
                .oee-value {
                    font-size: 2.5rem;
                }
                #trend_chart {
                    height: 90px;
                }
                .c-num {
                    font-size: 1.4rem;
                    font-weight: 800;
                }
                title {
                    font-size: 1rem;
                }
                .timestamp {
                    font-size: 1rem;
                }
                .summary-wrapper {
                    margin: 10px;
                    padding: 12px;
                }
                .legend-pill {
                    padding: 3px 10px;
                }
            }
            @media screen and (max-width:1000px) {
                .card {
                    width: 12%;
                }
                .oee-value {
                    font-size: 2rem;
                }
                #trend_chart {
                    height: 80px;
                }
                .c-num {
                    font-size: 1rem;
                    font-weight: 700;
                }
                title {
                    font-size: 0.8rem;
                }
                .timestamp {
                    font-size: 0.8rem;
                }
                .summary-wrapper {
                    margin: 8px;
                    padding: 10px;
                }
                .legend-pill {
                    padding: 2px 8px;
                }
            }



            img.center {
                display: block;
                margin-left: auto;
                margin-right: auto;
                width: 50%;
            }
            span.center {
                text-align: center;
                font-size: 0.9rem;
                font-weight: 600;
            }
            .durasi-text {
                font-size: 11px;
            }

            .icon-logo path {
                fill: #000000;
                transition: fill 0.3s ease; /* Makes the transition smooth */
            }

            /* Mouseover color change */
            .icon-logo:hover path {
                fill: #8B8484;
            }

            /* Animate the pattern tile vertically */
            @keyframes scrollVertical {
                0% {
                    transform: translateY(0);
                }
                100% {
                    /* Must match the exact height or a seamless multiple of your pattern definition */
                    transform: translateY(calc(240px - 100vh));
                }
            }

            #timeline_tricot_svg{
                animation: scrollVertical 20s linear infinite;
            }

        </style>
        <link rel="shortcut icon"  href="<?php echo base_url('dist/img/favicon_heksa.ico') ?>">
    </head>
    <body>
        <div class="live-bar d-flex justify-content-between align-items-center">
            <div class="brand"><?= $departmen->nama ?? "" ?> - MACHINE MONITORING</div>
            <div class="timestamp" id="realtime-clock">LOADING...</div>
        </div>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php if (strtolower($dept) === "wrd") { ?>
                    <div class="swiper-slide" data-swiper-autoplay="300000">
                        <!-- FIXED HEADER -->
                        <div class="fixed-top-wrapper">

                            <div class="row align-items-center">
                                <div class="col-md-6 border-end border-light">

                                    <h4 class="dashboard-title m-0">
                                        <a href="#"onclick="history.back(); return false;">
                                            <i class="fa-solid fa-arrow-left me-2"></i>
                                        </a>
                                        COUNTDOWN TIMER - WARPING DASAR</h4>
                                </div>
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
                                        <div class="counter-pill" style="color: #000000;border-color: #000000">
                                            <div class="c-num stt-total">0</div><div class="c-lab">Total</div>
                                        </div>
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
                    </div>
                <?php } ?>
                <div class="swiper-slide" data-swiper-autoplay="300000">
                    <div class="container-fluid">
                        <div class="summary-wrapper">
                            <div class="row align-items-center">
                                <div class="col-md-6 border-end border-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-center">
                                            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.55rem;">Today's Utilization</div>
                                            <div class="oee-value persen-today">0%</div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row g-2">
                                                <?php
                                                $state = [];
                                                foreach ($status as $key => $value) {
                                                    $state[$key] = 0;
                                                    $total = 0;
                                                    foreach ($mesin as $k => $val) {
                                                        $durasis = $durasi["d{$val->devid}"];

                                                        if ($durasis->state == $key)
                                                            $total += 1;
                                                    }
                                                    ?>
                                                    <div class="col-4">
                                                        <div class="counter-pill" style="color: <?= $value['warna'] ?>;border-color: <?= $value['warna'] ?>">
                                                            <div class="c-num stt-<?= $key ?>"><?= $total ?></div><div class="c-lab"><?= $value["stt"] ?></div>

                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                $state = json_encode($state);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">30-Day Utilization</div>
                                        <div class="small fw-bold" style="color: var(--accent-blue); font-size: 0.7rem;">MTD AVG: <span class="persen-util">0</span></div>
                                    </div>
                                    <div id="trend_chart"></div>
                                </div>
                            </div>
                        </div>

                        <div class="chart-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="text-uppercase m-0" style="font-size: 0.8rem; font-weight:800; color:var(--muted-text)">CAPTURE <span class="capture_date">24 JAM</span>
                                    <span id="jumlahMesin">&nbsp; (0 Mesin)</span>
                                </h5>

                                <div class="d-flex">
                                    <?php foreach ($status as $key => $value) {
                                        ?>
                                        <div class="legend-pill" style="background:<?= $value['warna'] ?>; color:#ffffff"><?= $value["stt"] ?></div>
                                    <?php }
                                    ?>
                                </div>
                                <div class="zoom-controls ms-3 d-flex">
                                    <div  class="legend-pill minus-day-timeline" title="Previous"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill icon-logo" viewBox="0 0 16 16">
                                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                                        </svg>
                                    </div>
                                    <div  class="legend-pill reset-day-timeline"  onclick="" title="Now">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise icon-logo" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z"/>
                                        <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466"/>
                                        </svg>
                                    </div>
                                    <div  class="legend-pill add-day-timeline"  title="Next">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill icon-logo" viewBox="0 0 16 16">
                                        <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/>
                                        </svg>
                                    </div>

                                </div>
                            </div>
                            <div id="scroll-container">
                                <div id="timeline_tricot"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" data-swiper-autoplay="10000">
                    <div class="col-md-12">
                        <div id="scroll-containers">
                            <?php
                            $exist = [];
                            $sumRed = 0;
                            $sumYel = 0;
                            $sumGr = 0;
                            foreach ($mesin as $key => $value) {
                                $durasis = $durasi["d{$value->devid}"];
                                $drs = $durasiAll["d{$value->devid}"];
                                $logo = "mark_success";
                                $ttlDwn = $value->downtime / $value->total;
                                $ttlUp = $value->uptime / $value->total;
                                $value->state = $durasis->state;
                                $border = $status[1]["warna"];
                                if ($value->state != 1 && $durasis->total_down <= 10) {
                                    $logo = "mark_danger";
                                    $border = $status[$value->state]["warna"];
                                    $sumYel += 1;
                                } else if ($value->state != 1 && $durasis->total_down >= 11) {
                                    $logo = "mark_danger";
                                    $border = $status[$value->state]["warna"];
                                    $sumRed += 1;
                                } else {
                                    $sumGr += 1;
                                }
                                ?>
                                <!--<div class="col-lg-2 col-md-4 col-xs-6">-->
                                <div class="card cardstatus card-<?= "d{$value->devid}" ?>" data-durasi_up="<?= $drs->state == 1 ? $drs->time : 0 ?>" data-durasi_down="<?= $drs->state != 1 ? $drs->time : 0 ?>"
                                     data-state="<?= $value->state ?>" data-totaldown = "<?= $ttlDwn ?>" data-total="<?= $value->total ?>" data-uptime="<?= $ttlUp ?>" data-downtime="<?= $ttlDwn ?>"
                                     style="border: 1px solid black;">
                                    <div class="container container-<?= "d{$value->devid}" ?>" style="height: 20%; background-color: <?= $border ?>; color:#ffffff">
                                        <b><?= "{$value->nama_mesin}" ?></b>
                                    </div>
                                    <span style="padding-top: 15px" class="center statuss statuss-<?= "d{$value->devid}" ?>" data-status="<?= $value->state ?>"><?= $durasis->status ?></span>
                                    <!--<img class="img-<?= "d{$value->devid}" ?> center statuss" data-status="<?= $logo ?>" src="<?= base_url("dist/img/{$logo}.png") ?>" alt="Avatar">-->
                                    <div class="container" style="word-wrap: break-word;">
                                        <p title="Downtime Shift Sekarang">Downtime : <span class="down-<?= "d{$value->devid}" ?>"><?= round(($ttlDwn / $value->total) * 100) ?></span> %</p>
                                        <span class="durasi-text durasi-<?= "d{$value->devid}" ?>"><?= ($value->state == 1) ? "Running : {$drs->time_text}" : "Stop : {$drs->time_text}" ?></span>
                                    </div>
                                </div>

                                <!--</div>-->
                            <?php }
                            ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
        <div class="autoplay-progress">
            <svg width="100%" height="1px">
            <line x1="0" y1="0" x2="100%" y2="0" />
            </svg>
            <span></span>
        </div>

        <?php $this->load->view("admin/_partials/js.php") ?>
        <script src="<?= base_url('dist/js/bs5/bs5.bundle.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('dist/js/sliders.js') ?>">
                                            function updateClock() {
                                                const now = new Date();
                                                const options = {weekday: 'short', day: 'numeric', month: 'short'};
                                                document.getElementById('realtime-clock').textContent =
                                                        `${now.toLocaleDateString('id-ID', options).toUpperCase()} | ${now.toLocaleTimeString('id-ID', {hour12: false})}`;
                                            }

        </script>

        <script type="text/javascript">// slider
            const progressCircle = document.querySelector('.autoplay-progress svg');
            var swiper = new Swiper('.mySwiper', {
                allowTouchMove: false,
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: {
//                    delay: 300000,
                    disableOnInteraction: false
                },
                on: {
                    autoplayTimeLeft(s, time, progress) {
                        progressCircle.style.setProperty('--progress', 1 - progress);
                    }
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                }
            });
        </script>
        <script> // countdown
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
                        dept: "TRI"
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
                let total = 0;
                machineData.forEach((item) => {
                    total += 1;
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
                        badgeElem.innerText = 'AMAN';
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
                $(".stt-total").html(total);
                if (tempRefresh !== "" && tempRefresh !== tempr)
                    refresh = true;
                tempRefresh = tempr;
            }



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
        </script>

        <script>
            var countMesin = parseInt("<?= $count_mesin ?>", 10);


            const  asData = ((custom = "") => {
                return $.ajax({
                    type: "post",
                    data: {
                        dept: "<?= $dept ?>",
                        custom: custom,
                        day: daysTimeline
                    },
                    url: "<?php echo base_url(); ?>report/machinemonitoringv2/get_items",
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
            loop = 0;
            const updateData = (async(custom = "") => {
                return $.ajax({
                    type: "post",
                    data: {
                        day: daysTimeline,
                        custom: custom,
                        dept: "<?= $dept ?>"
                    },
                    url: "<?php echo base_url(); ?>report/machinemonitoringv2/ins_timeline",

                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
            var runToday = 0;
            var timeRunToday = 0;
            const getMinuteDate = ((mulai, sampai) => {
                const dateMulai = moment(mulai).startOf('day');
                const dateSampai = moment(sampai);
                if (dateSampai.diff(dateMulai, 'days') > 0) {
                    return 1440;
                }
                return dateSampai.diff(dateMulai, 'minutes');
            });
            async function persenToday() {
                runToday = 0;
                timeRunToday = 0;
                await asDataGrafik(0).then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        runToday += parseInt(sd.running);
                        timeRunToday += getMinuteDate(moment().toString(), moment().toString()) * sd.count_mesin;
                    });
                });
                updatePersenToday(1, false);
            }
            const updatePersenToday = ((state, update = true) => {
                if (update) {
                    timeRunToday += 1;
                    if (state == 1)
                        runToday += 1;
                }
                var pr = (runToday / timeRunToday) * 100;
                pr = pr.toFixed(2);
                $(".persen-today").html(`${pr}%`);
            });
            const chartDom = document.getElementById('timeline_tricot');
            const myChart = echarts.init(chartDom, null, {
                renderer: 'svg' // Use 'svg' instead of 'canvas'
            });
            let currentZoomStart = 0;
            let currentZoomEnd = 100;
            function zoomTimeline(factor) {
                if (!myChart)
                    return;
                let center = (currentZoomStart + currentZoomEnd) / 2;
                let halfRange = (currentZoomEnd - currentZoomStart) / 2;
                let newHalfRange = halfRange / factor;

                let newStart = Math.max(0, center - newHalfRange);
                let newEnd = Math.min(100, center + newHalfRange);

                if (newEnd - newStart < 2)
                    return;

                currentZoomStart = newStart;
                currentZoomEnd = newEnd;

                myChart.dispatchAction({
                    type: 'dataZoom',
                    start: currentZoomStart,
                    end: currentZoomEnd
                });
            }

            function resetZoomTimeline() {
                if (!myChart)
                    return;
                currentZoomStart = 0;
                currentZoomEnd = 100;

                myChart.dispatchAction({
                    type: 'dataZoom',
                    start: 0,
                    end: 100
                });
            }

            async function drawTimeline(custom = "") {
                const machines = [];
                var namas = [];
                var nama_mesin = "", count = 0;
                const statusColors = JSON.parse('<?= json_encode($status) ?>');
//                if (custom == "")
                await updateData(custom);

                await asData(custom).then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        if (sd.nama_mesin !== "") {
                            if (nama_mesin !== sd.nama_mesin) {
                                if (!namas.includes(sd.nama_mesin)) {
                                    count += 1;
                                    namas.push(sd.nama_mesin);
                                }
                            }
                            machines.push({nama: sd.nama_mesin, status: sd.status, warna: sd.warna_status, start: moment(sd.start).toDate(), end: moment(sd.end).toDate()});
                            nama_mesin = sd.nama_mesin;
                        }

                    });
                });
                const now = new Date();
                const endTimeLimit = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 9, 0, 0).getTime();
                const startTimeLimit = endTimeLimit - (24 * 60 * 60 * 1000);

                const data = [];


                machines.forEach((m) => {
                    const diffInMinutes = moment(m.end).diff(moment(m.start), 'minutes');
                    data.push({
                        name: statusColors[m.status]["stt"],
                        value: [m.nama, m.start, m.end, diffInMinutes * 60000],
                        itemStyle: {normal: {color: statusColors[m.status]["warna"]}}
                    });
                });



                const option = {
                    tooltip: {
                        formatter: function (params) {
//                            return params.marker + params.name + ': ' + Math.round(params.value[3] / 60000) + ' min';
                            return params.marker + params.name;
                        }
                    },
                    grid: {top: 5, bottom: 5, left: 30, right: 10, height: "<?= $heighContainer ?>", containLabel: true},
                    xAxis: {
                        type: 'time',
                        position: 'top',
                        splitLine: {show: true, lineStyle: {color: 'grey'}},
                        axisLabel: {color: '#999', fontSize: 12, formatter: '{HH}:{mm}'},
                        minorTick: {show: true, splitNumber: 2, length: 3},
                        minorSplitLine: {
                            show: true, // Menampilkan garis grid minor (opsional)
                            lineStyle: {
                                color: '#ddd', // Warna garis grid kecil
                                type: 'dashed'  // Tipe garis grid kecil
                            }
                        }

                    },
                    yAxis: {
                        data: namas,
                        splitLine: {show: true, lineStyle: {color: '#F0F0F0'}},
                        axisLine: {show: false},
                        axisTick: {show: false},
                        axisLabel: {fontWeight: 'bold', color: '#333', fontSize: 15},
                        triggerEvent: true
                    },
                    dataZoom: [
                        {
                            type: 'inside', // Enables mouse wheel and touch pinch zoom
                            xAxisIndex: 0, // Target the first X-axis
                            start: 0, // Initial start percentage (0%)
                            filterMode: 'weakFilter',
                            end: 100         // Initial end percentage (100%),
                        }
                    ],
                    series: [{
                            type: 'custom',
                            clickable: true,
                            renderItem: function (params, api) {
                                let categoryIndex = api.value(0);
                                let start = api.coord([api.value(1), categoryIndex]);
                                let end = api.coord([api.value(2), categoryIndex]);
                                let height = api.size([0, 1])[1] * 0.9; // Tinggi bar 60% dari baris

                                return {
                                    type: 'rect',
                                    shape: echarts.graphic.clipRectByRect({
                                        x: start[0], y: start[1] - height / 2,
                                        width: end[0] - start[0], height: height
                                    }, {
                                        x: params.coordSys.x, y: params.coordSys.y,
                                        width: params.coordSys.width, height: params.coordSys.height
                                    }),
                                    style: api.style()
                                };
                            },
                            itemStyle: {opacity: 0.9},
                            encode: {x: [1, 2], y: 0},
                            data: data,
                            triggerEvent: false
                        }]
                };
                loop = 0;
                myChart.clear();
                myChart.setOption(option, true);
                myChart.on('click', function (params) {
                    // Check if a data graphical element was clicked
                    if (typeof params.value === "string") {
                        window.open("<?= base_url("report/machinemonitoringv2/detail/{$dept}?mesin=") ?>" + params.value, "_blank");
                        // You clicked a specific bar, line symbol, etc.
                    }
                });
                $("#jumlahMesin").html(`&nbsp; (${namas.length} Mesin)`);
//                sets();
            }

            const sets = (() => {
                const svgElement = document.querySelector('#timeline_tricot svg');
                if (svgElement) {
                    svgElement.setAttribute('id', "timeline_tricot_svg");
                }
                const rectElement = svgElement.querySelector('rect');
                if (rectElement) {
                    rectElement.setAttribute('fill', "url('#timeline_tricot_svg')");
                }
            });

            // --- 4. Trend Chart (Line/Area) --
            async function drawTrendChart() {
                const chartDoms = document.getElementById('trend_chart');
                const myCharts = echarts.init(chartDoms);

                const dates = [];
                const values = [];
                var persens = 0;
                await asDataGrafik().then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        dates.push(moment(sd.tgl).format("DD MMM"));
                        var pr = (sd.running) / (getMinuteDate(sd.tgl, moment().toString()) * sd.count_mesin);
                        persens += pr;
                        values.push((pr * 100));
                    });
                });
                persens = persens.toFixed(2);
                $(".persen-util").html(`${persens}%`);
                const option = {
                    grid: {top: 10, bottom: 20, left: 30, right: 10},
                    tooltip: {
                        formatter: function (params) {
                            return `${params.marker} ${params.name} ${params.value.toFixed(2)} %`;
                        }
                    },
                    xAxis: {
                        type: 'category',
                        data: dates,
                        axisLabel: {fontSize: 8, interval: 4, color: '#999'},
                        axisLine: {show: false},
                        axisTick: {show: false}
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: {fontSize: 8, color: '#999'},
                        splitLine: {lineStyle: {type: 'dashed'}}
                    },
                    series: [{
                            data: values,
                            type: 'line',
                            smooth: true,
                            symbol: 'circle',
                            symbolSize: 6,
                            lineStyle: {width: 3, color: '#0d6efd'},
                            itemStyle: {color: '#0d6efd'},
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                    {offset: 0, color: 'rgba(13, 110, 253, 0.3)'},
                                    {offset: 1, color: 'rgba(13, 110, 253, 0)'}
                                ])
                            }
                        }]
                };

                myCharts.setOption(option);
                myCharts.on('click', function (params) {
                    // Check if a data graphical element was clicked
                    if (params.name) {
                        window.open("<?= base_url('report/machinemonitoringv2/detail?date=') ?>" + params.name, "_blank");
                        // You clicked a specific bar, line symbol, etc.
                    }
                });

            }

            // --- 5. Auto Scroll Logic ---
            const delay = ms => new Promise(resolve => setTimeout(resolve, ms));

            async function initAutoScroll() {
                const scrollContainer = document.getElementById('scroll-container');
                let isPaused = false;
                scrollContainer.addEventListener('mouseenter', () => isPaused = true);
                scrollContainer.addEventListener('mouseleave', () => isPaused = false);
                await delay(5000);
                setInterval(() => {
                    if (!isPaused) {
                        scrollContainer.scrollTop += 1;
                        // Reset ke atas jika sudah di ujung bawah
                        if (scrollContainer.scrollTop >= (scrollContainer.scrollHeight - scrollContainer.offsetHeight - 1)) {
                            isPaused = true;
                            setTimeout(() => {
                                scrollContainer.scrollTop = 0;
                                setTimeout(() => {
                                    isPaused = false;
                                }, 5000); // Tunggu di atas sebentar
                            }, 1000);
                        }
                    }
                }, 40);
            }

            const  asDataGrafik = ((day = - 30) => {
                return $.ajax({
                    type: "post",
                    data: {
                        dept: "<?= $dept ?>",
                        days: day
                    },
                    url: "<?php echo base_url(); ?>report/machinemonitoringv2/get_grafiks",
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });

            var ipSocket = "<?= $ip_socket ?>";
            const socket = new WebSocket(`${ipSocket}`);
            socket.onopen = function () {
                console.log("Connected to server");
            };

            const updateSummary = (() => {
                const myElements = document.querySelectorAll('.statuss');
                var stt = <?= $state ?>;
                const dt = typeof stt === "string" ? JSON.parse(stt) : stt;
                var data = {
                    mark_success: 0,
                    mark_warning: 0,
                    mark_danger: 0
                };
                myElements.forEach(element => {
                    var ee = element.getAttribute("data-status");
                    data[ee] += 1;
                    dt[ee] += 1;
                });
                Object.keys(data).forEach(key => {
                    $(`.sum-${key}`).attr("data-val", data[key]);
                    $(`.sum-${key}`).html(data[key]);
                });
                Object.keys(dt).forEach(key => {
                    $(`.stt-${key}`).html(dt[key]);
                });
            });


            const converMinute = ((minute) => {
                var value = minute;
                var units = {
                    "day": 24 * 60,
                    "hour": 60,
                    "min": 1
                };

                var result = [];

                if (minute >= 1440) {
                    const rtf = new Intl.RelativeTimeFormat("en", {numeric: "auto"});
                    result.push(rtf.format(0 - Math.floor(minute / 1440), "day"));
                } else {
                    for (var name in units) {
                        var p = Math.floor(value / units[name]);
                        if (p == 1)
                            result.push(" " + p + " " + name);
                        if (p >= 2)
                            result.push(" " + p + " " + name + "s");
                        value %= units[name];
                    }
                }
                return result;


            });

            var dataMesin = [];
            const updateContents = ((data) => {
                var stts = JSON.parse('<?= json_encode($status) ?>');
                $.each(data, function (index, val) {
                    var datas = $(`.card-d${val.devid}`).data();
                    if (datas) {
//                        updatePersenToday(datas.state);
                        if (dataMesin[`${val.devid}`] === undefined) {
                            dataMesin[`${val.devid}`] = {
                                state: parseInt(datas.state),
                                total: parseInt(datas.total),
                                totaldown: parseInt(datas.downtime),
                                durasi_down: parseInt(datas.durasi_down),
                                uptime: datas.uptime,
                                durasi_up: parseInt(datas.durasi_up)
                            };
                        }
                        var stt = datas.state;
                        dataMesin[`${val.devid}`].state = parseInt(val.state);
                        dataMesin[`${val.devid}`].total += 1;
                        dataMesin[`${val.devid}`].totaldown += 1;
                        var dtt = "";
//                        console.log(stts[val.state]);
                        var border = stts[val.state]["warna"];
                        switch (true) {
                            case (parseInt(val.state) != 1):
                                dataMesin[`${val.devid}`].downtime += 1;
                                dataMesin[`${val.devid}`].durasi_down += 1;
                                dataMesin[`${val.devid}`].uptime = 0;
                                dataMesin[`${val.devid}`].durasi_up = 0;
                                var dt = converMinute(dataMesin[`${val.devid}`].durasi_down);
                                dtt = "Stop : " + dt.join();
//                                    border = stt[dataMesin[`${val.devid}`].state]["warna"];
                                break;
                            default:
                                dataMesin[`${val.devid}`].uptime += 1;
                                dataMesin[`${val.devid}`].durasi_up += 1;
                                dataMesin[`${val.devid}`].downtime = 0;
                                dataMesin[`${val.devid}`].durasi_down = 0;
                                var dt = converMinute(dataMesin[`${val.devid}`].durasi_up);
                                dtt = "Running : " + dt.join();
                                break;
                        }
                        $(`.statuss-d${val.devid}`).html(stts[val.state]["stt"]);
                        $(`.statuss-d${val.devid}`).attr("data-status", val.state);
                        $(`.container-d${val.devid}`).css("backgroundColor", `${border}`);
//                        }
                        $(`.durasi-d${val.devid}`).html(dtt);
                        var ttlDwn = (dataMesin[`${val.devid}`].totaldown / dataMesin[`${val.devid}`].total) * 100;
                        $(`.down-d${val.devid}`).html(ttlDwn.toFixed(0));
                    }

                });
                updateSummary();
            });

            socket.onmessage = async function (event) {
                loop += 1;
                var data = JSON.parse(event.data);
                if (data["version"] != undefined && data["version"] == 2) {
                    updateContents(data["data"]);
                    if (loop > 20) {
                        if (daysTimeline === 0 && zoomsTimeline === false)
                            await drawTimeline();
                        await persenToday();
                    }
                }

            };
            var daysTimeline = 0;
            var zoomsTimeline = false;
        </script>
        <script>

            $(function () {

                drawTimeline("");
                drawTrendChart();
                if (countMesin > 10)
                    initAutoScroll();
                if ("<?= $dept ?>".toLowerCase() === "wrd") {
                    initTableDOM();
                    updateTimerValues();
                    setTimeout(() => {
                        startAutoScroll();
                    }, 5000);
                }

                updateClock();
                setInterval(() => {
                    updateClock();
                    if ("<?= $dept ?>".toLowerCase() === "wrd")
                        updateTimerValues();

                }, 1000);


                $(".sum-mark_danger").attr("data-val",<?= $sumRed ?>);
                $(".sum-mark_danger").html("<?= $sumRed ?>");

                $(".sum-mark_warning").attr("data-val",<?= $sumYel ?>);
                $(".sum-mark_warning").html("<?= $sumYel ?>");

                $(".sum-mark_success").attr("data-val",<?= $sumGr ?>);
                $(".sum-mark_success").html("<?= $sumGr ?>");

                persenToday();

                $(".reset-day-timeline").on("click", async function () {
                    daysTimeline = 0;
                    $("#timeline_tricot").addClass('linear-background');
                    await drawTimeline(1);
                    $(".capture_date").html("24 JAM");
                    $("#timeline_tricot").removeClass('linear-background');
                });
                $(".minus-day-timeline").on("click", async function () {
                    daysTimeline -= 1;
                    $("#timeline_tricot").addClass('linear-background');
                    await drawTimeline(1);
                    let str = moment().add(daysTimeline, "day").format("YYYY-MM-DD");
                    let fns = moment().add((daysTimeline + 1), "day").format("YYYY-MM-DD");
                    $(".capture_date").html(`${str} 07:00 - ${fns} 07:00`);
                    $("#timeline_tricot").removeClass('linear-background');
                });
                $(".add-day-timeline").on("click", async function () {
                    daysTimeline += 1;
                    if (daysTimeline > 0) {
                        $(".reset-day-timeline").trigger("click");
                        return;
                    }
                    $("#timeline_tricot").addClass('linear-background');
                    await drawTimeline(1);
                    let str = moment().add(daysTimeline, "day").format("YYYY-MM-DD");
                    let fns = moment().add((daysTimeline + 1), "day").format("YYYY-MM-DD");
                    $(".capture_date").html(`${str} 07:00 - ${fns} 07:00`);
                    $("#timeline_tricot").removeClass('linear-background');
                });

                myChart.on('dataZoom', function (event) {
                    const startPercent = event.batch ? event.batch[0].start : event.start;
                    const endPercent = event.batch ? event.batch[0].end : event.end;
                    if ((endPercent - startPercent) < 100)
                        zoomsTimeline = true;
                    else
                        zoomsTimeline = false;

                });



            });

        </script>

    </body>
</html>