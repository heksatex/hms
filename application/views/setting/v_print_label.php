
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
     
            <div class="col-xs-12 table-responsive">
                <table id="example1" class="table table-striped">
                <thead>
                    <tr>
                    <th class='no'>No</th>
                    <th>Seri Beam</th>
                    <th>UI buat</th>
                    <th>Type Beam</th>
                    <th>Berat Kosong</th>
                    <th></th>
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

<div id="load_modal">
    <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>
</div>

<script type="text/javascript">

    
    var table;
    $(document).ready(function() {
 
        //datatables
        var table = $('#example1').DataTable({ 
            // "stateSave": true,
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
                "url": "<?php echo site_url('setting/printlabel/get_data')?>",
                "type": "POST",
                 "data": function ( data ) {},
            },
 
            "columnDefs": [
                { 
                    "targets": [ 0 ], 
                    "orderable": false, 
                },
                {
                'targets':5,
                // 'data' : 12,
                'checkboxes': {
                    'selectRow': true
                    },
                },
            ],
            "select": {
                'style': 'multi'
            },
          
          
        });

  

         $(document).on('click','#btn-print',function(e){
            e.preventDefault();

            var myCheckboxes = table.column(5).checkboxes.selected();
            var myCheckboxes_arr = new Array();
            $.each(myCheckboxes, function(index, rowId){        
                myCheckboxes_arr.push(rowId);
            });
            var arrStr = encodeURIComponent(JSON.stringify(myCheckboxes_arr));
            var url = '<?php echo base_url() ?>setting/printlabel/print';
            window.open(url+'?id='+ arrStr,'_blank');

        });

    });

    
 
</script>

</body>
</html>
