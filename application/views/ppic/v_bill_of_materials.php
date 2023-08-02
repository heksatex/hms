
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
   <?php 
     $data['deptid']     = $id_dept;
     $this->load->view("admin/_partials/topbar.php",$data)
   ?>
  </header>

  <!-- Menu Side Bar -->
  <aside class="main-sidebar">
  <?php
      $this->load->view("admin/_partials/sidebar.php"); 
   ?>
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
        <div class="box-body">

            <form name="input" class="form-horizontal" role="form" method="POST">
                <div class="form-group">
                    <div class="col-md-12">
                      <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                          <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                            <label style="cursor:pointer;">
                              <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                              Advanced  Search
                            </label>
                          </div>
                      </div>
                    </div>
                  </div>
                <div class="col-md-12">
                    <div class="panel panel-default" style="margin-bottom: 0px;">
                      <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                        <div class="panel-body" style="padding: 5px">
                          <div class="form-group col-md-12" style="margin-bottom:0px">
                              <div class="col-md-6">
                                <div class="form-group"> 
                                  <div class="col-xs-5"><label>Kode BOM</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="kode_bom" id="kode_bom" >
                                  </div>                                    
                                  <div class="col-xs-5"><label>Nama BOM</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="nama_bom" id="nama_bom"  >
                                  </div>                                    
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group"> 
                                  <div class="col-xs-5"><label>Kode Produk</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="kode_produk" id="kode_produk" >
                                  </div>                                    
                                  <div class="col-xs-5"><label>Nama Produk</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk"  >
                                  </div>                                
                                  <div class="col-xs-5"><label>Status</label></div>
                                  <div class="col-xs-7">
                                      <select class="form-control input-sm" name="status" id="status" >
                                            <option value=""></option>
                                            <option value="t">Aktif</option>
                                            <option value="f">Tidak Aktif</option>
                                      </select>
                                  </div>  
                                </div>
                              </div>
                              <div class="col-md-6" >
                                <div class="form-group" >
                                    <div class="col-xs-8" style="padding-top:0px">
                                        <button type="button" id="btn-filter" name="submit" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Proses</button>
                                    </div>                                    
                                </div>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
            </form>

            <div class="col-xs-12 table-responsive">
	            <table id="example1" class="table table-hover table-striped">
	              <thead>
	                <tr>
                    <th>No</th>
                    <th>Kode BOM</th>
                    <th>Tanggal Dibuat</th>
                    <th>Nama BOM</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
	                  <th>qty</th>
	                  <th>uom</th>
	                  <th>qty2</th>
	                  <th>uom2</th>
	                  <th>Status</th>
	                </tr>
	              </thead>
	            </table>
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
<!-- /.Site wrapper -->

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">
    var table;
    $(document).ready(function() {
 
        //datatables
        table = $('#example1').DataTable({ 
            "stateSave": true,
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",

            "aLengthMenu": [[100, 500, 1000, -1], [100, 500, 1000, "All"]],
            "iDisplayLength": 100,
            "processing": true, 
            "serverSide": true, 
            "order": [], 

            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('ppic/billofmaterials/get_data')?>",
                "type": "POST",
                 "data": function ( data ) {
                    data.kode_bom    = $('#kode_bom').val();
                    data.nama_bom    = $('#nama_bom').val();
                    data.kode_produk = $('#kode_produk').val();
                    data.nama_produk = $('#nama_produk').val();
                    data.status      = $('#status').val();

                },error: function() {
                    // Message also does not show here
                  alert("error Load");
                  $('#btn-filter').button('reset');
                },
                // "data":{"id_dept" : "<?php echo $id_dept;?>"}
            },
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              {
                "targets" : 3,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-300'>" + data + "</div>";
                }
              }
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
