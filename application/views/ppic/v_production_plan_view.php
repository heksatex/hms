<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HMS - Advanced PPIC Warping Scheduler (5-Day Timeline)</title>
        <link href="<?= base_url('dist/css/bs5/bs5.css'); ?>" rel="stylesheet">
        <link href="<?= base_url('dist/css/fontaws6.5.css'); ?>" rel="stylesheet">
        <link href="<?= base_url('dist/css/vis-timeline-graph2d.min.css'); ?>" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style>
            :root {
                --bg-body: #f1f5f9;
                --card-bg: #ffffff;
                --card-inner: #f8fafc;
                --text-main: #0f172a;
                --text-muted: #64748b;
                --teal: #0d9488;
                --teal-light: #ccfbf1;
                --gold: #d97706;
                --rose: #e11d48;
                --emerald: #059669;
                --sky: #0284c7;
                --border-color: #cbd5e1;
                --sidebar-width: 300px;
                --sidebar-width-collapsed: 15px;
            }

            body {
                background-color: var(--bg-body);
                color: var(--text-main);
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 0.8rem;
                min-height: 100vh;
                overflow-x: hidden;
            }

            .hms-header {
                background: #ffffff;
                border-bottom: 2px solid var(--teal);
                padding: 10px 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .panel-box {
                background-color: var(--card-bg);
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 12px;
                height: calc(100vh - 120px);
                display: flex;
                flex-direction: column;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .panel-title {
                font-weight: 700;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 10px;
                padding-bottom: 6px;
                border-bottom: 1px solid var(--border-color);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            /* MO LIST STYLES */
            .mo-scroll-container {
                overflow-y: auto;
                flex-grow: 1;
                padding-right: 4px;
            }

            .mo-card {
                background: var(--card-inner);
                border: 1px solid var(--border-color);
                border-left: 4px solid var(--sky);
                border-radius: 6px;
                padding: 8px 10px;
                margin-bottom: 8px;
                cursor: grab;
                transition: all 0.2s;
            }

            .mo-card:hover {
                border-color: var(--teal);
                background: #ffffff;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
                transform: translateX(3px);
            }

            .mo-card:active {
                cursor: grabbing;
            }

            .mo-card.dragging {
                opacity: 0.3;
            }

            .mo-code {
                font-family: 'JetBrains Mono', monospace;
                font-weight: 700;
                color: var(--teal);
                font-size: 0.82rem;
            }

            /* GANTT CHART STYLES */
            .gantt-wrapper {
                overflow-x: auto;
                overflow-y: auto;
                flex-grow: 1;
                background: #ffffff;
                border: 1px solid var(--border-color);
                border-radius: 6px;
                position: relative;
                cursor: grab;
            }

            .gantt-wrapper:active {
                cursor: grabbing;
            }



            .gantt-row {
                display: flex;
                border-bottom: 1px solid var(--border-color);
                min-height: 58px;
                position: relative;
                width: max-content;
            }



            .gantt-lane {
                display: flex;
                position: relative;
                background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px);
            }

            .gantt-lane.drag-over {
                background-color: var(--teal-light);
            }

            .ganti-lembar{
                background: linear-gradient(90deg, #0284c7, #2563eb)!important;
            }
            .bongkar-pasang{
                background: linear-gradient(90deg, #6c6c80, #484858)!important;
            }

            .mesin-bar {
                border-radius: 5px;
                width: 160px;
                height: 90px;
                /*background: linear-gradient(to right, rgba(128,128,128,1), rgba(128,128,128,0));*/
                border: 1px solid #1d4ed8;
                color: #0D0C0C;
                padding: 3px;
                font-size: 0.68rem;
                font-family: 'JetBrains Mono', monospace;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                box-shadow: 0 2px 4px rgba(0,0,0,0.12);
                white-space: nowrap;
                transition: transform 0.1s;
            }

            .gantt-bar {
                border-radius: 5px;
                background: linear-gradient(90deg, #198754, #198754);
                border: 1px solid #1d4ed8;
                color: #ffffff;
                padding: 3px 8px;
                font-size: 0.68rem;
                font-family: 'JetBrains Mono', monospace;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                box-shadow: 0 2px 4px rgba(0,0,0,0.12);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                /*cursor: grab;*/
                transition: transform 0.1s;
            }

            .gantt-bar:active {
                /*cursor: grabbing;*/
            }

            .gantt-bar:hover {
                transform: scale(1.01);
                border-color: var(--gold);
                z-index: 8;
            }

            .gantt-bar.collision {
                background: linear-gradient(90deg, #dc2626, #ef4444) !important;
                border-color: #991b1b !important;
                animation: pulse-border 1.5s infinite;
            }

            /* DRAG GHOST STYLING */
            #drag-ghost {
                position: absolute;
                top: -1000px;
                left: -1000px;
                height: 44px;
                background: linear-gradient(90deg, rgba(2, 132, 199, 0.85), rgba(37, 99, 235, 0.85));
                border: 2px dashed #f59e0b;
                border-radius: 5px;
                color: #ffffff;
                font-family: 'JetBrains Mono', monospace;
                font-size: 0.7rem;
                font-weight: bold;
                padding: 4px 8px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
                pointer-events: none;
                z-index: 9999;
            }

            @keyframes pulse-border {
                0%, 100% {
                    box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
                }
                50% {
                    box-shadow: 0 0 0 6px rgba(220, 38, 38, 0);
                }
            }

            .kpi-card {
                background: var(--card-inner);
                border: 1px solid var(--border-color);
                border-radius: 6px;
                padding: 6px 12px;
            }

            .zoom-controls {
                display: flex;
                align-items: center;
                gap: 4px;
                background: var(--card-inner);
                padding: 2px 6px;
                border-radius: 6px;
                border: 1px solid var(--border-color);
            }



            /* PRINT STYLES */
            @media print {
                body {
                    background: white !important;
                    font-size: 10pt;
                }
                .btn, .zoom-controls, .no-print, #mo-col {
                    display: none !important;
                }
                .panel-box {
                    height: auto !important;
                    border: none !important;
                    box-shadow: none !important;
                }
                .hms-header {
                    border-bottom: 2px solid black !important;
                    padding: 0 0 10px 0 !important;
                }
                #gantt-col {
                    width: 100% !important;
                    flex: 0 0 100% !important;
                    max-width: 100% !important;
                }
                .gantt-wrapper {
                    overflow: visible !important;
                    border: 1px solid #000 !important;
                }
                .no-print {
                    display: none !important;
                }

            }



            /* YARN DEPLETION MARKER IN GANTT */
            .yarn-depleted-marker {
                position: absolute;
                top: 0;
                height: 60px;
                bottom: 0;
                width: 2px;
                background-color: #e11d48;
                z-index: 9;
                pointer-events: none;
                z-index: 10;
            }

            .yarn-depleted-badge {
                position: absolute;
                top: 2px;
                transform: translateX(-50%);
                background: #e11d48;
                color: white;
                font-size: 0.6rem;
                font-weight: bold;
                padding: 2px 6px;
                border-radius: 4px;
                white-space: nowrap;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                z-index: 10;
            }


            /* Kontainer utama progress bar (latar belakang abu-abu) */
            .progress-container {
                width: 100%;
                background-color: #e0e0e0;
                position: relative;
                height: 10px;
                overflow: hidden;
                border-radius: 15px;
            }

            /* Isian progress bar (warna biru) */
            .progress-bar {/* Ubah angka ini untuk mengatur persentase */
                height: 100%;
                background-color: #2196F3;
                transition: width 0.5s ease-in-out;
                border-radius: 15px 0 0 15px;
            }

            /* Teks persentase di tengah-tengah kontainer */
            .progress-text {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;     /* Mengetengahkan vertikal */
                justify-content: center;    /* Mengetengahkan horizontal */
                color: #333333;             /* Warna teks */
                font-weight: bold;
                font-family: sans-serif;
                font-size: 10px;
            }




            .sidebar {
                width: var(--sidebar-width);
                height: 100vh;
                /*background: linear-gradient(135deg, #1a1c2e 0%, #16181f 100%);*/
                transition: all 0.3s ease;
            }

            .sidebar.collapsed {
                width: var(--sidebar-width-collapsed);
            }
            .main-content {
                margin-left: var(--sidebar-width);
                background-color: #f8f9fa;
/*                min-height: 100vh;
                padding: 20px;*/
                width: 100%;
                transition: all 0.3s ease;
            }

            .collapsed~.main-content {
                margin-left: var(--sidebar-width-collapsed);
            }

            .toggle-btn {
                position: absolute;
                right: -15px;
                top: 20px;
                background: linear-gradient(135deg, #1a1c2e 0%, #16181f 100%);
                border-radius: 50%;
                width: 30px;
                height: 30px;
                border: none;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
                z-index: 9999;
                cursor: pointer;
                color : white;
                transition: transform 0.3s ease;
            }

            .collapsed .toggle-btn {
                transform: rotate(180deg);
            }

            .collapsed .hide-on-collapse {
                opacity: 0;
                visibility: hidden;
            }
            
            .dddr {
                z-index: -1 !important;
            }

        </style>
    </head>

    <body>
        <!-- ELEMENT GHOST DRAG -->
        <div id="drag-ghost">
            <span id="ghost-id">MO</span>
            <span id="ghost-dur" class="text-warning">0m</span>
        </div>
        <!-- HEADER & KPI STRIP -->
        <header class="hms-header  no-print">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div class="fw-bold fs-6 text-dark"><i class="fa-solid fa-timeline text-teal me-2"></i>PRODUCTION PLANNING - WARPING DASAR</div>
                    
                </div>
                
            </div>
            <!-- METRICS STRIP -->
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="kpi-card d-flex justify-content-between align-items-center">
                        <span class="text-muted xsmall font-weight-bold">Total Antrean MO Terjadwal</span>
                        <span class="fw-bold text-dark" id="kpi-backlog">0 MO</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card d-flex justify-content-between align-items-center">
                        <span class="text-muted xsmall font-weight-bold">Rata-rata Utilitas Mesin</span>
                        <span class="fw-bold text-teal" id="kpi-utilization">0%</span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="kpi-card d-flex justify-content-between align-items-center">
                        <span class="text-muted xsmall font-weight-bold">Total Output Terjadwal</span>
                        <span class="fw-bold text-primary" id="kpi-output">0 Mtr</span>
                    </div>
                </div>
            </div>
        </header>
        <div class="d-flex p-2">
                <div class="container-fluid">
                    <div class="panel-title text-dark">
                        <span>
                            <div class="input-group flex-nowrap">
                                <span  class="input-group-text dddr" id="addon-wrapping"><i class="fa-solid fa-chart-gantt text-teal me-2"></i></span>
                                <input type="text" class="form-control" name="tanggal" id="tanggal">
                            </div>
                        </span>
                        <div>
                            <span style="color: #1B13F5" onclick="refreshVis()"><i class="fa-solid fa-refresh text-warning me-2"></i>Refresh</span>
                        </div>

                        <!-- CONTROLS ZOOM (25% s/d 200%) -->
                        <div class="zoom-controls">
                            <span class="xsmall text-muted me-1 fw-bold">Zoom:</span>
                            <button class="btn btn-xs btn-outline-secondary py-0 px-2 zoomout" title="Zoom Out (-10%)"><i class="fa-solid fa-minus"></i></button>
                            <div class="input-group">
                                <input type="text" readonly id="zoom-level-text"  class="form-control form-control-sm" style="max-width: 40px;" value="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <!--<span class="xsmall fw-bold px-1 text-teal" id="zoom-level-text">0%</span>-->
                            <button class="btn btn-xs btn-outline-secondary py-0 px-2 zoomin"title="Zoom In (+10%)"><i class="fa-solid fa-plus"></i></button>
                            <!--<button class="btn btn-xs btn-outline-dark py-0 px-2 ms-1" onclick="resetZoom()" title="Reset Zoom (100%)"><i class="fa-solid fa-compress"></i></button>-->
                        </div>
                    </div>
                    <div id="visualization"></div>
                </div>
            
        </div>

    </div>
    <?php $this->load->view("admin/_partials/js.php") ?>
    <script src="<?= base_url('dist/js/bs5/bs5.bundle.min.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('dist/js/vis-timeline-graph2d.min.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('dist/js/html2canvas.js') ?>"></script>
    <script type="text/javascript">

                                    //            const LOCAL_STORAGE_KEY = "hms_productionplan_schedule_data";
                                    var startDate = moment().subtract(1, "day").startOf('day').utcOffset('+07:00');
                                    var endDate = moment().add(3, "day").endOf('day').utcOffset('+07:00');
                                    let moData = [], mesinData = [], mesinDataVis = [], breaksDef, breaks = [], breaksStatus = {}, moDataCount = 0;
                                    var timeline;
                                    var items = new vis.DataSet();
                                    var containerVis = document.getElementById('visualization');
                                    // TIMELINE CONFIG: 5 HARI TOTAL (120 JAM)
                                    const TOTAL_DAYS = 5;
                                    const TOTAL_HOURS = TOTAL_DAYS * 24; // 120 Jam
                                    const START_OFFSET_DAYS = -1; // Kemarin (-1 Hari)

                                    // UKURAN BASE & STATE ZOOM (%)
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
                                    // CALCULATE DURATION WITH DYNAMIC SETUP TIME
                                    function calculateDurationMinutes(qty, isSameProductAsPrevious = false) {
                                        const speedMetersPerMin = 700;
                                        const setupTimeMinutes = isSameProductAsPrevious ? 15 : 45; // Setup lebih cepat jika produk sama
                                        return Math.round((Number(qty) / speedMetersPerMin) + setupTimeMinutes);
                                    }

                                    
                                    const getItems = (() => {
                                        return  $.ajax({
                                            url: "<?php echo base_url(); ?>ppic/productionplanning/get_items",
                                            type: "POST",
                                            data: {
                                                dept: "<?= $dep ?>",
                                                start: startDate.format("YYYY-MM-DD").toString(),
                                                end: endDate.format("YYYY-MM-DD").toString()
                                            }

                                        });
                                    });
                                    const MIN_ZOOM_MS = 1000 * 60 * 60; // 1 Hour (100% Zoom / closest look)
                                    const MAX_ZOOM_MS = 1000 * 60 * 60 * 48; // 1 Year (0% Zoom / widest look)
                                    const setTimeline = (() => {
                                        //moment.tz("America/New_York"); 
                                        var options = {
                                            format: {
                                                majorLabels: function (date, scale, step) {
                                                    return moment(date).locale("id").format('dddd, DD MMMM YYYY');
                                                }
                                            },
                                            maxHeight: '100VH',
                                            verticalScroll: true,
                                            zoomKey: 'ctrlKey',
                                            stack: false,
                                            editable: false,
                                            start: startDate.toDate(),
                                            end: endDate.toDate(),
                                            min: startDate.toDate(), // Cannot scroll before today
                                            max: endDate.toDate(),
                                            zoomMin: MIN_ZOOM_MS, // Min zoom: 1 hour
                                            zoomMax: MAX_ZOOM_MS, // Max zoom: 24 hours
                                            stackSubgroups: false,
                                            orientation: "top",
                                            template: function (it, element, data) {
                                                let ctn = "";
                                                switch (it.tipe) {
                                                    case "box" :
                                                        let style = "";
                                                        if (it.status == 'draft')
                                                            style = " background : #B29E1E;";
                                                        ctn = `<div class="gantt-bar" style='${style}'>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                       <a href="<?= base_url('manufacturing/mO/edit') ?>/${it.enc_kode}" target="_blank">
                                                            <span style="color:#ffffff; text-decoration:underline; text-decoration-color: #000000;">${it.kode}</span>
                                                        </a>
                                                    <span style="font-size:0.6rem; opacity:0.9;" title="Waktu Operasi">${it.start_time} - ${it.finish_time}</span>
                                                        </div>
                                                        <div class="text-truncate fw-semibold" style="font-size:0.65rem;" title="${it.nama_produk}">${it.nama_produk}</div>
                                                        <div class="d-flex justify-content-between align-items-center text-light" style="font-size:0.6rem;">
                                                        <span><i class="fa-solid fa-cube me-1"></i>${Number(it.qty).toLocaleString()} ${it.uom}</span>
                                                            <span class="badge bg-dark bg-opacity-50 text-warning" title="">
                                                        <i class="fa-regular fa-clock me-1"></i>${it.duration}
                                                    </span>
                                                    </div>
                                                            
                                                            </div>
                                                            <div class="progress-container">
                                                                                <div class="progress-bar" style="width:${it.progress}%"></div>
                                                                                <div class="progress-text">${it.progress}%</div>
                                                                            </div>
                                                                                `;
                                                        break;
                                                    case "break-bng":
                                                        ctn = `<div class="yarn-depleted-marker">
    
                                                            </div>
                                                            <div class="yarn-depleted-badge">
                                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>YARN DEPLETED
                                                            </div>`;
                                                        break;
                                                    default :
                                                        let nm = it.nama_produk.toLowerCase();
                                                        nm = nm.replaceAll(" ", "-");
                                                        ctn = `<div class="gantt-bar ${nm}" title="${it.nama_produk}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold">${it.nama_produk}</span>
                                                                </div>
                                                 `;
                                                }
                                                return ctn;
                                            },
                                            groupTemplate: function (gr) {
                                                return `<div class="mesin-bar" >
                                                          <div class="d-flex justify-content-between align-items-center">
                                                          <span class="text-truncate fw-bold">${gr.content}</span>
                                                               
                                                                  </div>
                                                                  
                                                                <div class="text-truncate fw-semibold" style="font-size:0.65rem;" title="${gr.benang}">${gr.benang}</div>
                                                                  <div>
                                                                    <i class="fa-solid fa-clock me-1"></i><span class="fw-bold">${(gr.benang !== '') ? gr.time:''}</span>
                                                                    </div>
                                                                <div class="d-flex justify-content-between align-items-center" style="font-size:0.6rem;">
                                                                <span><i class="fa-solid fa-cube me-1"></i>${gr.qty} KG &nbsp;</span>
                                                                <span class="text-muted" id="load-${gr.id}">0%</span>    
                                                                    
                                                                          </span>
                                                    </div>
                                                            </div>
                                                                    `;
                                            },
                                            onAdd: async function (item, callback) {
                                                item.end = moment(item.start).add(item.total_minute, "minutes");
                                                item.start = moment(item.start);
                                                var overlaps = await overlapCheck(item);
                                                if (overlaps.length > 0) {
                                                    alert_notify("fa fa-warning", "Time slot is already booked!", "danger", function () {}, 500);
                                                    callback(null); // Cancel the drag/move action
                                                } else {
                                                    switch (item.tipe) {
                                                        case "box":
                                                            const resp = await save({
                                                                mcid: [item.group],
                                                                kode: [item.kode],
                                                                start: [item.start.format("YYYY-MM-DD HH:mm:ss").toString()],
                                                                finish: [item.end.format("YYYY-MM-DD HH:mm:ss").toString()],
                                                                start_old: [item.start_time],
                                                                finish_old: [item.finish_time],
                                                                mcid_old: [item.mc_id],
                                                                total_minute: [item.total_minute],
                                                                tipe: item.tipe,
                                                                nama: [item.nama_produk]
                                                            });
                                                            items.remove(item.kode);
                                                            items.add({
                                                                id: item.kode,
                                                                enc_kode: resp.enc,
                                                                progress: 0,
                                                                group: item.group,
                                                                start: item.start.toDate(),
                                                                end: item.end.toDate(),
                                                                nama_produk: item.nama_produk,
                                                                uom: item.uom,
                                                                qty: item.qty,
                                                                duration: item.total_minute,
                                                                kode: item.kode,
                                                                start_time: item.start.toString(),
                                                                finish_time: item.end.toString(),
                                                                tipe: item.tipe,
                                                                qty_target: item.qty_target,
                                                                status : item.status

                                                            });
                                                            break;
                                                        default:
                                                            await save({
                                                                mcid: [item.group],
                                                                kode: [item.id],
                                                                start: [item.start.format("YYYY-MM-DD HH:mm:ss").toString()],
                                                                finish: [item.end.format("YYYY-MM-DD HH:mm:ss").toString()],
                                                                total_minute: [item.total_minute],
                                                                tipe: item.tipe,
                                                                nama: [item.nama_produk]
                                                            });
                                                            items.add({
                                                                group: item.group,
                                                                start: item.start.toDate(),
                                                                end: item.end.toDate(),
                                                                nama_produk: item.nama_produk,
                                                                duration: item.total_minute,
                                                                tipe: item.tipe,
                                                                id: item.id,

                                                            });
                                                    }
//                                                        callback(item);
                                                    timeline.redraw();
                                                    $(`#card-${item.kode}`).removeClass('dragging');
                                                    loadBebanMesin();
                                                }

                                            },
                                            onRemove: async function (item, callback) {
                                                item.kode = item.id;
                                                if (item.tipe === 'break-bng')
                                                    return false;
                                                remove({kode: [item.kode]}).then(async rst => {
                                                    //                                                    await moList();
                                                    callback(item);
//                                                        refreshVis();
                                                    loadBebanMesin();
                                                }).catch((e) => {
                                                    callback(null);
                                                });
                                            },
                                            onMove: async function (item, callback) {
                                                if (item.tipe === 'break-bng') {
                                                    callback(null);
                                                    return false;
                                                }
                                                var start = moment(item.start);
                                                var end = moment(item.end);
                                                let diffMinute = end.diff(start, "minutes");
                                                item.start_time = moment(item.start).format("YYYY-MM-DD HH:mm:ss").toString();
                                                item.finish_time = moment(item.end).format("YYYY-MM-DD HH:mm:ss").toString();
                                                item.total_minute = diffMinute;
                                                var overlaps = await overlapCheck(item);
                                                if (overlaps.length > 0) {
                                                    alert_notify("fa fa-warning", "Time slot is already booked!", "danger", function () {}, 500);
                                                    callback(null); // Cancel the drag/move action
                                                } else {
                                                    item.kode = item.id;
                                                    await update(item).then(async rst => {
                                                        callback(item);
                                                        //                                                                items.update(item);
//                                                            refreshVis();
//                                                            updateKPIs();
                                                    }).catch(e => {
                                                        callback(null);
                                                    });
                                                }
                                            }

                                        };
                                        // Create a Timeline
                                        timeline = new vis.Timeline(containerVis, items, options);
                                        timeline.setGroups(mesinDataVis);
                                        timeline.moveTo(new Date(), {animation: false});
                                        setTimelineItems();
                                        timeline.redraw();
                                        timeline.on('rangechanged', () => {
                                            const currentWindow = timeline.getWindow();
                                            const currentDuration = currentWindow.end.valueOf() - currentWindow.start.valueOf();
                                            let calculatedFactor = (MAX_ZOOM_MS - currentDuration) / (MAX_ZOOM_MS - MIN_ZOOM_MS);

                                            // Constrain boundaries between 0 and 100
                                            let percentage = Math.max(0, Math.min(100, calculatedFactor * 100));
                                            percentage = percentage.toFixed(),
                                                    document.getElementById('zoom-level-text').value = `${percentage}`;
                                        });

                                    });
                                    const getBreaks = ((it) => {
                                        const gets = breaks.filter((brk, idx) => {
                                            if (brk.group !== it.mc_id) {
                                                return false;
                                            }

                                            var str = moment(it.start_time);
                                            var end = moment(it.finish_time);
                                            var p = JSON.parse(breaksDef);
                                            if (moment(p[idx].start) < end) {
                                                var mntEnd = hitungHabisBng(str, end, it.qty_target, brk.qty_max);
                                                if (brk.qty_max > 0) {
                                                    brk.qty_max -= it.qty_target;
                                                    if (brk.qty_max < 0) {
                                                        brk.start = str.add(mntEnd, "minute");
                                                        return true;
                                                    }
                                                }
                                            }
                                            return false;
                                        });
                                        return gets;
                                    });
                                    const overlapCheck = ((item) => {
                                        if (item.tipe === 'break-bng')
                                            return false;
                                        var overlaps = items.get({
                                            filter: function (exs) {
                                                if (exs.tipe === 'break-bng')
                                                    return false;
                                                if (exs.id === item.id)
                                                    return false;
                                                if (exs.group !== item.group)
                                                    return false;
                                                exs.end = moment(exs.end);
                                                exs.start = moment(exs.start);
                                                if (item.end > exs.start && item.end < exs.end) {
                                                    return true;
                                                }
                                                if (item.start > exs.start && item.start < exs.end) {
                                                    return true;
                                                }
                                                if (exs.start > item.start && exs.start < item.start) {
                                                    return true;
                                                }
                                                if (exs.end > item.start && exs.end < item.end) {
                                                    return true;
                                                }

                                                //                                                                exs.start < item.end &&
                                                //                                                                exs.end > item.start

                                            }
                                        });
                                        return overlaps;
                                    });
                                    const setTimelineItems = (async () => {
                                        items.clear();
                                        await getItems().then(resp => {
                                            resp.data.forEach(it => {
                                                var persenProgress = 0;
                                                if (it.tipe === 'box') {
                                                    let hsl = (it.qty_hasil / it.qty) * 100;
                                                    persenProgress = hsl.toFixed();
                                                }
                                                let drt = converMinute(it.total_minute);
                                                items.add({
                                                    id: it.kode,
                                                    enc_kode: it.enc_kode,
                                                    progress: persenProgress,
                                                    group: it.mc_id,
                                                    start: it.start_time,
                                                    end: it.finish_time,
                                                    nama_produk: it.nama_produk,
                                                    uom: it.uom,
                                                    qty: it.qty,
                                                    duration: drt,
                                                    kode: it.kode,
                                                    start_time: it.start_time,
                                                    finish_time: it.finish_time,
                                                    total_minute: it.total_minute,
                                                    tipe: it.tipe,
                                                    qty_target: it.qty_target,
                                                    status: it.status
                                                });

                                                if (it.tipe === 'box') {
                                                    getBreaks(it).forEach(brk => {
                                                        items.add({
                                                            id: brk.id,
                                                            group: brk.group,
                                                            start: brk.start,
                                                            tipe: brk.tipe
                                                        });
                                                        breaksStatus[brk.group] = brk.start.format("YYYY-MM-DD HH:mm").toString();
                                                    });
                                                }
                                            });
                                        });
                                        //                                                breaks.forEach(brk => {
                                        //
                                        //                                                });
                                        loadBebanMesin();
                                    });
                                    const hitungHabisBng = ((start, end, kg_target, qty_max) => {
                                        var minutesPassed = end.diff(start, 'minutes');
                                        var kgPerMenit = kg_target / minutesPassed;
                                        kgPerMenit = kgPerMenit.toFixed(2);
                                        var mnt = qty_max / kgPerMenit;
                                        //                                        console.log(`minute : ${minutesPassed} , kg/menit : ${kgPerMenit}, hasil : ${mnt}`);
                                        return Math.round(mnt);
                                    });

//                                        const setMes
                                    function populateMachineFilter() {
                                        const filterEl = document.getElementById('filterMachine');
                                        if (!filterEl)
                                            return;
                                        filterEl.innerHTML = '<option value="">Semua MC Target</option>';
                                        mesinDataVis.forEach(mc => {
                                            filterEl.innerHTML += `<option value="${mc.id}">${mc.content}</option>`;
                                        });
                                        filterEl.innerHTML += `<option value="UNASSIGNED">UNASSIGNED</option>`;
                                    }

                                    const getMesin = (() => {
                                        $.ajax({
                                            url: "<?php echo base_url(); ?>ppic/productionplanning/get_mesin",
                                            type: "GET",
                                            data: {
                                                dept: "<?= $dep ?>",
                                            },
                                            success: function (data) {
                                                mesinData = JSON.parse(JSON.stringify(data.data));
                                                mesinDataVis = [];
                                                breaks = [];
                                                mesinData.forEach(mc => {
                                                    mesinDataVis.push({
                                                        id: mc.mc_id,
                                                        content: mc.nama_mesin,
                                                        time: mc.time,
                                                        benang: mc.benang,
                                                        est: mc.estimasi,
                                                        qty: mc.qty
                                                    });
                                                    if (mc.benang != "") {
                                                        breaks.push({
                                                            group: mc.mc_id,
                                                            start: moment(mc.time),
                                                            tipe: "break-bng",
                                                            content: "YARN DEPLETED",
                                                            nm: "YARN DEPLETED",
                                                            id: mc.ids,
                                                            qty_max: parseFloat(mc.qty)
                                                        });
                                                    }
                                                });
                                                breaksDef = JSON.stringify([...breaks]);
                                                refreshVis();
                                                populateMachineFilter();
                                                //                        renderGanttLanes();
                                            }

                                        });
                                    });
                                    const save = ((data) => {
                                        return new Promise((resolve, reject) => {
                                            $.ajax({
                                                url: "<?php echo base_url(); ?>ppic/productionplanning/save_plan",
                                                type: "post",
                                                data: data,
                                                success: (response) => resolve(response),
                                                error: (error) => reject(0)

                                            });
                                        });
                                    });
                                    const update = ((data) => {
                                        return new Promise((resolve, reject) => {
                                            $.ajax({
                                                url: "<?php echo base_url(); ?>ppic/productionplanning/update_plan",
                                                type: "post",
                                                data: data,
                                                success: (response) => resolve(1),
                                                error: (error) => reject(0)

                                            });
                                        });
                                    });
                                    const remove = ((data) => {
                                        return new Promise((resolve, reject) => {
                                            $.ajax({
                                                url: "<?php echo base_url(); ?>ppic/productionplanning/rmv_plan",
                                                type: "post",
                                                data: data,
                                                success: (response) => resolve(1),
                                                error: (error) => reject(0)

                                            });
                                        });
                                    });
                                    const dbounce = ((func, delay) => {
                                        let timeout;
                                        return function (...args) {
                                            clearTimeout(timeout);
                                            timeout = setTimeout(() => {
                                                func.apply(this, args);
                                            }, delay);
                                        };
                                    });
                                    function dragEnd(e) {
                                        e.target.classList.remove('dragging');
                                    }

                                    function zoomTimeline(deltaPercent) {
                                        var crt = $("#zoom-level-text").val();
                                        crt = parseInt(crt);
                                        currentZoomLevel = Math.max(0, Math.min(100, crt + deltaPercent));
//                                            document.getElementById('zoom-level-text').innerText = `${currentZoomLevel}%`;
                                        zoomTimelineToPercentage(currentZoomLevel);
                                    }

                                    function resetZoom() {
                                        //                                        timeline.fit({animation: true});
                                        timeline.moveTo(new Date(), {
                                            scale: 0.5,
                                            animation: {
                                                duration: 1000, // 1 second animation
                                                easingFunction: 'easeInOutQuad'
                                            }
                                        });
                                        document.getElementById('zoom-level-text').innerText = '100%';
                                        //                                        renderGanttHeader();
                                        //                renderGanttLanes();
                                    }
                                    function dragStartTimeBreak(e) {
                                        var data = e.target.dataset;
                                        data.content = "New Task";
                                        e.dataTransfer.setData('text', JSON.stringify(data));
                                        e.target.classList.add('dragging');
                                    }

                                    function dragStart(e, moId) {
                                        const mo = moData.find(m => m.kode === moId);
                                        var data = e.target.dataset;
                                        data.content = "New Task";
                                        data.kode = moId;
                                        e.dataTransfer.setData('text', JSON.stringify(data));
                                        e.target.classList.add('dragging');
                                    }
                                    async function resetAllData() {
                                        const dts = timeline.itemsData.get();
                                        const mo = dts.map(dt => dt.kode);
                                        if (mo.length > 0) {
                                            if (confirm('Apakah Anda yakin ingin mengosongkan semua jadwal dan mengembalikan ke data awal?')) {
                                                let rmv = await remove({kode: mo});
                                                if (rmv == 1) {
                                                    items.clear();
                                                    timeline.redraw();
                                                }

                                                //                                                renderGanttBars();
                                            }

                                        }

                                    }

                                    function updateKPIs() {
                                        const unassignedCount = moDataCount;
                                        document.getElementById('mo-count').innerText = `${unassignedCount} MO`;
//                                        document.getElementById('kpi-backlog').innerText = `${unassignedCount} MO`;
//                                        let totalScheduledMinutes = 0;
//                                        let totalOutputMtr = 0;
//                                        moData.filter(m => m.mc != "").forEach(m => {
//                                            totalScheduledMinutes += parseInt(m.total_minute);
//                                            totalOutputMtr += Number(m.qty);
//                                        });
//                                        const avgUtilization = Math.round((totalScheduledMinutes / (TOTAL_HOURS * 60 * mesinData.length)) * 100);
//                                        document.getElementById('kpi-utilization').innerText = `${avgUtilization}%`;
//                                        document.getElementById('kpi-output').innerText = `${totalOutputMtr.toLocaleString()} Mtr`;
                                    }

                                    const loadBebanMesin = (() => {
                                        var startDate = $('#tanggal').data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm');
                                        var endDate = $('#tanggal').data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm');
                                        var pjg = moment(endDate).diff(moment(startDate), "minute");
                                        var totalMesin = mesinDataVis.length;
                                        var ratamesin = 0;
                                        mesinData.forEach(mc => {
                                            var itm = items.get({
                                                filter: function (item) {
                                                    return item.group === mc.mc_id && item.tipe === 'box';
                                                }
                                            });
                                            var totalMinute = 0;
                                            itm.forEach(it => {
                                                var mnt = moment(it.end).diff(moment(it.start), "minute");
                                                totalMinute += mnt;
                                            });
                                            var hsl = (totalMinute / pjg) * 100;
                                            ratamesin += hsl;
                                            $(`#load-${mc.mc_id}`).html(hsl.toFixed(2) + "%");
                                        });
                                        var totalMeter = 0;
                                        var totalMo = items.get({
                                            filter: function (item) {
                                                if (item.tipe === 'box') {
                                                    totalMeter += parseInt(item.qty);
                                                    return true;
                                                }
                                            }
                                        });
                                        document.getElementById('kpi-backlog').innerText = `${totalMo.length} MO`;
                                        var rata2mesin = ratamesin / totalMesin;
                                        document.getElementById('kpi-utilization').innerText = `${rata2mesin.toFixed(2)}%`;
                                        document.getElementById('kpi-output').innerText = `${totalMeter.toLocaleString()} Mtr`;
                                    });
                                    const exportToExcel = (() => {
                                        $.ajax({
                                            url: "<?php echo base_url(); ?>ppic/productionplanning/export_excel",
                                            type: "post",
                                            data: {
                                                dept: "<?= $dep ?>",
                                                start: startDate.format("YYYY-MM-DD").toString(),
                                                end: endDate.format("YYYY-MM-DD").toString()
                                            },
                                            success: function (resp) {
                                                const link = document.createElement('a');
                                                link.download = resp?.text_name;
                                                link.href = resp?.data;
                                                link.click();
                                            }

                                        });
                                    });
                                    const exportPdf = (() => {
                                        $.ajax({
                                            url: "<?= base_url('ppic/productionplanning/export_pdf') ?>",
                                            type: "POST",
                                            beforeSend: function (xhr) {
                                                please_wait(function () {});
                                            },
                                            data: {
                                                dept: "<?= $dep ?>",
                                                start: startDate.format("YYYY-MM-DD").toString(),
                                                end: endDate.format("YYYY-MM-DD").toString()
                                            },
                                            success: function (data) {
                                                unblockUI(function () {});
                                                window.open(data.url, "_blank").focus();
                                            },
                                            error: function (req, error) {
                                                unblockUI(function () {
                                                    setTimeout(function () {
                                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                                    }, 500);
                                                });
                                            }
                                        });
                                    });
                                    
                                    const statusBenang = ((dataMc) => {
                                        if (breaksStatus[dataMc] == undefined)
                                            return '<span class="badge bg-success">Stok Mencukupi</span>';
                                        else
                                            return '<span class="badge bg-danger">Stok Tidak Mencukupi</span>';
                                    });
                                    const refreshVis = (() => {
                                        $("#visualization").html("");
                                        breaks = JSON.parse(breaksDef);
                                        setTimeline();
                                        //                                        timeline.redraw();
                                    });
                                    const setDateForm = ((cls) => {
                                        $(cls).daterangepicker({
                                            singleDatePicker: true,
                                            showDropdowns: true,
                                            minYear: 1901,
                                            maxYear: parseInt(moment().format('YYYY'), 10),
                                            locale: {
                                                format: 'YYYY-MM-DD HH:mm'
                                            },
                                            timePicker: true,
                                            timePicker24Hour: true
                                        });
                                    });

                                    const zoomTimelineToPercentage = ((percentage) => {
// Normalize percentage to a 0-1 scale
                                        const factor = percentage / 100;

                                        // Calculate the target window duration (Interval decreases as percentage increases)
                                        const targetDuration = MAX_ZOOM_MS - (factor * (MAX_ZOOM_MS - MIN_ZOOM_MS));

                                        // Get current window states to maintain focus center
                                        const currentWindow = timeline.getWindow();
                                        const centerTime = (currentWindow.start.valueOf() + currentWindow.end.valueOf()) / 2;

                                        // Compute the new start and end timestamps centered on the current view
                                        const newStart = centerTime - (targetDuration / 2);
                                        const newEnd = centerTime + (targetDuration / 2);

                                        // Apply the calculated window programmatically
                                        timeline.setWindow(newStart, newEnd, {animation: false});
                                    });


                                    $(function () {
                                        $('#tanggal').daterangepicker({
                                            //                    autoUpdateInput: false,
                                            startDate: startDate,
                                            endDate: endDate,
                                            locale: {
                                                format: 'YYYY-MM-DD'
                                            }
                                        });
                                        $('#tanggal').on('apply.daterangepicker', function (ev, picker) {
                                            startDate = picker.startDate;
                                            //                                            startDate = moment()picker.startDate;
                                            endDate = picker.endDate;
                                            refreshVis();
                                            loadBebanMesin();
                                            //                                            console.log(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                                        });
                                        getMesin();
                                        $(".zoomin").on("click", function () {
//                                                timeline.zoomIn(0.5);
                                            zoomTimeline(10);
                                        });
                                        $(".zoomout").on("click", function () {
//                                                timeline.zoomOut(0.5);
                                            zoomTimeline(-10);
                                        });
                                    });
    </script>
</body>
</html>