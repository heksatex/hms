
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
          <h3 class="box-title"><b>Changed Ready Goods Category</b></h3>
        </div>
        <div class="box-body ">
              <form name="input" class="form-horizontal" role="form">
                    <!-- <div class="col-md-12">
                      <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Total Lot</label></div>
                                <div class="col-xs-8"  id="total_items"><label>:</label> 0 Lot </div>
                            </div>
                        </div>
                      </div>
                    </div> -->
                    <div class="col-md-12">
                      <div class="col-md-8 col-lg-8">
                          <div class="col-sm-4 col-md-4 col-lg-4">
                            <button type="button" class="btn btn-sm btn-default" name="cetak-tag" id="cetak-tag" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-print"></i> Print Label</button>
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
                                        <th class="style ">Category</th>
                                        <th class="style ">Article</th>
                                        <th class="style ">Color</th>
                                        <th class="style ">Size</th>
                                        <th class="style ">Action</th>
                                        <th class="style "></th>
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
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "lengthMenu": [
                [10, 50, 100, 500, -1],
                [10, 50, 100, 500, 'All']
            ],
            "ajax": {
                "url": "<?php echo site_url('report/marketing/get_data_ready_goods_category_changed')?>",
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
                    "targets": [4], 
                    "className":"text-right nowrap",
                },
                { 
                    "targets": [1], 
                    // "className":"nowrap",
                },
                {
                    "targets" : 6,
                    
                    'checkboxes': {
                        'selectRow': true
                    },
                    'createdCell':  function (td, cellData, rowData, row, col){
                    var rowId = rowData[5];
                    if(rowId.includes('REMOVE') == true){  
                        this.api().cell(td).checkboxes.disable();
                    }
                    }, 
                },
            ],
            "select": {
              'style': 'multi'
            },
            'rowCallback': function(row, data, dataIndex){
               // Get row ID
               var rowId = data[5];
                // If row ID is in the list of selected row IDs
                if (rowId.includes('REMOVE') == true){     
                  $(row).find('input[type="checkbox"]').prop('disabled', true);
               }
            },
            "drawCallback": function( settings, start, end, max, total, pre ) {  
                // console.log(settings.json.total_lot); 
                // let total_record = settings.json.total_lot; // total glpcs
                // $('#total_items').html('<label>:</label> '+ formatNumber(total_record) + ' Lot' )
            },
        });

    });


    $("#cetak-kategori").on("click", function () {
      let url = "<?= base_url("report/marketing/print_category") ?>";
      var win = window.open(url, "width=1000,height=700");
      setTimeout(function () {
          win.document.close();
          // win.print();
          // win.close();
      }, 500);
    });


    $(document).on('click','#cetak-tag',function(e){
            e.preventDefault();

            var myCheckboxes = table.column(6).checkboxes.selected();
            var myCheckboxes_arr = new Array();

            $.each(myCheckboxes, function(index, rowId){        
                myCheckboxes_arr.push({rowId});
            });

            if (myCheckboxes.length === 0) {
                alert_notify('fa fa-warning', 'Pilih terlebih dahulu yang akan di print !', 'danger', function() {});
            }else{
                 $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url :'<?php echo base_url('report/marketing/print_category_tag')?>',
                    beforeSend: function(e) {
                        if(e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }                  
                        please_wait(function(){});
                    },
                    data: { changed:'true',
                          data_print:JSON.stringify(myCheckboxes_arr),
                    },
                    success: function(data){
                        var divp = document.getElementById('printed');
                        divp.innerHTML = data.data_print;
                        unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                        print_voucher();
                                });},1000); 
                        });
                            
                    },error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText)
                        unblockUI( function() {});
                    }
                });

               
            }
    });

    // load new page print
    function print_voucher() {
        var win = window.open();
        win.document.write($("#printed").html());
        win.document.close();
        setTimeout(function(){ win.print(); win.close();}, 200);
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
