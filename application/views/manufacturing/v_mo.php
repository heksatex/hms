
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
   <?php $this->load->view("admin/_partials/topbar.php") ?>
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
        <div class="box-header with-border">
          <h3 class="box-title"><b>LIST MODE</b></h3>
          <div class="image pull-right text-right">
              <?php 
                $dept = "jadwal_".$this->uri->segment(3); 
              ?>
              <!--a href="<?php echo base_url("manufacturing/mO/".$dept);?>"  data-toggle="tooltip" title="Kanban Mode">
                <img src="<?php echo base_url('dist/img/kanban.png'); ?>" style="width: 7%; height: auto; text-align: right;" >
              </a-->
          </div>
        </div>
        <div class="box-body">
          <div class="col-xs-12 table-responsive">
            <table id="example1" class="table table-striped">
              <thead>
                <tr>
                  <th class="no">No</th>
                  <th>Kode</th>
                  <th>Tanggal</th>
                  <th>Product</th>
                  <th>qty</th>
                  <th>uom</th>
                  <th>reff note PPIC</th>
                  <th>responsible</th>
                  <th>status</th>
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
                "url": "<?php echo site_url('manufacturing/mO/get_data')?>",
                "type": "POST",
                "data":{"id_dept" : "<?php echo $id_dept;?>"}
            },
 
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              {
                "className" : "text-right",
                 render: $.fn.dataTable.render.number(',', '.', 2, ''),
                "targets"   : [4]
              },
              {
                "targets" : 6,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-300'>" + data + "</div>";
                }
              },
            ],

 
        });
 
    });
 
</script>

</body>
</html>
