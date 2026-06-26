<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>HMS - <?= $departmen->nama ?? "" ?> OEE Dashboard (ECharts Version)</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= base_url('dist/css/slider.css'); ?>" />
        <!-- Menggunakan ECharts CDN -->
        <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
        <style>
            :root {
                --bg-light: #f4f7fa;
                --card-light: #ffffff;
                --primary-text: #212529;
                --muted-text: #6c757d;
                --accent-blue: #0d6efd;
                --status-green: #198754;
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
                font-size: 1.1rem;
            }
            .brand {
                font-size: 1rem;
                letter-spacing: 2px;
                font-weight: 800;
                color: var(--muted-text);
            }
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
                border-radius: 8px;
                padding: 8px 15px;
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
                padding: 20px;
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
                height: 1200px;
            }

            .legend-pill {
                padding: 4px 12px;
                border-radius: 5px;
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
            }
            @media screen and (max-width:1000px) {
                .card {
                    width: 12%;
                }
            }
            img.center {
                display: block;
                margin-left: auto;
                margin-right: auto;
                width: 50%;
            }
            .durasi-text {
                font-size: 11px;
            }
        </style>
    </head>
    <body>

        <div class="live-bar d-flex justify-content-between align-items-center">
            <div class="brand"><?= $departmen->nama ?? "" ?> - MACHINE MONITORING</div>
            <div class="timestamp" id="realtime-clock">LOADING...</div>
        </div>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
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
                                                foreach ($status as $key => $value) {
                                                    ?>
                                                    <div class="col-3"><div class="counter-pill" style="color: <?= $value['warna'] ?>;border-color: <?= $value['warna'] ?>">
                                                            <div class="c-num stt-<?= $key ?>">0</div><div class="c-lab"><?= $value["stt"] ?></div>

                                                        </div>
                                                    </div>
                                                    <?php
                                                }
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-uppercase m-0" style="font-size: 0.85rem; font-weight:800; color:var(--muted-text)">CAPTURE 24 JAM</h5>
                                <div class="d-flex">
                                    <?php foreach ($status as $key => $value) {
                                        ?>
                                        <div class="legend-pill" style="background:<?= $value['warna'] ?>; color:#ffffff"><?= $value["stt"] ?></div>
                                    <?php }
                                    ?>
                                </div>
                            </div>
                            <div id="scroll-container">
                                <div id="timeline_tricot"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="col-md-12">
                        <div id="scroll-containers">
                            <?php
                            $exist = [];
                            $sumRed = 0;
                            $sumYel = 0;
                            $sumGr = 0;
                            foreach ($mesin as $key => $value) {
                                $durasis = $durasi["d{$value->devid}"];
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
                                <div class="card card-<?= "d{$value->devid}" ?>" data-durasi_up="<?= $durasis->total_up ?>" data-durasi_down="<?= $durasis->total_down ?>"
                                     data-state="<?= $value->state ?>" data-totaldown = "<?= $ttlDwn ?>" data-total="<?= $value->total ?>" data-uptime="<?= $ttlUp ?>" data-downtime="<?= $ttlDwn ?>"
                                     style="border: 1px solid black;">
                                    <div class="container container-<?= "d{$value->devid}" ?>" style="height: 20%; background-color: <?= $border ?>; color:#ffffff">
                                        <b><?= "{$value->nama_mesin}" ?></b>
                                    </div>
                                    <img class="img-<?= "d{$value->devid}" ?> center statuss" data-status="<?= $logo ?>" src="<?= base_url("dist/img/{$logo}.png") ?>" alt="Avatar">
                                    <div class="container" style="word-wrap: break-word;">
                                        <p title="Downtime Shift Sekarang">Downtime : <span class="down-<?= "d{$value->devid}" ?>"><?= round(($ttlDwn / $value->total) * 100) ?></span> %</p>
                                        <span class="durasi-text durasi-<?= "d{$value->devid}" ?>"><?= ($value->state == 0) ? "Running : {$durasis->total_up_text}" : "Stop : {$durasis->total_down_text}" ?></span>
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
        <script type="text/javascript" src="<?= base_url('dist/js/sliders.js') ?>"></script>
        <script type="text/javascript">




            // --- 1. Realtime Clock ---
            function updateClock() {
                const now = new Date();
                const options = {weekday: 'short', day: 'numeric', month: 'short'};
                document.getElementById('realtime-clock').textContent =
                        `${now.toLocaleDateString('id-ID', options).toUpperCase()} | ${now.toLocaleTimeString('id-ID', {hour12: false})}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // --- 2. Initialize Charts ---
            window.onload = function () {
                drawTimeline();
                drawTrendChart();
                if (countMesin > 10)
                    initAutoScroll();
            };

            var countMesin = parseInt("<?= $count_mesin ?>", 10);
            const progressCircle = document.querySelector('.autoplay-progress svg');
            var swiper = new Swiper('.mySwiper', {
                allowTouchMove: false,
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: {
                    delay: 300000,
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

            // --- 3. Timeline Chart (Custom Series) ---

            const  asData = (() => {
                return $.ajax({
                    type: "post",
                    data: {
                        dept: "<?= $dept ?>"
                    },
                    url: "<?php echo base_url(); ?>report/machinemonitoringv2/get_items",
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
            loop = 0;
            const updateData = (() => {
                return $.ajax({
                    type: "post",
                    url: "<?php echo base_url(); ?>report/machinemonitoringv2/ins_timeline",

                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
            var runToday = 0;
            var timeRunToday = 0;
            async function persenToday() {
                await asDataGrafik(0).then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        runToday += (sd.running / sd.total);
                        timeRunToday += parseInt(sd.total);
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
            })

            async function drawTimeline() {
                const chartDom = document.getElementById('timeline_tricot');
                const myChart = echarts.init(chartDom);
                const machines = [];
                var namas = [];
                var nama_mesin = "", count = 0;
                const statusColors = JSON.parse('<?= json_encode($status) ?>');
                updateData();
                await asData().then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        if (nama_mesin !== sd.nama_mesin) {
                            count += 1;
                            namas.push(sd.nama_mesin);
                        }
                        machines.push({nama: sd.nama_mesin, status: sd.status, warna: sd.warna_status, start: moment(sd.start).toDate(), end: moment(sd.end).toDate()});
                        nama_mesin = sd.nama_mesin;
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
                    grid: {top: 10, bottom: 20, left: 30, right: 10, height: '10%', containLabel: true},
                    xAxis: {
                        type: 'time',
                        position: 'top',
                        splitLine: {show: true, lineStyle: {color: 'grey'}},
                        axisLabel: {color: '#999', fontSize: 10, formatter: '{HH}:{mm}'},
                        minorTick: {show: true, splitNumber: 4}

                    },
                    yAxis: {
                        data: namas,
                        splitLine: {show: true, lineStyle: {color: '#F0F0F0'}},
                        axisLine: {show: false},
                        axisTick: {show: false},
                        axisLabel: {fontWeight: 'bold', color: '#333'}
                    },
                    series: [{
                            type: 'custom',
                            clickable: true,
                            renderItem: function (params, api) {
                                let categoryIndex = api.value(0);
                                let start = api.coord([api.value(1), categoryIndex]);
                                let end = api.coord([api.value(2), categoryIndex]);
                                let height = api.size([0, 1])[1] * 0.6; // Tinggi bar 60% dari baris

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
                            data: data
                        }]
                };
                loop = 0;
                myChart.clear();
                myChart.setOption(option, true);
            }

            // --- 4. Trend Chart (Line/Area) ---
            async function drawTrendChart() {
                const chartDom = document.getElementById('trend_chart');
                const myChart = echarts.init(chartDom);

                const dates = [];
                const values = [];
                var persens = 0;
                await asDataGrafik().then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        dates.push(moment(sd.tgl).format("DD MMM"));
                        var pr = (sd.running / sd.total) / sd.total;
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
                        splitLine: {lineStyle: {type: 'dashed'}},
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

                myChart.setOption(option);
                myChart.getZr().on('click', function (params) {
                    // Check if a data graphical element was clicked
                    if (params.target) {
                        console.log("ads");
                        // You clicked a specific bar, line symbol, etc.
                    } else {
                        console.log("adsss");
                        // You clicked on the empty chart background
                    }
                });

            }

            // --- 5. Auto Scroll Logic ---
            function initAutoScroll() {
                const scrollContainer = document.getElementById('scroll-container');
                let isPaused = false;

                scrollContainer.addEventListener('mouseenter', () => isPaused = true);
                scrollContainer.addEventListener('mouseleave', () => isPaused = false);

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
                                }, 2000); // Tunggu di atas sebentar
                            }, 2000);
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
                var data = {
                    mark_success: 0,
                    mark_warning: 0,
                    mark_danger: 0
                };
                myElements.forEach(element => {
                    var ee = element.getAttribute("data-status");
                    data[ee] += 1;
                });
                Object.keys(data).forEach(key => {
                    $(`.sum-${key}`).attr("data-val", data[key]);
                    $(`.sum-${key}`).html(data[key]);
                    //                    console.log(key, data[key]); // Prints "name Jean-Luc Picard" then "rank Captain"
                });
                var dt = JSON.parse('<?= $state ?>');
                dataMesin.forEach(el => {
                    dt[el.state] += 1;
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
                var base = "<?= base_url("dist/img/") ?>";
                $.each(data, function (index, val) {
                    var datas = $(`.card-d${val.devid}`).data();
                    if (datas) {
                        updatePersenToday(datas.state);
                        if (dataMesin[`${val.devid}`] === undefined) {
                            dataMesin[`${val.devid}`] = {
                                state: parseInt(datas.state),
                                total: parseInt(datas.total),
                                totaldown: parseInt(datas.downtime),
                                durasi_down: datas.durasi_down,
                                uptime: datas.uptime,
                                durasi_up: datas.durasi_up
                            };
                        }
                        var stt = datas.state;
                        dataMesin[`${val.devid}`].state = parseInt(val.state);
                        dataMesin[`${val.devid}`].total += 1;
                        dataMesin[`${val.devid}`].totaldown += 1;
                        var animasi = false;
                        var dtt = "";
                        var border = stts[val.state]["warna"];
                        ;
                        var status = "mark_success";
                        var logo = base + "/mark_success.png";
                        switch (true) {
                            case (parseInt(val.state) != 1 && dataMesin[`${val.devid}`].durasi_down <= 9):
                                dataMesin[`${val.devid}`].downtime += 1;
                                dataMesin[`${val.devid}`].durasi_down += 1;
                                logo = base + "mark_warning.png";
                                status = "mark_danger";
                                dataMesin[`${val.devid}`].uptime = 0;
                                dataMesin[`${val.devid}`].durasi_up = 0;
                                animasi = (dataMesin[`${val.devid}`].durasi_down <= 1) ? true : false;
                                var dt = converMinute(dataMesin[`${val.devid}`].durasi_down);
                                dtt = "Stop : " + dt.join();
//                                    border = stt[dataMesin[`${val.devid}`].state]["warna"];
                                break;
                            case (parseInt(val.state) != 1 && dataMesin[`${val.devid}`].durasi_down >= 10) :
                                logo = base + "mark_danger.png";
                                status = "mark_danger";
                                dataMesin[`${val.devid}`].downtime += 1;
                                dataMesin[`${val.devid}`].durasi_down += 1;
                                dataMesin[`${val.devid}`].uptime = 0;
                                dataMesin[`${val.devid}`].durasi_up = 0;
                                animasi = (dataMesin[`${val.devid}`].durasi_down <= 11) ? true : false;
                                var dt = converMinute(dataMesin[`${val.devid}`].durasi_down);
                                dtt = "Stop : " + dt.join();
//                                    border = "red";
                                break;
                            default:
                                dataMesin[`${val.devid}`].uptime += 1;
                                dataMesin[`${val.devid}`].durasi_up += 1;
                                dataMesin[`${val.devid}`].downtime = 0;
                                dataMesin[`${val.devid}`].durasi_down = 0;
                                status = "mark_success";
                                animasi = (stt != val.state) ? true : false;
                                var dt = converMinute(dataMesin[`${val.devid}`].durasi_up);
                                dtt = "Running : " + dt.join();
                                break;
                        }


                        if (animasi) {
                            var image = $(`.img-d${val.devid}`);

                            image.fadeOut("fast", function () {
                                $(`.img-d${val.devid}`).attr("src", logo);
                                $(`.img-d${val.devid}`).attr("data-status", status);
                                image.fadeIn('fast');
                            });
                            $(`.container-d${val.devid}`).css("backgroundColor", `${border}`);
                        }
                        $(`.durasi-d${val.devid}`).html(dtt);

                        //                        $(`.card-d${val.devid}p${val.port}`).attr("data-total", datas.total);
                        //                        $(`.card-d${val.devid}p${val.port}`).attr("data-downtime", datas.downtime);
                        //                        $(`.card-d${val.devid}p${val.port}`).attr("data-durasi_down", datas.durasi_down);
                        //                        $(`.card-d${val.devid}p${val.port}`).attr("data-durasi_up", datas.durasi_up);
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
                    if (loop > 10)
                        await drawTimeline();
                }

            };

            $(function () {
                $(".sum-mark_danger").attr("data-val",<?= $sumRed ?>);
                $(".sum-mark_danger").html("<?= $sumRed ?>");

                $(".sum-mark_warning").attr("data-val",<?= $sumYel ?>);
                $(".sum-mark_warning").html("<?= $sumYel ?>");

                $(".sum-mark_success").attr("data-val",<?= $sumGr ?>);
                $(".sum-mark_success").html("<?= $sumGr ?>");

                persenToday();
            });
        </script>
    </body>
</html>