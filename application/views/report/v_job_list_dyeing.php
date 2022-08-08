
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
          <h3 class="box-title"><b>Job List Dyeing</b></h3>
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
                              <label>Status MG </label>
                            </div>
                            <div class="col-md-7">
                                 <select type="text" class="form-control input-sm" name="status" id="status" >
                                    <option value="">All</option>
                                    <option value="draft">Draft</option>
                                    <option value="ready">Ready</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
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
                      <th>Product</th>
                      <th>Status Kain</th>
                      <th>Status Obat</th>
                      <th>Status MG</th>
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

           "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",
            "stateSave": true,
            "processing": true, 
            "serverSide": true, 
            "order": [2, "asc"], 
            "aLengthMenu": [[10,20], [10,20]],
            "iDisplayLength": 10,
            "paging": true,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('report/joblistdyeing/get_data')?>",
                "type": "POST",
                 "data": function ( data ) {
                    data.id_dept  = "<?php echo $id_dept_asli;?>"
                    data.kode     = $('#kode').val();
                    data.origin   = $('#origin').val();
                    data.produk   = $('#produk').val();
                    data.status   = $('#status').val();
                }
                //"data":{"id_dept" : "<?php echo $id_dept;?>"}
            },
 
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              { 
                  "targets": [ 5 ], 
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

</script>

</body>
</html>
