
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
          <h3 class="box-title"><b>Job List Lab</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_job_list" action="#">
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
                                    <label>Status DTI </label>
                                </div>
                                <div class="col-md-7">
                                    <select class="form-control input-sm" name="status_dti" id="status_dti">
                                        <option value="">All</option>
                                        <option value="draft">Draft</option>
                                        <option value="ready">Ready</option>
                                        <option value="requested">Requested</option>
                                        <option value="done">Done</option>
                                        <option value="cancel">Cancel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Status Resep </label>
                                </div>
                                <div class="col-md-7">
                                    <select class="form-control input-sm" name="status_resep" id="status_resep">
                                        <option value="">All</option>
                                        <option value="draft" selected>Draft</option>
                                        <option value="done">Done</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-5">
                                </div>
                                <div class="col-md-7">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-filter" id="btn-filter" >Proses</button>
                                    <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" > <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
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
                      <th>No SC</th>
                      <th>MKT</th>
                      <th>No Ow</th>
                      <th>Tgl OW</th>
                      <th>Status OW</th>
                      <th>Product</th>
                      <th>Warna</th>
                      <th>Stock Greige [Mtr]</th>
                      <th>Gramasi</th>
                      <th>Finishing</th>
                      <th>Route</th>
                      <th>Lebar Jadi</th>
                      <th>DTI</th>
                      <th>Reff Note</th>
                      <th>Delivery Date</th>
                      <th>Status Resep</th>
                      <th>Tgl Selesai Resep</th>
                      <th>Action</th>
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

  //* Show collapse advanced search
  $('#advancedSearch').on('shown.bs.collapse', function () {
      $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
  });

  //* Hide collapse advanced search
  $('#advancedSearch').on('hidden.bs.collapse', function () {
    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
  });


  var table;
    $(document).ready(function() {
 
        //datatables
        table = $('#example1').DataTable({ 
          "aLengthMenu": [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, "All"]],
           "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",
            "stateSave": false,
            "processing": true, 
            "serverSide": true, 
            // "order": [2, "asc"], 
            "iDisplayLength": 50, 
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('report/joblistlab/get_data')?>",
                "type": "POST",
                "data": function ( data ) {
                    data.sc             = $('#sc').val();
                    data.sales_group    = $('#sales_group').val();
                    data.ow             = $('#ow').val();
                    data.produk         = $('#produk').val();
                    data.warna          = $('#warna').val();
                    data.status_ow      = $('#status_ow').val();
                    data.status_dti     = $('#status_dti').val();
                    data.status_resep   = $('#status_resep').val();
                }
            },
 
            "columnDefs": [
                { 
                    "targets": [ 0 ], 
                    "orderable": false, 
                },
                { 
                    "targets": [ 7 ], 
                    "orderable": false, 
                },
                { 
                    "targets": [ 19 ], 
                    "visible": false, 
                },
            ],
            "createdRow": function( row, data, dataIndex ) {
              if (data[19] == 'f' ){          
                $(row).css("color","red");
              }else if(data[19]=='ng'){
                $(row).css("color","blue");
              }else if(data[19]=='r'){
                $(row).css("color","purple");
              }
            },
        });

        $('#btn-filter').click(function(){ //button filter event click
          $('#btn-filter').button('loading');
          table.ajax.reload( function(){
            $('#btn-filter').button('reset');
          });  //just reload table
        });



      
    });

    function doneResep(btn,id,ow,status_resep) {

        if(status=='done'){
            var message = 'Data Resep Sudah Done !';
            alert_modal_warning(message);
        }else{
            bootbox.dialog({
            message: "Anda yakin ingin menyelesaikan Resep untuk <b>"+ow+"</b> ini ?",
            title: " Done Resep !",
            buttons: {
            danger: {
                label    : "Yes ",
                className: "btn-primary btn-sm",
                callback : function() {
                        please_wait(function(){});
                        $(btn).button('loading');
                        $.ajax({
                            type: 'POST',
                            dataType : 'json',
                            url : "<?php echo site_url('report/joblistlab/done_resep')?>",
                            data : {id:id,ow:ow},
                            error: function (xhr, ajaxOptions, thrownError) { 
                                alert(xhr.responseText);
                                $(btn).button('reset');
                                unblockUI( function(){});
                            }
                        })
                        .done(function(response){
                            if(response.sesi == 'habis'){//jika session habis
                                alert_modal_warning(response.message);
                                window.location.replace('../index');
                            }else if(response.status == 'draft' || response.status == 'ada'  ||  response.status == 'not_valid' ){
                                //jika ada item masih draft/status sudah terkirim/lokasi lot tidak valid
                                unblockUI( function(){});
                                alert_modal_warning(response.message);                      
                                $(btn).button('reset');
                                table.ajax.reload();
                            }else{
                                unblockUI( function() {
                                    setTimeout(function() { alert_notify(response.icon,response.message,response.type); }, 1000);
                                });
                                table.ajax.reload();
                            }
                        })
                }
            },
            success: {
                    label    : "No",
                    className: "btn-default  btn-sm",
                    callback : function() {
                        $('.bootbox').modal('hide');
                    }
            }
            }
        });
        }
    }


    $('#btn-excel').click(function(){ //button excel event click

            $('#btn-excel').button('loading');
            var sc             = $('#sc').val();
            var sales_group    = $('#sales_group').val();
            var ow             = $('#ow').val();
            var produk         = $('#produk').val();
            var warna          = $('#warna').val();
            var status_ow      = $('#status_ow').val();
            var status_dti     = $('#status_dti').val();
            var status_resep   = $('#status_resep').val();
      
            $.ajax({
                "type":'POST',
                "url" : "<?php echo site_url('report/joblistlab/export_excel')?>",
                "data": {sc:sc, ow:ow, produk:produk, warna:warna, sales_group:sales_group, status_ow:status_ow, status_dti:status_dti, status_resep:status_resep},
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
      
    });
 

</script>

</body>
</html>
