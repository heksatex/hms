
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
          <h3 class="box-title"><b>Job List Gudang Obat</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_job_list" action="#">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="col-md-2 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                        <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
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
                              <label>Kode </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="kode" id="kode" >
                            </div>
                          </div> 
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Origin </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="origin" id="origin" >
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
                              <label>Reff Picking </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="reff_picking" id="reff_picking" >
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Status </label>
                            </div>
                            <div class="col-md-7">
                                 <select type="text" class="form-control input-sm" name="status" id="status" >
                                    <option value="">All</option>
                                    <option value="draft">Draft</option>
                                    <option value="ready">Ready</option>
                                    <option value="done">Terkirim</option>
                                    <option value="cancel">Batal</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                            </div>
                            <div class="col-md-7">
                              <button type="button" class="btn btn-sm btn-default" name="btn-filter" id="btn-filter" >Proses</button>
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
                      <th>Kode</th>
                      <th>Tgl.dibuat</th>
                      <th>Origin</th>
                      <th>Reff Picking</th>
                      <th>Status</th>
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
            "stateSave": true,
            "processing": true, 
            "serverSide": true, 
            "order": [2, "asc"], 

            "paging": true,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('report/joblistgobout/get_data')?>",
                "type": "POST",
                 "data": function ( data ) {
                    data.id_dept  = "<?php echo $id_dept_asli;?>"
                    data.kode     = $('#kode').val();
                    data.origin   = $('#origin').val();
                    data.produk   = $('#produk').val();
                    data.status   = $('#status').val();
                    data.reff_picking = $('#reff_picking').val();
                },
                //"data":{"id_dept" : "<?php echo $id_dept;?>"}
            },
 
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              { 
                  "targets": [ 6 ], 
                  "orderable": false, 
              },
          
            ],
        });

        $('#btn-filter').click(function(){ //button filter event click
          $('#btn-filter').button('loading');
          table.ajax.reload( function(){
            $('#btn-filter').button('reset');
          });  //just reload table
        });
 
    });


    function kirim_barang(kode,move_id,method,origin,id_dept,status){

      if(status == 'cancel'){
        var message = 'Maaf, Data Tidak bisa Dikirim, Data Sudah dibatalkan !';
        alert_modal_warning(message);
        
      }else if(status=='done'){
        var message = 'Maaf, Data Sudah Terkirim !';
        alert_modal_warning(message);

      }else if(status=='draft'){
        var message = "Maaf, Product Belum ready !";
        alert_modal_warning(message);

      }else{
        bootbox.dialog({
        message: "Anda yakin ingin mengirim ?",
        title: "<i class='glyphicon glyphicon-send'></i> Send !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                    please_wait(function(){});
                    $('#btn-kirim').button('loading');
                    $.ajax({
                          type: 'POST',
                          dataType : 'json',
                          url : "<?php echo site_url('warehouse/pengirimanbarang/kirim_barang')?>",
                          data : {kode:kode, move_id:move_id, method:method, origin:origin, id_dept:id_dept},
                          error: function (xhr, ajaxOptions, thrownError) { 
                          alert(xhr.responseText);
                          $('#btn-kirim').button('reset');
                          unblockUI( function(){});
                          refresh_div_out();
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
                        refresh_div_out();                 
                        $('#btn-kirim').button('reset');
                      }else{
                        if(response.backorder == "yes"){
                          alert_modal_warning(response.message2);
                        }
                        unblockUI( function() {
                          setTimeout(function() { alert_notify(response.icon,response.message,response.type); }, 1000);
                        });
                        refresh_div_out();                     
                        $('#btn-kirim').button('reset');
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

</script>

</body>
</html>
