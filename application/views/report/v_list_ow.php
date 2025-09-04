
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    .divListviewHead table  {
      display: block;
      height: calc( 100vh - 250px );
      overflow-x: auto;
    }

  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
   <?php $this->load->view("admin/_partials/topbar.php") ?>
  </header>

  <!-- Menu Side Bar -->
  <aside class="main-sidebar">
  <?php $this->load->view("admin/_partials/sidebar.php") ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header">
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b>List OW</b></h3>
        </div>
        <div class="box-body">

            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_job_list" action="">
                <div class="col-md-8">
                    <div class="form-group">
                    <div class="col-md-12"> 
                        <div class="col-md-2">
                        <label>Tanggal OW</label>
                        </div>
                        <div class="col-md-4">
                        <div class='input-group'>
                            <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" required="">
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>    
                        </div>
                        </div>
                        <div class="col-md-1">
                            <label>s/d</label>
                        </div>
                        <div class="col-md-4">
                        <div class='input-group'>
                            <input type="text" class="form-control input-sm" name="tglsampai" id='tglsampai' required="">
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>    
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-sm btn-default" name="btn-filter" id="btn-filter" >Proses</button>
                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" > <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                    <div class="col-md-12">
                        <div class="col-md-2 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                            <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed' >
                            <label style="cursor:pointer;">
                                <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                                Advanced 
                            </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                        <div class="pull-right text-right">
                            <div id='pagination'></div>
                        </div>
                        </div>
                    </div>

                    </div>
                </div>
              <br>
                <div class="col-md-12" style="padding-bottom: 5px;">
                   <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body" style="padding: 5px">
                        <div class="col-md-4" >
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>SC </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="sc" id="sc" >
                            </div>
                          </div> 
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Marketing </label>
                            </div>
                            <div class="col-md-7">
                                <select class="form-control input-sm" name="sales_group" id="sales_group" >
                                    <option value=''>--Pilih Marketing--</option>
                                    <?php foreach($mst_sales_group as $msg){
                                            echo '<option value="'.$msg->kode_sales_group.'">'.$msg->nama_sales_group.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>OW </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="ow" id="ow" >
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Produk </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="produk" id="produk" >
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Warna </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="warna" id="warna" >
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Status OW </label>
                            </div>
                            <div class="col-md-7">
                                  <select class="form-control input-sm" id="status_ow" name="status_ow" >
                                    <?php $arr_stat = array('','t','f','ng','r');
                                          foreach($arr_stat as $stats){
                                            if($stats == 't'){
                                              $status = 'Aktif';
                                            }else if($stats == 'ng'){
                                              $status = 'Not Good';
                                            }else if($stats == 'r'){
                                              $status = 'Reproses';
                                            }else if($stats == 'f'){
                                              $status = 'Tidak Aktif';
                                            }else{
                                              $status = 'All';
                                            }
                                            if($stats == 't'){
                                              echo '<option value="'.$stats.'" selected>'.$status.'</option>';
                                            }else{
                                              echo '<option value="'.$stats.'">'.$status.'</option>';
                                            }
                                          }
                                    ?>
                                  </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>No.OW </label>
                            </div>
                            <div class="col-md-7">
                                <select type="text" class="form-control inpt-sm" name="no_ow" id="no_ow"> 
                                  <option value=''>All</option>
                                  <option value='t' selected> Ada</option>
                                  <option value='f'>Tidak Ada</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Status Resep </label>
                            </div>
                            <div class="col-md-7">
                                <select type="text" class="form-control inpt-sm" name="status_resep" id="status_resep"> 
                                  <option value=''>All</option>
                                  <option value='draft'> Draft</option>
                                  <option value='done'> Done</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                              <div class="col-md-5">
                                <label>Tampil Stock Greige </label>
                              </div>
                              <div class="col-md-7">
                                  <input type="checkbox" name="stock_grg" value="show" >
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
            </form>
            <br>

            <!-- table -->
            <div class="box-body">
              <div class="col-xs-12 table-responsive">
                <table id="example1" class="table table-striped">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>No.SC</th>
                      <th>Kode MKT</th>
                      <th>No.OW</th>
                      <th>Tgl OW</th>
                      <th>Status OW</th>
                      <th>Product</th>
                      <th>Warna</th>
                      <th>Qty</th>
                      <th>Stock.GRG [Qty1]</th>
                      <th>Gramasi</th>
                      <th>Finishing</th>
                      <th>Route</th>
                      <th>L.Greige</th>
                      <th>L.Jadi</th>
                      <th>DTI</th>
                      <th>Status Resep</th>
                      <th>Piece Info</th>
                      <th>Reff Note</th>
                      <th>Delivery Date</th>
                      <th>CO</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>
<?php $this->load->view("admin/_partials/modal.php") ?>
<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    //* Show collapse advanced search
    $('#advancedSearch').on('shown.bs.collapse', function () {
        $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
    });

    //* Hide collapse advanced search
    $('#advancedSearch').on('hidden.bs.collapse', function () {
        $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
    });

    var d     = new Date();
    var month = d.getMonth();
    var day   = d.getDate();
    var day_7 = d.getDate()-7;
    var year  = d.getFullYear();

    // set date tgldari
    $('#tgldari').datetimepicker({
        defaultDate : new Date(year, month, day_7),
        format : 'D-MMMM-YYYY',
        ignoreReadonly: true,
        maxDate: new Date
        
    });

    // set date tglsampai
    $('#tglsampai').datetimepicker({
        defaultDate : new Date(),
        format : 'D-MMMM-YYYY',
        ignoreReadonly: true,
        maxDate: new Date
    });

    $(document).ready(function() {

        $('#example1').DataTable({ 
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'><'col-sm-7'p>>",
            "aLengthMenu": [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, "All"]],
            "iDisplayLength": 50,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "columnDefs": [
                { 
                  "targets": [21], 
                  "orderable": false, 
                },
                { 
                  "targets": [22], 
                  "visible": false, 
                },
              ]
        });
    });
    
 
    function fetch_data(tgl_dari,tgl_sampai,sc,ow,produk,warna,sales_group,no_ow,status_ow,check_stock,status_resep) {
        //datatables
        $('#example1').DataTable({ 
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'><'col-sm-7'p>>",
            "processing": true, 
            "serverSide": true, 
            "order": [4, "asc"], 
            "aLengthMenu": [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, "All"]],
            "iDisplayLength": 50,

            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('report/listOW/get_data')?>",
                "type": "POST",
                "data": function ( data ) {
                    data.tgl_dari     = tgl_dari;
                    data.tgl_sampai   = tgl_sampai;
                    data.sc     = sc;
                    data.ow     = ow;
                    data.produk = produk;
                    data.warna  = warna;
                    data.sales_group  = sales_group;
                    data.no_ow  = no_ow;
                    data.status_ow  = status_ow;
                    data.check_stock  = check_stock;
                    data.status_resep = status_resep;
                },beforeSend: function () {
                  //please_wait(function(){});
                }, complete: function () {
                  //unblockUI( function(){});
                },
            },
 
            "columnDefs": [
                { 
                  "targets": [0], 
                  "orderable": false, 
                },
                { 
                  "targets": [20], 
                  "orderable": false, 
                },
                { 
                  "targets": [9], 
                  "orderable": false, 
                },
                { 
                  "targets": [22], 
                  "visible": false, 
                },
                { 
                  "targets": [ 6 ], 
                  "width" : 120,
                },
                { 
                  "targets": [ 8 ], 
                  "className": 'text-right', 
                  "width" : 80,
                },
                { 
                  "targets": [ 9 ], 
                  "className": 'text-right', 
                },
             
            ],
            "createdRow": function( row, data, dataIndex ) {
              if (data[22] == 'f' ){          
                $(row).css("color","red");
              }else if(data[22]=='ng'){
                $(row).css("color","blue");
              }else if(data[22]=='r'){
                $(row).css("color","purple");
              }
            },
        });

    }

    $('#btn-filter').click(function(){ //button filter event click
        $('#btn-filter').button('loading');

        var check_stock = $("input[name=stock_grg]").is(':checked');
        var tgl_dari    = $('#tgldari').val();
        var tgl_sampai  = $('#tglsampai').val();
        var sc          = $('#sc').val();
        var ow          = $('#ow').val();
        var produk      = $('#produk').val();
        var warna       = $('#warna').val();
        var sales_group = $('#sales_group').val();
        var no_ow       = $('#no_ow').val();
        var status_ow   = $('#status_ow').val();
        var status_resep= $('#status_resep').val();

        var tgldari_2 = $('#tgldari').data("DateTimePicker").date();
        var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

        var timeDiff = 0;
        if (tglsampai_2) {
            timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
        }
        selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second


        if(tgl_dari == '' || tgl_sampai == ''){
          alert_modal_warning('Periode Tanggal Harus diisi !');
        }else if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
          alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
        // }else if(selisih > 30 ){
        //   alert_modal_warning('Maaf, Periode Tanggal tidak boleh lebih dari 31 hari !')
        }else{
          $('#example1').DataTable().destroy();
          fetch_data(tgl_dari,tgl_sampai,sc,ow,produk,warna,sales_group,no_ow,status_ow,check_stock,status_resep);
        }
          //table.ajax.reload( function(){
        $('#btn-filter').button('reset');
          //});  //just reload table
    });
     

      //modal view move items
    function view_detail(kode_co,sales_order,ow,id_warna){
        $("#view_data").modal({
            show: true,
            backdrop: 'static'
        })
        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Tracking OW');
        $.post('<?php echo site_url()?>report/listOW/view_detail_items',
            {kode_co:kode_co, sales_order:sales_order,  id_warna:id_warna, ow:ow,},
            function(html){
                setTimeout(function() {$(".view_body").html(html);  });
            }   
        );
    }


    $('#btn-excel').click(function(){ //button excel event click
        $('#btn-excel').button('loading');
      
        var check_stock = $("input[name=stock_grg]").is(':checked');
        var tgl_dari    = $('#tgldari').val();
        var tgl_sampai  = $('#tglsampai').val();
        var sc          = $('#sc').val();
        var ow          = $('#ow').val();
        var produk      = $('#produk').val();
        var warna       = $('#warna').val();
        var sales_group = $('#sales_group').val();
        var no_ow       = $('#no_ow').val();
        var status_ow   = $('#status_ow').val();
        var status_resep= $('#status_resep').val();

        var tgldari_2 = $('#tgldari').data("DateTimePicker").date();
        var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

        var timeDiff = 0;
        if (tglsampai_2) {
            timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
        }
        selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second


        if(tgl_dari == '' || tgl_sampai == ''){
          alert_modal_warning('Periode Tanggal Harus diisi !');
        }else if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
          alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
        // }else if(selisih > 30 ){
        //   alert_modal_warning('Maaf, Periode Tanggal tidak boleh lebih dari 31 hari !')
        }else{
          // fetch_data(tgl_dari,tgl_sampai,sc,ow,produk,warna,sales_group,no_ow,status_ow,check_stock);

            $.ajax({
                "type":'POST',
                "url" : "<?php echo site_url('report/listOW/export_excel')?>",
                "data": {tgldari:tgl_dari, tglsampai:tgl_sampai, warna:warna, sc:sc, ow:ow, produk:produk, sales_group:sales_group, no_ow:no_ow, status_ow:status_ow, stock_grg:check_stock, status_resep:status_resep},
                "dataType":'json',
                beforeSend: function() {
                  $('#btn-excel').button('loading');
                },error: function(){
                  alert('Error Export Excel');
                  $('#btn-excel').button('reset');
                }
            }).done(function(data){
                if(data.status =="failed"){
                  alert_modal_warning(data.message);
                }else{
                  var $a = $("<a>");
                  $a.attr("href",data.file);
                  $("body").append($a);
                  $a.attr("download",data.filename);
                  $a[0].click();
                  $a.remove();
                }
                $('#btn-excel').button('reset');
            });
        }
          //table.ajax.reload( function(){
          // $('#btn-excel').button('reset');
          //});  //just reload table
    });


</script>

</body>
</html>
