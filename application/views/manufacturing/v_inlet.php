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
      $data['deptid'] = $id_dept;
      $this->load->view("admin/_partials/topbar.php", $data)
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
                          <div class="form-group">
                            <div class="col-md-8"> 
                              <div class="col-md-2"><label><input  type="checkbox" name="checkTgl" id="checkTgl" > Tgl. Inlet</label></div>
                              <div class="col-md-4">
                                <div class='input-group'>
                                  <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" required="" readonly="" >
                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>    
                                </div>
                              </div>
                              <div class="col-md-1">
                                  <label>s/d</label>
                              </div>
                              <div class="col-md-4">
                                <div class='input-group'>
                                  <input type="text" class="form-control input-sm" name="tglsampai" id='tglsampai' required="" readonly="" >
                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>    
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-md-12" style="margin-bottom:0px">
                              <div class="col-md-6">
                                <div class="form-group"> 
                                  <div class="col-xs-5"><label>KP/Lot</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="lot" id="lot" >
                                  </div>  
                                  <div class="col-xs-5"><label>Barcode GJD</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="lot_gjd" id="lot_gjd" >
                                  </div>  
                                  <div class="col-xs-5"><label>Marketing</label></div>
                                  <div class="col-xs-7">
                                      <select class="form-control input-sm" name="marketing" id="marketing" >
                                        <option value=""></option>
                                        <?php foreach ($sales_group as $row) {
                                                echo "<option value='".$row->kode_sales_group."'>".$row->nama_sales_group."</option>";
                                              }
                                        ?>
                                      </select>
                                  </div>                         
                                  <div class="col-xs-5"><label>MG GJD</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="mg" id="mg" >
                                  </div>                                    
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="col-xs-5"><label>Nama Produk</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk"  >
                                  </div> 
                                  <div class="col-xs-5"><label>Corak Remark</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark"  >
                                  </div>
                                  <div class="col-xs-5"><label>Warna Remark</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark"  >
                                  </div>
                                  <div class="col-xs-5"><label>Status</label></div>
                                  <div class="col-xs-7">
                                      <select class="form-control input-sm" name="status" id="status" >
                                        <option value=""></option>
                                        <?php foreach ($list_status as $row) {
                                                echo "<option value='".$row->jenis_status."'>".$row->nama_status."</option>";
                                              }
                                        ?>
                                      </select>
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
            <br>
            <div class="col-xs-12 table-responsive">
              <table id="example1" class="table table-striped table-condesed table-hover">
                <thead>
                  <tr>
                    <th class="no">No</th>
                    <th>KP/Lot</th>
                    <th>Tgl.Inlet</th>
                    <th>MG GJD</th>
                    <th>Marketing</th>
                    <th>Nama Produk</th>
                    <th>Corak Remark</th>
                    <th>Warna Remark</th>
                    <th>Lebar Jadi</th>
                    <th>Desain Barcode</th>
                    <th>Status</th>
                    <th>#</th>
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
    $(function () {

      var d     = new Date();
      var month = d.getMonth();
      var day   = d.getDate();
      var year  = d.getFullYear();
      defaultDatesampai = new Date(year, month, day, 23, 59, 59);
      $('#checkTgl').on('change',function(){
        let checkTgl = document.getElementById("checkTgl");

        if(checkTgl.checked == true){

          // readonly tgl false
          $('#tgldari').prop('readonly',false);
          $('#tglsampai').prop('readonly',false);

          // set date tgldari
          $('#tgldari').datetimepicker({
              defaultDate : new Date(year, month, day, 00, 00, 00),
              format : 'D-MMMM-YYYY HH:mm:ss',
              // ignoreReadonly: true,
              maxDate: new Date(),
          });
    
          // set date tglsampai
          $('#tglsampai').datetimepicker({
              defaultDate : new Date(year, month, day, 23, 59, 59),
              format : 'D-MMMM-YYYY HH:mm:ss',
              // ignoreReadonly: true,
              maxDate: new Date(year, month, day, 23, 59, 59),
          });
          // $('#tglsampai').val(d);

        }else{
          // readonly tgl true
          $('#tgldari').prop('readonly',true);
          $('#tglsampai').prop('readonly',true);
          
        }
      });
        
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
          "url": "<?php echo site_url('manufacturing/inlet/get_data') ?>",
          "type": "POST",
          "data": function ( data ) {
                    var check = 0;
                    if($("#checkTgl").is(":checked") == true){
                      check = 1;
                    }
                    data.sales_group = $('#marketing').val();
                    data.lot         = $('#lot').val();
                    data.lot_gjd     = $('#lot_gjd').val();
                    data.nama_produk = $('#nama_produk').val();
                    data.mg          = $('#mg').val();
                    data.corak_remark= $('#corak_remark').val();
                    data.warna_remark= $('#warna_remark').val();
                    data.status      = $('#status').val();
                    data.checkTgl    = check;
                    data.tgldari     = $('#tgldari').val();
                    data.tglsampai   = $('#tglsampai').val();
          },"error": function() {
            // Message also does not show here
            alert("error Load");
            $('#btn-filter').button('reset');
          },
        },

        "columnDefs": [
          {
            "targets": [0],
            "orderable": false,
          },
          {
            "targets": 1,
            render: function (data, type, full, meta) {
              return "<div class='text-wrap width-150'>" + data + "</div>";
            }
          },
          {
            "targets": 5,
            render: function (data, type, full, meta) {
              return "<div classv_m'text-wrap width-300'>" + data + "</div>";
            }
          },
        ],
      });

      $('#btn-filter').click(function(){ //button filter event click
        $('#btn-filter').button('loading');
        table.ajax.reload( function(){
          $('#btn-filter').button('reset');
        });
      });

      // function reload
      function reload_table(event){
        if(event.keyCode == 13) {
          $('#btn-filter').button('loading');
          table.ajax.reload( function(){
            $('#btn-filter').button('reset');
          });
        }
      }
      

      $('#example1 tbody').on('click', '.btn-delete-inlet', function (e) {
          e.preventDefault();
          let id = $(this).data('id');
          let lot = $(this).data('lot');
          let btn_load = $(this);
          bootbox.confirm({
            message: "Apa anda yakin ingin menghapus/ Membatalkan Inlet KP/Lot <b>"+lot+" </b> ini ?",
            title: "<i class='glyphicon glyphicon-trash' style='color: red'></i> Delete !",
            buttons: {
              confirm: {
                label: 'Yes',
                className: 'btn-primary btn-sm'
              },
              cancel: {
                label: 'No',
                className: 'btn-default btn-sm'
              },
            },
            callback: function (result) {
              if(result == true){
             
                btn_load.button('loading');
                please_wait(function(){});
                $.ajax({
                    type: "POST",
                    url :'<?php echo base_url('manufacturing/inlet/delete_inlet')?>',
                    dataType: 'JSON',
                    data    : {id:id,lot:lot},
                    statusCode: {
                      404: function() {
                        alert( "page not found" );
                      },
                      500: function(){
                        alert('Gagagallll')
                      }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                      // if(xhr.status == 404){
                      //   alert_modal_warning('Page Not Found');
                      // }
                      unblockUI( function(){});
                      btn_load.button('reset');
                    }
                }).done(function(res){
                      btn_load.button('reset');
                      unblockUI( function(){
                          setTimeout(function() { alert_notify(res.icon,res.message,res.type,function(){});}, 1000);
                      });
                      table.ajax.reload( function(){});
                });

              }
            }
          });

      });

    });

  </script>

</body>

</html>