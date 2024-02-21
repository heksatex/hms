
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
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Nama Produk</label></div>
                              <div class="col-xs-8">
                                  <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk" />
                                </div>                                    
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Lot </label></div>
                              <div class="col-xs-8">
                                  <input type="text" class="form-control input-sm" name="lot" id="lot" />
                                </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Note</label></div>
                              <div class="col-xs-8">
                                  <input type="text" class="form-control input-sm" name="note" id="note"  />
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
                    <th>Tanggal dibuat</th>
                    <th>Departemen</th>
                    <th>Kode produk</th>
                    <th>Nama produk</th>
                    <th>Lot</th>
                    <th>Qty</th>
                    <th>Qty2</th>
                    <th>Jml Split</th>
                    <th>Note</th>
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
                "url": "<?php echo site_url('warehouse/splitlot/get_data')?>",
                "type": "POST",
                 "data": function ( data ) {
                    data.dept_id      = $('#departemen').val();
                    data.nama_produk  = $('#nama_produk').val();
                    data.lot          = $('#lot').val();
                    data.note = $('#note').val();
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
