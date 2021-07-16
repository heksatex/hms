
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
            <div class="col-md-6">
              <div class="form-group"> 
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode</label></div>
                   <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="kode" id="kode"  />
                    </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Status </label></div>
                  <div class="col-xs-8 col-md-8">
                    <select class="form-control input-sm" name="status" id="status">
                        <option value="">All</option>
                        <option value="draft">Draft</option>
                        <option value="ready">Ready</option>
                        <option value="done">Done</option>
                        <option value="cancel">Batal</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Note</label></div>
                   <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="note" id="note" />
                    </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Departemen</label></div>
                   <div class="col-xs-8 col-md-8 ">
                    <select class="form-control input-sm" name="departemen" id="departemen" >
                    <option value="">All</option>
                      <?php foreach ($warehouse as $row) {?>
                         <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                      <?php  }?>
                    </select>                 
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
                  <th>No</th>
                  <th>Kode</th>
                  <th>Tanggal dibuat</th>
                  <th>Departemen</th>
                  <th>Lokasi Tujuan</th>
                  <th>Total </th>
                  <th>Note</th>
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
                "url": "<?php echo site_url('warehouse/transferlokasi/get_data')?>",
                "type": "POST",
                 "data": function ( data ) {
                    data.dept_id = $('#departemen').val();
                    data.kode    = $('#kode').val();
                    data.status  = $('#status').val();
                    data.note    = $('#note').val();
                },
                //"data":{"id_dept" : "<?php echo $id_dept;?>"}
            },
 
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              {
                "targets" : 1,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-140'>" + data + "</div>";
                }
              },
              {
                "targets" : 6,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-150'>" + data + "</div>";
                }
              },
              {
                "targets" : 7,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-130'>" + data + "</div>";
                }
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
