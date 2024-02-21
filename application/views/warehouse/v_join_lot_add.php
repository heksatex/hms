<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    .select2-container--focus{
        border:  1px solid #66afe9;
    }

    .select2-container--default .select2-selection--single{
        height : 30px;
        font-size : 12px;
        padding: 5px 12px;
    }

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
  <?php $this->load->view("admin/_partials/sidebar.php") ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header">
     <?php $this->load->view("admin/_partials/statusbar.php") ?>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Add Join Lot </h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">
             
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode  </label></div>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="kode" id="kode"  readonly="readonly" >                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                            </div>                                    
                        </div>
                        
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal transaksi</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                            </div>                                    
                        </div>

                        <div class="col-md-12 col-xs-12" >
                            <div class="col-xs-4"><label>Departemen</label></div>
                            <div class="col-xs-8">
                                <select class="form-control input-sm select2" name="departemen" id="departemen">
                                <option value="">Pilih Departemen</option>
                                <?php foreach ($warehouse as $row) { ?>
                                    <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                                <?php }?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                            <div class="col-xs-4"><label>Note </label></div>
                            <div id="ta" class="col-xs-8">
                                <textarea class="form-control input-sm" name="note" id="note" ></textarea>
                            </div>                                    
                        </div>
                    </div>

                    <div class="col-md-6" >
                    </div>

                </div>
            
            </form>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Join Items</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable  over" id="table_items" style="border-bottom:0px !important; width:100%">
                          <thead>                          
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style" width="min-width:80px">Kode Produk</th>
                              <th class="style" style="min-width:80px;" >Nama Produk</th>
                              <th class="style" style="min-width:100px;" >Corak Remark</th>
                              <th class="style" style="min-width:100px;" >Warna Remark</th>
                              <th class="style" style="min-width:100px;" >Lot</th>
                              <th class="style" style="min-width:50px;" >Grade</th>
                              <th class="style" style="min-width:80px;" >Qty</th>
                              <th class="style" style="min-width:80px;" >Qty2</th>
                              <th class="style" style="min-width:80px;" >Qty Jual</th>
                              <th class="style" style="min-width:80px;" >Qty2 Jual</th>
                              <th class="style" style="min-width:80px;" >Lbr.Jadi</th>
                              <th class="style" width="50px"></th>
                            </tr>
                          </thead>
                          <tbody>
                           
                          </tbody>
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>
                    <!-- /.tab-pane -->
              
                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div>
              <!-- /.col -->
            </div>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    // // untuk focus after select2 close
    $(document).on('focus', '.select2', function (e) {
        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');

            // Set focus back to select2 element on closing.
            s2element.on('select2:closing', function (e) {
                s2element.select2('focus');
            });
        }
    });

    $(document).on('select2:opening', '.select2', function (e) {
        if( $(this).attr('readonly') == 'readonly') {
            //   console.log( 'can not open : readonly' );
            e.preventDefault();
            $(this).select2('close');
            return false;
        }else{
            //   console.log( 'can be open : free' );
        }
    });

    $('.select2').select2({
        allowClear: true,
        placeholder: 'Pilih',
        width: '100%'
    });

    $("#departemen").val('GJD').trigger('change');

    // simpan 
    $(document).on("click", "#btn-simpan", function(){
            
        let dept = $("#departemen").val();
        let note = $("#note").val();

        if(dept.length === 0){
            alert_notify("fa fa-warning","Departemen Harus diisi !","danger",function(){});
            $('#departemen').focus();
        }else if(note.length === 0){
            alert_notify("fa fa-warning","Note Harus diisi !","danger",function(){});
            $('#note').focus();
        }else{
            $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : '<?php echo base_url('warehouse/joinlot/save_join_lot')?>',
                    beforeSend: function(e) {
                        if(e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }                  
                        $('#btn-simpan').button('loading');
                        please_wait(function(){});
                    },
                    data: {dept:dept, note:note},
                    success: function(data){
                       if(data.status == 'failed'){
                            unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                        $('#btn-simpan').button('reset');
                                });},1000); 
                            });
                        }else{
                            unblockUI(function() {
                                setTimeout(function() {
                                    alert_notify(data.icon, data.message, data.type,function() {
                                    window.location.replace('edit/'+data.isi);
                                    }, 1000);
                                });
                            });
                            $('#btn-simpan').button('reset');
                        }
                            
                    },error: function (xhr, ajaxOptions, thrownError) {
                        unblockUI(function() {});
                        $('#btn-simpan').button('reset');
                        if(xhr.status == 401){
                            var err = JSON.parse(xhr.responseText);
                            alert(err.message);
                        }else{
                            alert("Error Simpan Data!")
                        }    
                    }
                });
            }


        });

   
</script>


</body>
</html>
