
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
                    <div class="form-group">
                        <div class="col-md-12">
                        <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                            <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                <label style="cursor:pointer;">
                                <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                                Advanced  Search
                                </label>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default" style="margin-bottom: 0px;">
                        <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                            <div class="panel-body" style="padding: 5px">
                                <div class="form-group col-md-12" style="margin-bottom:0px">
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <div class="col-xs-4"><label>Departemen</label></div>
                                            <div class="col-xs-8 col-md-8 ">
                                                <select class="form-control input-sm" name="departemen" id="departemen" >
                                                <option value="">All</option>
                                                <?php foreach ($warehouse as $row) {?>
                                                    <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                                                <?php  }?>
                                                </select>                 
                                            </div>                                  
                                            <div class="col-xs-4"><label>Nama Produk</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk" />
                                            </div>
                                            <div class="col-xs-4"><label>Corak Remark</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark" />
                                            </div>                                    
                                            <div class="col-xs-4"><label>Warna Remark</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" name="warna_remark" id="warna_remark" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="col-xs-4"><label>Barcode/Lot </label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" name="lot" id="lot" />
                                            </div>
                                            <div class="col-xs-4"><label>Note</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" name="note" id="note"  />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" >
                                        <div class="form-group" >
                                            <div class="col-xs-8" style="padding-top:0px">
                                                <button type="button" id="btn-filter" name="submit" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Proses</button>
                                            </div>                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
            </form>
            <div class="col-xs-12 table-responsive">
                <table id="example1" class="table table-striped">
                <thead>
                    <tr>
                    <th class='no'>No</th>
                    <th>Kode</th>
                    <th>Tanggal buat</th>
                    <th>Tanggal transaksi</th>
                    <th>Departemen</th>
                    <th>Jml Join</th>
                    <th>Nama Produk</th>
                    <th>Corak Remark</th>
                    <th>Warna Remark</th>
                    <th>Barcode/Lot</th>
                    <th>Notes</th>
                    <th>Status</th>
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

    //* Show collapse advanced search
    $('#advancedSearch').on('shown.bs.collapse', function () {
       $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
    });

    //* Hide collapse advanced search
    $('#advancedSearch').on('hidden.bs.collapse', function () {
       $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
    });
    
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
                "url": "<?php echo site_url('warehouse/joinlot/get_data')?>",
                "type": "POST",
                 "data": function ( data ) {
                    data.dept_id      = $('#departemen').val();
                    data.nama_produk  = $('#nama_produk').val();
                    data.lot          = $('#lot').val();
                    data.warna_remark = $('#warna_remark').val();
                    data.corak_remark = $('#corak_remark').val();
                    data.note         = $('#note').val();
                },
            },
 
            "columnDefs": [
                { 
                    "targets": [ 0 ], 
                    "orderable": false, 
                },
                {
                    "targets" : 1,
                    render: function (data, type, full, meta) {
                            return "<div class='text-wrap width-110'>" + data + "</div>";
                    }
                },
                {
                    "targets" : 2,
                    render: function (data, type, full, meta) {
                            return "<div class='text-wrap width-70'>" + data + "</div>";
                    }
                },
                {
                'targets':12,
                'data' : 12,
                'checkboxes': {
                    'selectRow': true
                    },
                    'createdCell':  function (td, cellData, rowData, row, col){
                        var rowId = rowData[12];
                    },
                },
            ],
            "select": {
                'style': 'multi'
            },
            // 'rowCallback': function(row, data, dataIndex){
            //     var rowId = data[12];
            // }
            'rowCallback': function(row, data, dataIndex){
               // Get row ID
               var rowId = data[11];
                // If row ID is in the list of selected row IDs
                if (rowId != 'Done'){
                  $(row).find('input[type="checkbox"]').prop('disabled', true);
               }
            }
        });

        $('#btn-filter').click(function(){ //button filter event click
          $('#btn-filter').button('loading');
            table.ajax.reload( function(){
            $('#btn-filter').button('reset');
          });  //just reload table
        });
       
 
  

        $(document).on('click','#btn-print',function(e){
            e.preventDefault();

            var myCheckboxes = table.column(12).checkboxes.selected();
            var myCheckboxes_arr = new Array();

            $.each(myCheckboxes, function(index, rowId){        
                myCheckboxes_arr.push(rowId);
            });

            if (myCheckboxes.length === 0) {
                alert_notify('fa fa-warning', 'Pilih LOT terlebih dahulu yang akan di print !', 'danger', function() {});
            }else{
                $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $("#print_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $("#print_data .modal-dialog .modal-content .modal-footer #btn-print-modal").remove();

                $('.modal-title').text('Pilih Desain Barcode dan K3L ');
                $.post('<?php echo site_url()?>warehouse/joinlot/print_modal2',
                { data:myCheckboxes_arr},
                    function(html){
                        setTimeout(function() {$(".print_data").html(html);  },1000);
                        $("#print_data .modal-dialog .modal-content .modal-footer").prepend('<button class="btn btn-default btn-sm" id="btn-print-modal" name="btn-print-modal" >Print</button>');

                    }   
                );
            }
        });
    });

     // load new page print
    function print_voucher() {
        var win = window.open();
        win.document.write($("#printed").html());
        win.document.close();
        setTimeout(function(){ win.print(); win.close();}, 200);
    }
 
</script>

</body>
</html>
