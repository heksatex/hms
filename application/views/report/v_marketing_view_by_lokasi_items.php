
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
          <h3 class="box-title"><b>Stock By Lokasi (GJD)</b></h3>
        </div>
        <div class="box-body ">
              <form name="input" class="form-horizontal" role="form">
                    <div class="col-md-12">
                      <div class="row col-md-6">
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Lokasi</label></div>
                                <div class="col-xs-8"><label>:</label> <?php echo $lokasi; ?></div>
                            </div>
                        </div> 
                      </div>
                    </div>
                    <div class="col-md-12">
                        <div class=" row col-md-6">
                          <div class="form-group">
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Total Lot</label></div>
                                  <div class="col-xs-8"  id="total_items"><label>:</label> 0 Lot </div>
                              </div>
                          </div>
                        </div>
                        <div class=" col-md-6">
                          <div class="form-group">
                              <div class="col-md-12 col-xs-12">
                                 <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                              </div>
                          </div>
                        </div>
                      </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive over">
                            <table class="table table-condesed table-hover rlstable over" width="100%" id="table_items" >
                                <thead>                          
                                    <tr>
                                        <th class="style width-50">No.</th>
                                        <th class="style ">Lot</th>
                                        <th class="style ">Corak</th>
                                        <th class="style ">Warna</th>
                                        <th class="style ws">Lebar Jadi</th>
                                        <th class="style text-right">Qty1 [JUAL]</th>
                                        <th class="style text-right">Qty2 [JUAL]</th>
                                        <th class="style ws">Lokasi Fisik / Rak</th>
                                        <th class="style ws">Lot/KP</th>
                                        <th class="style ws">SO/SC</th>
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
        table = $('#table_items').DataTable({ 
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
                "url": "<?php echo site_url('report/marketing/get_data_stock_by_lokasi')?>",
                "type": "POST",
                "data": {"lokasi": "<?php echo $lokasi;?>"}
            },
           
            "columnDefs": [
              { 
                "targets": [0], 
                "orderable": false, 
              },
              { 
                "targets": [5,6], 
                "className":"text-right nowrap",
              },
              { 
                "targets": [2,3], 
                "className":"nowrap",
              },
            ],
            "drawCallback": function( settings, start, end, max, total, pre ) {  
                // console.log(this.fnSettings()); /* for json response you can use it also*/ 
                let total_record = this.fnSettings().json.recordsTotal;
                $('#total_items').html('<label>:</label> '+ formatNumber(total_record) + ' Lot' )
            },
        });
 
    });

    function formatNumber(n) {
      return new Intl.NumberFormat('en-US').format(n);
    }

    // button excel
    $('#btn-excel').click(function(){
        $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/Marketing/export_excel_view_by_lokasi')?>",
            "data": {"lokasi": "<?php echo $lokasi;?>"},
            "dataType":'json',
            beforeSend: function() {
              $('#btn-excel').button('loading');
            },error: function(){
              alert('Error Export Excel');
              $('#btn-excel').button('reset');
            }
        }).done(function(data){
            if(data.status =="failed"){
              alert_modal_warning(data.message);
            }else{
              var $a = $("<a>");
              $a.attr("href",data.file);
              $("body").append($a);
              $a.attr("download",data.filename);
              $a[0].click();
              $a.remove();
            }
            $('#btn-excel').button('reset');
        });
    });



</script>

</body>
</html>
