
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    .ws{
      white-space: nowrap;
    }

    @media (max-width: 767px) {
      .top-bar{
        display:none !important;
      }

      .content-wrapper{
        padding-top : 50px !important;
        margin-top  : 10px !important;
      }
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
      <div class="box ">
        <div class="box-header with-border">
          <h3 class="box-title"><b>View Product Ready Goods</b></h3>
        </div>
        <div class="box-body ">
              <form name="input" class="form-horizontal" role="form">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Total Lot</label></div>
                                <div class="col-xs-8"  id="total_items"><label>:</label> 0 Lot </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive over">
                            <table class="table table-condesed table-hover rlstable over" width="100%" id="table_group" >
                                <thead>                          
                                    <tr>
                                        <th class="style width-50">No.</th>
                                        <th class="style ">Corak</th>
                                        <th class="style ">Lebar Jadi</th>
                                        <th class="style ">Uom1</th>
                                        <th class="style ">Uom2</th>
                                        <th class="style ws">Gl / Lot</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
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
        table = $('#table_group').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
          
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "lengthMenu": [
                [10, 50, 100, 500, -1],
                [10, 50, 100, 500, 'All']
            ],
            "ajax": {
                "url": "<?php echo site_url('report/marketing/get_data_ready_goods_group')?>",
                "type": "POST",
                // "data": {"product": "", "color":"", "marketing":"",}
            },
           
            "columnDefs": [
              { 
                "targets": [0], 
                "orderable": false, 
              },
              { 
                "targets": [2,3,4,5], 
                "className":"text-right nowrap",
              },
              { 
                "targets": [1], 
                // "className":"nowrap",
              },
            ],
            "drawCallback": function( settings, start, end, max, total, pre ) {  
                // console.log(this.fnSettings().json); /* for json response you can use it also*/ 
                // console.log(settings.json.total_lot); 
                let total_record = settings.json.total_lot; // total glpcs
                $('#total_items').html('<label>:</label> '+ formatNumber(total_record) + ' Lot' )
            },
        });
 
    });

    function formatNumber(n) {
      return new Intl.NumberFormat('en-US').format(n);
    }



</script>

</body>
</html>
