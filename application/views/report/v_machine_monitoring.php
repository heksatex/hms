<!doctype html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
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
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><strong>Mesin Monitoring <?= $dept_nm ?> (Realtime Update)</strong></h3>
                            <div class="pull-right" id="btn-header">
                                Mesin : 
                                <select class="mesin-select2" id="mesin">
                                    <option value=""></option>
                                    <?php
                                    foreach ($allMesin as $key => $value) {
                                        ?>
                                        <option value="<?= $value->dept_id ?>" <?= ($value->dept_id === $dept) ? "selected" : ""?>><?= $value->nama ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                &nbsp;&nbsp;&nbsp;
                                Summary : 
                                <span class="label label-danger sum-mark_danger" style="background-color: red; color: black;" data-val="0">0</span>
                                <span class="label label-warning sum-mark_warning" style="background-color: yellow; color: black;" data-val="0">0</span>
                                <span class="label label-success sum-mark_success" style="background-color: #00ff00; color: black;" data-val="0">0</span>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php
                            $exist = [];
                            $sumRed = 0;
                            $sumYel = 0;
                            $sumGr = 0;
                            foreach ($mesin as $key => $value) {
                                $durasis = $durasi["d{$value->devid}p{$value->port}"];
                                $logo = "mark_success";
                                $ttlDwn = $value->downtime / $value->total;
                                $ttlUp = $value->uptime / $value->total;
                                $value->state = $durasis->state;
                                $border = "#00ff00";
                                if ($value->state == 1 && $durasis->total_down <= 10) {
                                    $logo = "mark_warning";
                                    $border = "yellow";
                                    $sumYel += 1;
                                } else if ($value->state == 1 && $durasis->total_down >= 11) {
                                    $logo = "mark_danger";
                                    $border = "red";
                                    $sumRed += 1;
                                } else {
                                    $sumGr += 1;
                                }
                                ?>
                                <!--<div class="col-lg-2 col-md-4 col-xs-6">-->
                                <div class="card card-<?= "d{$value->devid}p{$value->port}" ?>" data-durasi_up="<?= $durasis->total_up ?>" data-durasi_down="<?= $durasis->total_down ?>"
                                     data-state="<?= $value->state ?>" data-totaldown = "<?= $ttlDwn ?>" data-total="<?= $value->total ?>" data-uptime="<?= $ttlUp ?>" data-downtime="<?= $ttlDwn ?>"
                                     style="border: 1px solid <?= $border ?>;">
                                    <div class="container" style="height: 25%;">
                                        <b><?= "{$value->nama_mesin}" ?></b>
                                    </div>
                                    <img class="img-<?= "d{$value->devid}p{$value->port}" ?> center statuss" data-status="<?= $logo ?>" src="<?= base_url("dist/img/{$logo}.png") ?>" alt="Avatar">
                                    <div class="container" style="word-wrap: break-word;">
                                        <p title="Downtime Shift Sekarang">Downtime : <span class="down-<?= "d{$value->devid}p{$value->port}" ?>"><?= round(($ttlDwn / $value->total) * 100) ?></span> %</p>
                                        <span class="durasi-text durasi-<?= "d{$value->devid}p{$value->port}" ?>"><?= ($value->state == 0) ? "Running : {$durasis->total_up_text}" : "Stop : {$durasis->total_down_text}" ?></span>
                                    </div>
                                </div>

                                <!--</div>-->
                            <?php }
                            ?>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>
        </div>
        <script>
            var times = "<?= $times ?>";
            const updateContent = ((data) => {
                var base = "<?= base_url("dist/img/") ?>";
                $.each(data, function (index, val) {
                    var datas = $(`.card-d${val.devid}p${val.port}`).data();
                    if (datas) {
                        var stt = datas.state;
                        datas.state = parseInt(val.state);
                        datas.total += parseInt(val.total);
                        datas.totaldown += parseInt(val.downtime);
                        var animasi = false;
                        var dtt = "";
                        var border = "#00ff00";
                        var status = "mark_success";
                        var logo = base + "/mark_success.png";
                        if (datas.state === 1 && datas.durasi_down <= 9) {
                            datas.downtime += parseInt(val.downtime);
                            datas.durasi_down += parseInt(val.downtime);
                            logo = base + "mark_warning.png";
                            status = "mark_warning";
                            datas.uptime = 0;
                            datas.durasi_up = 0;
                            animasi = (datas.durasi_down <= 1) ? true : false;
                            var dt = converMinute(datas.durasi_down);
                            dtt = "Stop : " + dt.join();
                            border = "yellow";
                        } else if (datas.state === 1 && datas.durasi_down >= 10) {
                            logo = base + "mark_danger.png";
                            status = "mark_danger";
                            datas.downtime += parseInt(val.downtime);
                            datas.durasi_down += parseInt(val.downtime);
                            datas.uptime = 0;
                            datas.durasi_up = 0;
                            animasi = (datas.durasi_down <= 11) ? true : false;
                            var dt = converMinute(datas.durasi_down);
                            dtt = "Stop : " + dt.join();
                            border = "red";
                        } else {
                            datas.uptime += parseInt(val.uptime);
                            datas.durasi_up += parseInt(val.uptime);
                            datas.downtime = 0;
                            datas.durasi_down = 0;
                            status = "mark_success";
                            animasi = (stt != val.state) ? true : false;
                            var dt = converMinute(datas.durasi_up);
                            dtt = "Running : " + dt.join();
                        }

                        if (animasi) {
                            var image = $(`.img-d${val.devid}p${val.port}`);

                            image.fadeOut("fast", function () {
                                $(`.img-d${val.devid}p${val.port}`).attr("src", logo);
                                $(`.img-d${val.devid}p${val.port}`).attr("data-status", status);
                                image.fadeIn('fast');
                            });
                            $(`.card-d${val.devid}p${val.port}`).css("border", `1px solid ${border}`);
                        }
                        $(`.durasi-d${val.devid}p${val.port}`).html(dtt);

                        $(`.card-d${val.devid}p${val.port}`).attr("data-total", datas.total);
                        $(`.card-d${val.devid}p${val.port}`).attr("data-downtime", datas.downtime);
                        $(`.card-d${val.devid}p${val.port}`).attr("data-durasi_down", datas.durasi_down);
                        $(`.card-d${val.devid}p${val.port}`).attr("data-durasi_up", datas.durasi_up);
                        var ttlDwn = (datas.totaldown / datas.total) * 100;
                        $(`.down-d${val.devid}p${val.port}`).html(ttlDwn.toFixed(0));
                    }

                });
                updateSummary();
            });

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
            const requestUpdate = (() => {
                $.ajax({
                    url: "<?= base_url("report/machinemonitoring/update") ?>",
                    dataType: 'JSON',
                    type: "post",
                    timeout: 10000,
                    data: {
                        times: times
                    },
                    complete: function (jqXHR, textStatus) {

                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                    },
                    success: ((data) => {
                        times = data.times;
                        updateContent(data.data);
                    })
                });
            });
            var ipSocket = "<?= $ip_socket ?>";
//            const socket = new WebSocket(`ws://${ipSocket}:8889`);
            const socket = new WebSocket(`${ipSocket}`);
            socket.onopen = function () {
                console.log("Connected to server");
            };
            socket.onmessage = function (event) {
                var data = JSON.parse(event.data);
//                var dept = $("#mesin :selected").val();
//                console.log(dept);
                updateContent(data);
//                document.getElementById("messages").innerHTML += `<p><strong>Server:</strong> ${event.data}</p>`;
            };
            $(function () {
                $(".mesin-select2").select2({
                    allowClear: true,
                    placeholder: "Filter Departemen",
                });
                $(".sum-mark_danger").attr("data-val",<?= $sumRed ?>);
                $(".sum-mark_danger").html("<?= $sumRed ?>");

                $(".sum-mark_warning").attr("data-val",<?= $sumYel ?>);
                $(".sum-mark_warning").html("<?= $sumYel ?>");

                $(".sum-mark_success").attr("data-val",<?= $sumGr ?>);
                $(".sum-mark_success").html("<?= $sumGr ?>");

//                updateSummary();
//                const socket = new WebSocket("ws://localhost:8080/chat");
//                socket.onopen = function () {
//                    console.log("Connected to server");
//                };
//                socket.onmessage = function (event) {
//                    console.log(event.data);
////                document.getElementById("messages").innerHTML += `<p><strong>Server:</strong> ${event.data}</p>`;
//                };
//                const now = new Date();
//                const secondsUntilNextMinute = 60 - now.getSeconds();
//                const initialDelayMilliseconds = secondsUntilNextMinute * 1000;
//
//                const intervalID = setInterval(requestUpdate(), 60000);
//                setTimeout(() => {
//                    requestUpdate();
//                    setInterval(requestUpdate, 60000);
//                }, initialDelayMilliseconds);
                $("#mesin").on("change", function () {
                    var dept_id = $(this).val();
                    var dept_nama = $("#mesin :selected").text();
                    location.href = "<?= base_url("report/machinemonitoring") ?>"+`?dept=${dept_id}&nm=${dept_nama}`;
                });
            });
        </script>
    </body>
</html>