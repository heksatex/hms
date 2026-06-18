<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('dist/css/vis-timeline-graph2d.min.css'); ?>" />
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
            #visualization {
                box-sizing: border-box;
                width: 100%;
            }
            .vis-item.vis-background.highlight-red {
                background-color: red;
                padding: 3px 0px 3px 0px !important;
                border-bottom: 1px solid white;
            }
            .vis-item.vis-background.highlight-green {
                background-color: green;
                padding: 1px 0px 1px 0px !important;
                border-bottom: 1px solid white;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <div class="content-wrapper">
                <div id="visualization"></div>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script type="text/javascript" src="<?= base_url('dist/js/vis-timeline-graph2d.min.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script>
            var groups = new vis.DataSet(JSON.parse('<?= $group ?>'));

            // create a dataset with items
            // note that months are zero-based in the JavaScript Date object, so month 3 is April
            var items = new vis.DataSet([
            ]);

            // create visualization

            var startOfDay = moment().subtract(24, 'h').toDate();
            var endOfDay = moment().toDate();
            var container = document.getElementById("visualization");
            var options = {
                format: {
                    minorLabels: {
                        minute: 'h:mm a',
                        hour: 'HH' // Example: 13:00
                    }
                },
                start: startOfDay,
                end: endOfDay,
                min: startOfDay, // Cannot scroll before today
                max: endOfDay,
                zoomMin: 1000 * 60 * 60, // Min zoom: 1 hour
                zoomMax: 1000 * 60 * 60 * 24, // Max zoom: 24 hours
                editable: true,
                stack: true,
                zoomable: false,
                verticalScroll: true,
                autoResize: true
            };

            var timeline = new vis.Timeline(container);
            timeline.setOptions(options);
            timeline.setGroups(groups);
            timeline.setItems(items);
            const loadDataGrafik = ((items) => {
                $.ajax({
                    type: "post",
                    url: "<?php echo base_url(); ?>report/Machinemonitoringv2/get_items",
                    beforeSend: function (xhr) {
                        please_wait((() => {

                        }));
                    },
                    error: function (req, error) {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                            }, 500);
                        });
                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    },
                    success: ((data) => {
                        items.add(data.data);
                    })
                });
            });

            window.onload = function () {
                setTimeout(function () {
                    loadDataGrafik(items);
                }, 1000);

//                setTimeout(function () {
//                    var startOfDay = moment().subtract(1410, 'm').toDate();
//                    var endOfDay = moment().add(30,"m").toDate();
//                    options.end = endOfDay;
//                    options.start = startOfDay;
//                    options.min = startOfDay;
//                    options.max = endOfDay;
//                    options.zoomMin = 1000 * 60 * 60;
//                    options.zoomMax = 1000 * 60 * 60 * 24; // Max zoom: 24 hours
//                    timeline.setOptions(options);
//                }, 10000);

            };


//            setInterval(() => {
//                items.add([
//                    {
//                        id: 1,
//                        group: "d10p8",
//                        content: "",
//                        type: 'background', className: 'highlight-red',
//                        start: new Date().setHours(09, 22, 00),
//                        end: new Date().setHours(09, 23, 00)
//                    }
//                ]);
//                console.log("updated");
//            }, 10000);
//                $('#visualization').autoscroll(AUTOSCROLL_Y);
        </script>
    </body>
</html>