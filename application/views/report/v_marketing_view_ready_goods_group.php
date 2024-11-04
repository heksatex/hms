
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
                  <div class="row">
                      <div class=" col-md-8">
                        <div class="col-md-12">
                          <div class="col-md-">
                            <div class="form-group">
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-xs-4"><label>Total Lot</label></div>
                                    <div class="col-xs-8"  id="total_items"><label>:</label> 0 Lot </div>
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="col-md-12 col-lg-12">
                              <div class="col-sm-3 col-md-3 col-lg-3">
                                <select class="form-control input-sm" name="cmbSearch" id="cmbSearch">
                                  <option value="uom_jual">Uom1</option>
                                </select>
                              </div>
                              <div id='f_search'>
                                <div class="col-sm-3 col-md-3 col-lg-3 " >
                                  <select class="form-control input-sm" name="cmbOperator" id="cmbOperator">
                                    <option value=">">Greather  than</option>
                                    <option value="<">Less than</option>
                                  </select>
                                </div>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                  <input type="number" class="form-control input-sm" id="search_field" name="search_field" onkeypress="return isNumberKey(event)" onkeydown=" event_input(event)" >
                                </div>
                              </div>
                              <div class="col-sm-4 col-md-4 col-lg-4">
                                <button type="button" class="btn btn-sm btn-default btn-flat" id="btn-search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <span class="fa fa-search" ></span> Proses</button>
                                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                              </div>
                          </div>   
                        
                        </div>
                      </div>
                      <div class="col-md-4">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                            <small><b>*Kondisi</b>
                                <li>Umur > 90 Hari</li>
                                <li>Lokasi tidak di (XPD, PORT, 6Z.01.Z, GJD4)</li>
                                <li>Kain Hasil Jacquard</li>
                                <li>Grade A</li>
                            </small>
                            </div>
                      </div>
                  </div>
                </form>
                &nbsp
                <div class="row">
                    <div class="col-md-12">
                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive over">
                            <table class="table table-condesed table-hover rlstable over" width="100%" id="table_group" >
                                <thead>                          
                                    <tr>
                                        <th class="style width-50">No.</th>
                                        <th class="style ">Corak</th>
                                        <th class="style ">Jml Warna</th>
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
                 "data": function ( data ) {
                    data.search_field = $('#search_field').val();
                    data.cmbSearch = $('#cmbSearch').val();
                    data.cmbOperator = $('#cmbOperator').val();
                },
            },
           
            "columnDefs": [
              { 
                "targets": [0], 
                "orderable": false, 
              },
              { 
                "targets": [2,3,4,5,6], 
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

        $('#btn-search').click(function(){ //button search event click
            $('#btn-search').button('loading');
            table.ajax.reload( function(){  
            $('#btn-search').button('reset');
          });  //just reload table
        });
 
    });

    function event_input(event){ 
      if(event.keyCode == 13) {
          event.preventDefault();
          $('#btn-search').button('loading');
            table.ajax.reload( function(){  
            $('#btn-search').button('reset');
          }); 
      }
    }

    function formatNumber(n) {
      return new Intl.NumberFormat('en-US').format(n);
    }

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    $('#btn-excel').click(function(){
        $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/Marketing/export_excel_ready_goods_group')?>",
            "data": {"search_field": $('#search_field').val(), "cmbSearch": $('#cmbSearch').val(), "cmbOperator": $('#cmbOperator').val()},
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
