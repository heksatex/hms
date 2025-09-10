
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
          <h3 class="box-title"><b>Outstanding OW</b></h3>
        </div>
        <div class="box-body">

            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_job_list">
                <div class="col-md-8">
                  <div class="form-group">
                    <div class="col-md-6"> 
                        <div class="form-group">
                            <div class="col-md-5">
                              <label>SC </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="sc" id="sc" onkeydown=" event_input(event)" >
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-md-5">
                              <label>Marketing </label>
                            </div>
                            <div class="col-md-7">
                                <select class="form-control input-sm" name="sales_group" id="sales_group"  onkeydown="event_input(event)" >
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
                                <input type="text" class="form-control input-sm" name="ow" id="ow"  onkeydown="event_input(event)">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-5">
                              <label>Produk </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="produk" id="produk" onkeydown="event_input(event)" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-5">
                              <label>Warna </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="warna" id="warna"  onkeydown="event_input(event)">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-5">
                              <label>Status OW </label>
                            </div>
                            <div class="col-md-7">
                                  <select class="form-control input-sm" id="status_ow" name="status_ow" >
                                    <?php $arr_stat = array('','t','ng');
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
                  </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-sm btn-default" name="btn-filter" id="btn-filter" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." >Proses</button>
                    <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
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
                      <th>Reff Note</th>
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
<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

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
        });
    });
    
 
    function fetch_data(sc,ow,produk,warna,sales_group,status_ow){
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
                "url": "<?php echo site_url('report/outstandingow/get_data')?>",
                "type": "POST",
                "data": function ( data ) {
                    data.sc     = sc;
                    data.ow     = ow;
                    data.produk = produk;
                    data.warna  = warna;
                    data.sales_group  = sales_group;
                    data.status_ow = status_ow;
                },beforeSend: function () {
                  //please_wait(function(){});
                  $('#btn-filter').button('loading');
                }, complete: function () {
                  //unblockUI( function(){});
                  $('#btn-filter').button('reset');
                },
            },
 
            "columnDefs": [
                { 
                  "targets": [0], 
                  "orderable": false, 
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
                  "width" : 120,
                },
            ],
           
        });

    }

    $('#btn-filter').click(function(){ //button filter event click
      btn_filter();
    });
    
    function btn_filter(){
        $('#btn-filter').button('loading');
        $('#example1').DataTable().destroy();
        
        var sc          = $('#sc').val();
        var ow          = $('#ow').val();
        var produk      = $('#produk').val();
        var warna       = $('#warna').val();
        var sales_group = $('#sales_group').val();
        var status_ow   = $('#status_ow').val();
       
        fetch_data(sc,ow,produk,warna,sales_group,status_ow);
          //table.ajax.reload( function(){
        $('#btn-filter').button('reset');
          //});  //just reload table
    }

    function event_input(event){ 
      if(event.keyCode == 13) {
          event.preventDefault();
          btn_filter();
      }
    }
     

    
  $('#btn-excel').click(function(){

      var sc          = $('#sc').val();
      var ow          = $('#ow').val();
      var produk      = $('#produk').val();
      var warna       = $('#warna').val();
      var sales_group = $('#sales_group').val();
      var status_ow   = $('#status_ow').val();

      $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/outstandingow/export_excel')?>",
            "data": {sc:sc, ow:ow, produk:produk,warna:warna, sales_group:sales_group,status_ow:status_ow},
            "dataType":'json',
            beforeSend: function() {
              $('#btn-excel').button('loading');
            },error: function(){
              alert("Export Excel error");
              $('#btn-excel').button('reset');
            }
      }).done(function(data){
            if(data.status == "failed"){
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

  });

</script>

</body>
</html>
