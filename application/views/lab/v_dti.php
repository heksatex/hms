
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
 
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
        <div class="box-body">

          <form name="input" class="form-horizontal" role="form" method="POST">
            <div class="col-md-6">
              <div class="form-group"> 
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Warna</label></div>
                   <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="warna" id="warna"  />
                    </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Status </label></div>
                  <div class="col-xs-8 col-md-8">
                    <select class="form-control input-sm" name="status" id="status">
                        <option value="">All</option>
                        <option value="draft">Draft</option>
                        <option value="ready">Ready</option>
                        <option value="requested">Requested</option>
                        <option value="done">Done</option>
                        <option value="cancel">Cancel</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Marketing </label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="marketing" id="marketing"  style="width:100% !important"> 
                      <option value="">-- Pilih Marketing --</option>
                          <?php 
                              foreach ($mst_sales_group as $val) {
                                echo "<option value='".$val->kode_sales_group."'>".$val->nama_sales_group."</option>";
                             }
                          ?>
                    </select>
                  </div>                                         
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes</label></div>
                   <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="notes" id="notes" />
                    </div>                                    
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label></label></div>
                   <div class="col-xs-8">
                      <button type="button" id="btn-filter" name="submit" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Proses</button>
                    </div>                                    
                </div>
              </div>
            </div>
          </form>

          <div class="col-xs-12 table-responsive">
            <table id="example1" class="table table-striped">
              <thead>
                <tr>
                  <th class="no">No</th>
                  <th>Nama Warna</th>
                  <th>Tanggal dibuat</th>
                  <th>Status</th>
                  <th>Marketing</th>
                  <th>Jml Varian</th>
                  <th>Notes</th>
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


  <?php $this->load->view("admin/_partials/modal.php") ?>
</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">
    var table;
    $(document).ready(function() {
 
        //datatables
        table = $('#example1').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "stateSave": true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('lab/dti/get_data')?>",
                "type": "POST",
                "data": function ( data ) {
                    data.warna     = $('#warna').val();
                    data.status   = $('#status').val();
                    data.marketing= $('#marketing').val();
                    data.notes    = $('#notes').val();
                },
            },
 
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              { 
                  "targets": [ 1 ], 
                  "width" : 200,
              },
              { 
                  "targets": [ 2 ], 
                  "width" : 100,
              },
              { 
                  "targets": [ 5 ], 
                  "width" : 70,
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
