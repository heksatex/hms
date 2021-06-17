
<!DOCTYPE html>
<html>
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
     <?php $this->load->view("admin/_partials/statusbar.php") ?>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Receiving IN Manual</h3>
          
        </div>
        <div class="box-body">
          <form class="form-horizontal" id="form_receiving">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">

              <div class="col-md-6">

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>RCV/IN/ </label></div>
                  <div class="col-xs-5">
                    <input type="text" class="form-control input-sm" name="rcv_in" id="rcv_in"/>
                  </div>
                  <div class="col-xs-3">
                    <button type="button" name="proses" id="btn-proses" class="btn btn-sm btn-primary" value="Proses" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." >Proses</button> 
                  </div>
                </div>

                <br>
                <br>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>No </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly"/>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Status </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="status" id="status" readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Creation Date </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="creation_date" id="creation_date" readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Source Document </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="source_doc" id="source_doc" readonly="readonly" />
                  </div>                                    
                </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Note </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"></textarea>
                  </div>                                    
                </div>
              </div>

            </div>
           
          </form>

          <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Products</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="table_items">
                          <head>
                            <tr>
                              <th class="style no">No</th>
                              <th class="style">kode Produk</th>
                              <th class="style">Nama Produk</th>
                              <th class="style">Lot</th>
                              <th class="style">Qty</th>
                              <th class="style">uom</th>
                              <th class="style">status</th>
                            </tr>
                          </head>
                          <tbody>
                            <tr>
                              <td colspan="6" align="center">Tidak ada Data</td>
                            </tr>
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
   <?php $this->load->view("admin/_partials/modal.php") ?>
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  //enter di textfield RCV/IN/...
  $("#rcv_in").on("keypress", function (e) {
      if (e.keyCode === 13) {  
          proses(e);
      };
  })


  //klik button proses
  $('#btn-proses').click(function(){
      proses();
  });

  function proses(){
    var no_rcv = $('#rcv_in').val();
    if(no_rcv == ''){
        document.getElementById('rcv_in').focus()
        alert_notify('fa fa-check','NO Receiving IN tidak boleh Kosong !','danger');
        //alert_modal_warning('NO Receiving IN tidak boleh Kosong !');
    }else{
      var no_rcv_in = 'RCV/IN/'+no_rcv;
      $('#btn-proses').button('loading');
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/receivinginmanual/get_receiving_in_by_kode')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {no_rcv_in    : no_rcv_in,

         },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              $("#form_receiving")[0].reset();   
              //alert_notify(data.icon,data.message,data.type);
              $("#table_items").load(location.href + " #table_items");
              $('#table_items tbody:last').empty();
              //alert(data.message);
              alert_modal_warning(data.message);  
              $('#btn-proses').button('reset');

            }else{
              //jika berhasil 
              $("#kode").val(data.no_receiving);
              $("#creation_date").val(data.creation_date);
              $("#source_doc").val(data.source_doc);
              $("#status").val(data.status);
              $("#note").val(data.note);
              var no      = 1;
              var row     = ""
              var empty   = true;
              $('#table_items tbody:last').empty();

              $.each(data.items, function(index,item){

                var row   ='<tr class="">'
                  + '<td>'+no+'</td>'
                  + '<td>'+item.default_code+'</td>'
                  + '<td>'+item.name_template+'</td>'
                  + '<td>'+item.lot+'</td>'
                  + '<td>'+item.qty+'</td>'
                  + '<td>'+item.uom+'</td>'
                  + '<td>'+item.state+'</td>'
                  + '</tr>';
                $('#table_items tbody:last').append(row);
                no = no + 1;                  
                empty = false;
              }); 

              if(empty == true){
                var row   ='<tr class="">'
                  + '<td colspan="6" align="center">Tidak ada Data</td>'
                  + '</tr>';
                $('#table_items tbody:last').append(row);
              }
              $('#btn-proses').button('reset');
              alert_notify(data.icon,data.message,data.type);

            }

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText+'error');
            $('#btn-proses').button('reset');
            //unblockUI( function(){});
            //$('#btn-simpan').button('reset');
            
          }
      });
    }
  }


  //klik button simpan
    $('#btn-simpan').click(function(){
      var status = $('#status').val();
      
      if(status != 'done'){
        alert_modal_warning('No Receiving IN Belum ditransfer !');

      }else{
        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
           type: "POST",
           dataType: "json",
           url :'<?php echo base_url('warehouse/receivinginmanual/simpan')?>',
           beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
           },
           data: {kode       : $('#kode').val(),
                  status     : status,
                  creation_date : $('#creation_date').val(),
                  source_doc    : $('#source_doc').val(),
                  note       : $('#note').val()

            },success: function(data){
              if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('index');
              }else if(data.status == "failed"){
                //jika ada form belum keiisi
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                });
              }else{
                //jika berhasil disimpan
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                });
                $('#table_items tbody:last').empty();
                var row   ='<tr class="">'
                  + '<td colspan="6" align="center">Tidak ada Data</td>'
                  + '</tr>';
                $('#table_items tbody:last').append(row);
                $("#form_receiving")[0].reset();   
              }
              $('#btn-simpan').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.responseText);
              unblockUI( function(){});
              $('#btn-simpan').button('reset');
            }
        });
      }

    });
   
</script>


</body>
</html>
