<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>TERMINAL NOC - PRO CONSOLE v3.0</title>
        <style>
            :root {
                --bg-color: #05070a;
                --panel-bg: #0d1117;
                --accent: #00d4ff;
                --online: #238636;
                --offline: #da3633;
                --text-dim: #8b949e;
            }

            * {
                box-sizing: border-box;
            }
            body {
                margin: 0;
                padding: 0;
                background-color: var(--bg-color);
                color: #e6edf3;
                font-family: 'Cascadia Code', 'Consolas', monospace;
                height: 100vh;
                overflow: hidden;
                background-image: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
                    linear-gradient(90deg, rgba(255, 0, 0, 0.03), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.03));
                background-size: 100% 2px, 3px 100%;
            }

            /* Header Layout */
            .header {
                display: grid;
                grid-template-columns: 1fr 2fr 1fr;
                align-items: center;
                padding: 0 30px;
                background: var(--panel-bg);
                border-bottom: 2px solid #30363d;
                height: 70px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.6);
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .logo-box {
                width: 32px;
                height: 32px;
                border: 2px solid var(--accent);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: var(--accent);
            }
            .brand h1 {
                font-size: 0.9rem;
                margin: 0;
                letter-spacing: 1px;
            }

            /* Stats Centered */
            .stats-panel {
                display: flex;
                justify-content: center;
                gap: 40px;
                background: rgba(0, 0, 0, 0.4);
                padding: 8px 30px;
                border-radius: 50px;
                border: 1px solid #30363d;
            }
            .stat-group {
                display: flex;
                flex-direction: column;
                align-items: center;
                min-width: 90px;
            }
            .stat-label {
                font-size: 0.6rem;
                color: var(--text-dim);
                text-transform: uppercase;
            }
            .stat-value {
                font-size: 1.2rem;
                font-weight: bold;
            }

            .sys-info {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
            }
            #clock {
                color: var(--accent);
                font-size: 1.1rem;
                font-weight: bold;
            }

            /* Table Area */
            #scroll-container {
                height: calc(100vh - 70px);
                overflow-y: auto;
                padding: 10px 40px;
            }

            table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 5px;
            }

            th {
                position: sticky;
                top: 0;
                background: var(--bg-color);
                z-index: 10;
                padding: 12px;
                font-size: 0.75rem;
                text-align: left;
                color: var(--accent);
                border-bottom: 1px solid var(--accent);
                text-transform: uppercase;
            }

            td {
                padding: 10px 15px;
                font-size: 0.9rem;
                background: rgba(22, 27, 34, 0.7);
                border-top: 1px solid #30363d;
                border-bottom: 1px solid #30363d;
            }

            .ip-col {
                color: #fff;
                font-weight: bold;
                width: 10%;
            }
            .status-col {
                width: 10%;
            }
            .alias-col {
                color: var(--text-dim);
                font-style: italic;
            }

            .status-tag {
                padding: 2px 10px;
                border-radius: 3px;
                font-size: 0.7rem;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border: 1px solid transparent;
            }
            .online-tag {
                background: rgba(0, 255, 157, 0.1);
                color: var(--online);
                border-color: var(--online);
            }
            .offline-tag {
                background: rgba(255, 62, 62, 0.1);
                color: var(--offline);
                border-color: var(--offline);
                animation: pulse-red 1.5s infinite;
            }

            .matrix-container {
                height: calc(100vh - 80px);
                padding: 15px;
                display: grid;
                grid-template-columns: repeat(20, 1fr);
                grid-template-rows: repeat(13, 1fr);
                gap: 4px;
            }

            .node {
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                font-weight: bold;
                border-radius: 2px;
                color: #fff;
            }

            /* Status Colors - Solid No Animation */
            .active-node {
                background-color: var(--online);
            }
            .down-node {
                background-color: var(--offline);
            }

            @keyframes pulse-red {
                0% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.5;
                }
                100% {
                    opacity: 1;
                }
            }

            #scroll-container::-webkit-scrollbar {
                width: 0px;
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
        </style>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="http://10.10.0.8/hms_staging_2/dist/css/slider.css" />
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    </head>
    <body>

        <div class="header">
            <div class="brand">
                <div class="logo-box"></div>
                <div>
                    <h1>HMS - PING MONITOR</h1>
                    <div style="font-size: 0.55rem; color: var(--online);">SYSTEM_ACTIVE</div>
                </div>
            </div>

            <div class="stats-panel">
                <div class="stat-group">
                    <span class="stat-label">TOTAL_HOST</span>
                    <span class="stat-value">255</span>
                </div>
                <div class="stat-group" style="border-left: 1px solid #333; border-right: 1px solid #333;">
                    <span class="stat-label">ONLINE</span>
                    <span class="stat-value" style="color: var(--online);" id="on-val">0</span>
                </div>
                <div class="stat-group">
                    <span class="stat-label">OFFLINE</span>
                    <span class="stat-value" style="color: var(--offline);" id="off-val">0</span>
                </div>
            </div>

            <div class="sys-info">
                <span id="clock">00:00:00</span>
                <span style="font-size: 0.6rem; color: var(--text-dim);">LAST UPDATE: <span id="lastscan"></span></span>
            </div>
        </div>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div id="scroll-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 2%;">ID</th>
                                    <th class="ip-col">IP_ADDRESS</th>
                                    <th class="status-col">STATUS</th>
                                    <th class="status-col">ALIAS</th>
                                </tr>
                            </thead>
                            <tbody id="table-body"></tbody>
                        </table>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="matrix-container" id="matrix"></div>
                </div>
            </div>
        </div>



        <div class="autoplay-progress">
            <svg width="100%" height="1px">
            <line x1="0" y1="0" x2="100%" y2="0" />
            </svg>
            <span></span>
        </div>
        <script type="text/javascript" src="http://10.10.0.8/hms_staging_2/dist/js/sliders.js"></script>
        <script>
            const container = document.getElementById('scroll-container');
            const tableBody = document.getElementById('table-body');
            const progressCircle = document.querySelector('.autoplay-progress svg');
            let isWaiting = false;
            const lasScan = document.getElementById('lastscan');
            var swiper = new Swiper('.mySwiper', {
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: {
                    delay: 300000,
                    disableOnInteraction: false
                },
                on: {
                    autoplayTimeLeft(s, time, progress) {
                        progressCircle.style.setProperty('--progress', 1 - progress);
                    },
                }
            });


            var ipSocket = "<?= $ip_socket ?>";
            const socket = new WebSocket(`${ipSocket}`);
            socket.onopen = function () {
                console.log("Connected to server");
            };
            socket.onmessage = function (event) {
                var data = JSON.parse(event.data);
                loadDatas(data["data"]);
                loadGrid(data["data"]);
//                Object.entries(data).forEach(([key, value]) => {
//                    console.log(`${key}: ${value}`);
//                });
                //                updateContent(data);
                lasScan.textContent = data["time"];
            };

            const loadDatas = ((dt) => {
                var i = 0, on = 0, off = 0, isOnline = true;
                let html = '';
                Object.entries(dt).forEach(([key, value]) => {
                    i++;
                    isOnline = true;
                    on++;
                    if (value == 0) {
                        isOnline = false;
                        off++;
                        on--;
                    }

                    html += `
                        <tr>
                            <td>${i.toString().padStart(3, '0')}</td>
                            <td class="ip-col">10.10.0.${key}</td>
                            <td class="status-col">
                                <span class="status-tag ${isOnline ? 'online-tag' : 'offline-tag'}">
                                    ${isOnline ? 'ACTIVE' : 'DOWN'}
                                </span>
                            </td>
                            <td class="status-col">ALIAS</td>
                        </tr>`;


                });
                tableBody.innerHTML = html;
                document.getElementById('on-val').innerText = on;
                document.getElementById('off-val').innerText = off;
            })

            function loadData() {
                let html = '';
                let on = 0, off = 0;
                for (let i = 1; i <= 255; i++) {
                    const isOnline = Math.random() > 0.15;
                    if (isOnline)
                        on++;
                    else
                        off++;

                    html += `
                        <tr>
                            <td>${i.toString().padStart(3, '0')}</td>
                            <td class="ip-col">10.10.0.${i}</td>
                            <td class="status-col">
                                <span class="status-tag ${isOnline ? 'online-tag' : 'offline-tag'}">
                                    ${isOnline ? 'ACTIVE' : 'DOWN'}
                                </span>
                                    <td class="status-col">ALIAS</td>
                            </td>
                        </tr>`;
                }
                tableBody.innerHTML = html;
                document.getElementById('on-val').innerText = on;
                document.getElementById('off-val').innerText = off;
            }

            function autoScroll() {
                if (isWaiting)
                    return;

                const speed = 2;
                const currentScroll = container.scrollTop;
                const maxScroll = container.scrollHeight - container.clientHeight;

                if (currentScroll === 0) {
                    // Tahan di ATAS
                    isWaiting = true;
                    setTimeout(() => {
                        isWaiting = false;
                        container.scrollTop += speed;
                    }, 5000);
                } else if (currentScroll >= maxScroll - 1) {
                    // Tahan di BAWAH
                    isWaiting = true;
                    setTimeout(() => {
                        container.scrollTop = 0; // Lompat ke atas
                        isWaiting = false;
                    }, 5000);
                } else {
                    container.scrollTop += speed;
                }
            }

            setInterval(() => {
                document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID');
            }, 1000);

            loadData();
            setInterval(autoScroll, 45);
//            setInterval(loadData, 60000); // Re-scan data tiap menit

            const matrix = document.getElementById('matrix');
            function loadGrid(dt) {
                let html = '';
                let on = 0, off = 0, i = 0;
                Object.entries(dt).forEach(([key, value]) => {
                    i++;
                    isOnline = true;
                    on++;
                    if (value == 0) {
                        isOnline = false;
                        off++;
                        on--;
                    }

                    html += `<div class="node ${isOnline ? 'active-node' : 'down-node'}" title="10.10.0.${key}">${key}</div>`;

                    matrix.innerHTML = html;
                    document.getElementById('on-val').innerText = on;
                    document.getElementById('off-val').innerText = off;
                });
            }
        </script>

    </body>
</html>