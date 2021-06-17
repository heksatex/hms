
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

            <div class="col-xs-12 table-responsive">
	            <table id="example1" class="table table-striped">
	              <thead>
	                <tr>
                    <th>No</th>
                    <th>Kode BOM</th>
                    <th>Nama BOM</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
	                  <th>qty</th>
	                  <th>uom</th>
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
                /*
                 "data": function ( data ) {
                    data.id_dept = "<?php echo $id_dept;?>"
                    data.kode = $('#kode').val();
                    data.status = $('#status').val();
                    data.reff = $('#reff').val();
                    data.reff_picking = $('#reff_picking').val();
                },*/
                "data":{"id_dept" : "<?php echo $id_dept;?>"}
            },
            "columnDefs": [
              {
                "targets" : 2,
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
