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
          <h3 class="box-title">Form Tambah</h3>          
        </div>

        <div class="box-body">
          <form class="form-horizontal">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">

              <div class="col-md-6">

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode BOM </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_bom" id="kode_bom" readonly="readonly"/>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Produk </label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="sel2_nama_produk" id="sel2_nama_produk"/></select>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama BOM </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="nama_bom" id="nama_bom" />
                    <input type="hidden" class="form-control input-sm" name="nama_produk" id="nama_produk" readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty </label></div>
                  <div class="col-xs-4">
                    <input type="text" class="form-control input-sm" name="qty" id="qty" onkeyup="validAngka(this)"  />
                  </div>
                  <div class="col-xs-4">
                     <select class="form-control input-sm" name="sel2_uom" id="sel2_uom" >
                  </select>    
                  </div>
                </div>

              </div>

            </div>
           
          </form>

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

    function validAngka(a){
      if(!/^[0-9.]+$/.test(a.value)){
        a.value = a.value.substring(0,a.value.length-1000);
        alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger');
      }
    }

    //select 2 product
    $('#sel2_nama_produk').select2({
      allowClear: true,
      placeholder: "",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>ppic/billofmaterials/get_produk_bom_select2",
            //delay : 250,
            data : function(params){
              return{
                prod:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.kode_produk,
                    //text:item.nama_produk
                    text:'['+item.kode_produk+'] '+item.nama_produk
                });
              });
              return {
                results:results
              };
            },
            error: function (xhr, ajaxOptions, thrownError){
              //alert('Error data');
              //alert(xhr.responseText);
            }
      }
    });

    //jika nama_bom diubah
    $("#sel2_nama_produk").change(function(){
      $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('ppic/billofmaterials/get_prod_by_id') ?>',
          type: "POST",
          data: {kode_produk: $("#sel2_nama_produk").val() },
          success: function(data){
            $('#kode_produk').val(data.kode_produk);
            $('#nama_bom').val(data.nama_produk);
            $('#nama_produk').val(data.nama_produk);
            $('#qty').val(data.qty);
            //$('#sel2_uom').val(data.uom);
            var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
            $("#sel2_uom").empty().append($newOptionuom).trigger('change');

          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }
      });
    });


    //select 2 uom 
    $('#sel2_uom').select2({
        allowClear: true,
        placeholder: "",
        ajax:{
            dataType: 'JSON',
            type : "POST",
            url  : "<?php echo base_url();?>ppic/billofmaterials/get_uom_select2",
            data : function(params){
              return{
                  prod:params.term,
              };
            }, 
            processResults:function(data){
                var results = [];
                $.each(data, function(index,item){
                  results.push({
                    id:item.short,
                    text:item.short
                   });
                });
                return {
                  results:results
                };
            },
            error: function (xhr, ajaxOptions, thrownError){
                //alert('Error data');
                //alert(xhr.responseText);
            }
        }
    });  


    //klik button simpan
    $('#btn-simpan').click(function(){
        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
           type: "POST",
           dataType: "json",
           url :'<?php echo base_url('ppic/billofmaterials/simpan')?>',
           beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
           },
           data: {kode_bom       : $('#kode_bom').val(),
                  nama_bom       : $('#nama_bom').val(),
                  kode_produk    : $('#sel2_nama_produk').val(),
                  nama_produk    : $('#nama_produk').val(),
                  qty            : $('#qty').val(),
                  uom            : $('#sel2_uom').val()

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
                $('#kode').val(data.isi);
                unblockUI( function() {
                  setTimeout(function() { 
                    alert_notify(data.icon,data.message,data.type, function(){
                     
                    window.location.replace('edit/'+data.kode_encrypt);
                  },1000); 
                  });
                });
              }
              $('#btn-simpan').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.responseText);
              unblockUI( function(){});
              $('#btn-simpan').button('reset');
            }
        });
        

    });
   
</script>


</body>
</html>
