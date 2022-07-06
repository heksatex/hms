
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

            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_job_list" action="<?=base_url()?>report/listOW/export_excel">
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
                    <button type="submit" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" > <i class="fa fa-file-excel-o"></i> Excel</button>
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
                      <th>DTI</th>
                      <th>Piece Info</th>
                      <th>Reff Note</th>
                      <th>CO</th>
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
    var day_30 = d.getDate()-30;
    var year  = d.getFullYear();

    // set date tgldari
    $('#tgldari').datetimepicker({
        defaultDate : new Date(year, month, day_30),
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
            "iDisplayLength": 1000,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            
        });
    });
    
 
    function fetch_data(tgl_dari,tgl_sampai,sc,ow,produk,warna,sales_group){
        //datatables
        $('#example1').DataTable({ 
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'><'col-sm-7'p>>",
            "processing": true, 
            "serverSide": true, 
            "order": [4, "asc"], 
            "aLengthMenu": [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, "All"]],
            "iDisplayLength": 1000,

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
                }
            },
 
            "columnDefs": [
                { 
                  "targets": [0], 
                  "orderable": false, 
                },
                { 
                  "targets": [14], 
                  "orderable": false, 
                },
                { 
                  "targets": [ 6 ], 
                  "width" : 120,
                },
                { 
                  "targets": [ 8 ], 
                  "className": 'text-right', 
                  "width" : 50,
                },
                { 
                  "targets": [ 9 ], 
                  "className": 'text-right', 
                },
             
            ],
        });

    }

    $('#btn-filter').click(function(){ //button filter event click
        $('#btn-filter').button('loading');
        $('#example1').DataTable().destroy();
        
        var tgl_dari    = $('#tgldari').val();
        var tgl_sampai  = $('#tglsampai').val();
        var sc          = $('#sc').val();
        var ow          = $('#ow').val();
        var produk      = $('#produk').val();
        var warna       = $('#warna').val();
        var sales_group = $('#sales_group').val();

        fetch_data(tgl_dari,tgl_sampai,sc,ow,produk,warna,sales_group);
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


</script>

</body>
</html>
