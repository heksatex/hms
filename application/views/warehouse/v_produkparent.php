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
          <div class="col-xs-12 table-responsive">
            <table id="example1" class="table table-striped">
              <thead>
                <tr>
                  <th class="no">No</th>
                  <th>Nama</th>
                  <th>Tanggal dibuat</th>
                  <th>Jumlah Child</th>
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
            "stateSave": true,
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",
            "aLengthMenu": [[50, 100, 1000, -1], [50, 100, 1000, "All"]],
            "iDisplayLength": 50,
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
                "url": "<?php echo site_url('warehouse/produkparent/get_data')?>",
                "type": "POST",
                error: function() {
                  alert("error Load");
                },
            },
            "columnDefs": [
                { 
                    "targets": [ 0 ], 
                    "orderable": false, 
                },
                { 
                  "targets": [ 2 ], 
                  "width" : 150,
                },
                { 
                  "targets": [ 3 ], 
                  "width" : 150,
                },
            ],
        });
 
    });

    $('#btn-tambah').on("click",function(e) {
       
        $("#tambah_data").modal({
            show: true,
            backdrop: 'static'
        });
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('id',"btn-tambah-parent");
        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Add Product Parent' );
        $.post('<?php echo site_url()?>warehouse/produkparent/add_parent_produk',
            function(html){
              setTimeout(function() {$(".tambah_data").html(html); },1000);
            }   
         ); 
    });

    function view_parent(id)
    {
        $("#edit_data2").modal({
            show: true,
            backdrop: 'static'
        });
        $(".edit_data2").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('View Product Parent' );
        $.post('<?php echo site_url()?>warehouse/produkparent/view_parent_produk',
            {id:id},
            function(html){
              setTimeout(function() {$(".edit_data2").html(html);  },1000);
            }   
         ); 
    }

    $(".modal").on('hidden.bs.modal', function(){
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah-parent").attr('id',"btn-tambah");
        //$("#tambah_data .modal-dialog .modal-content .tambah_data").html('');
        table.ajax.reload( function(){});
    });
 
</script>

</body>
</html>
