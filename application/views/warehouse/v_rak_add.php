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
               <div class="col-md-4 col-xs-4">
                <center><label>Arah Panah</label>
                <div class="form-group">
                  <div class="rado">
                    <label class="fa fa-arrow-up" style=" font-size: 32px;"> </label>
                    <input type="radio" name="arah_panah" id="arah_panah1" value="1" checked>
                  </div>
                  <div class="rado">
                    <label class="fa fa-arrow-down" style=" font-size: 32px;"> </label>
                    <input type="radio" name="arah_panah" id="arah_panah2" value="0">
                  </div>
                </div>
                </center>
               </div>
              <div class="col-md-8 col-xs-8">
                  <div class="col-md-3"><label style="font-size: 30px">A </label> <label>Aisle</label>
                    <input type="text"  class="form-control" name="aisle" id="aisle"  placeholder="A"  onkeyup="return myFunction(this,'bay')" maxlength="2" >
                  </div>
                  <div class="col-md-3"><label style="font-size: 30px">B </label> <label>Bay</label>
                    <input type="text"  class="form-control" name="bay" id="bay"  placeholder="B"  onkeyup="return myFunction(this,'slot')" maxlength="2">                
                  </div>
                  <div class="col-md-3"><label style="font-size: 30px">S </label> <label>Slot</label>
                    <input type="text"  class="form-control" name="slot" id="slot" placeholder="S"  onkeyup="return myFunction(this,'kode_rak')" maxlength="2" >
                  </div>
              </div>     
            </div>       
            <div class="form-group">                  
              <div class="col-md-6 ">
                <div class="col-md-5"><label>Kode Rak / Nama Rak</label></div>
                  <div class="col-md-7 ">
                   <input type="text" class="form-control" name="kode_rak" id="kode_rak" readonly="readonly"> 
                  </div>                                    
                <div class="col-md-5"><label>Departemen</label></div>
                  <div class="col-md-7 ">
                    <select class="form-control input-sm" name="departemen" id="departemen" >
                    <option value="">Pilih Departemen</option>
                      <?php foreach ($warehouse as $row) {?>
                         <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                      <?php  }?>
                    </select>                 
                  </div>
                <div class="col-md-5"><label>Status Aktif</label></div>
                  <div class="col-md-7 ">
                    <select class="form-control input-sm" name="status" id="status" >
                    <option value='t'>Aktif</option>
                    <option value='f'>Tidak Aktif</option>
                    </select>                 
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

  // focus to aisle
  $('#aisle').focus();
  $("input[id='arah_panah1']").prop("checked", true);

  function myFunction(field,nextFieldID){
   
    var aisle = $('#aisle').val();
    var bay  = $('#bay').val();
    var slot = $('#slot').val();

    if(bay!= ''){
      var b = '.'+bay;
    }else{
      b = bay;
    }

    if(slot!= ''){
      var s = '.'+slot;
    }else{
      s = slot;
    } 
    $('#kode_rak').val(aisle+''+b+''+s);

    //focus next textbox
    if(field.value.length >= field.maxLength){
      document.getElementById(nextFieldID).focus();
    }

  }

  $("#btn-simpan").on('click', function() {

      var kode_rak   = '';
      var departemen = $('#departemen').val();
      var aisle = $('#aisle').val();
      var bay   = $('#bay').val();
      var slot  = $('#slot').val();
      var panah = $("input[name='arah_panah']:checked"). val();
      var status  = $('#status').val();
      var valid = true;

      kode_rak = $('#kode_rak').val();
      //alert(kode_lokasi);
      //return false; 
      /*
      if(aisle == ''){
        alert('Aisle Tidak boleh Kosong !');
        valid = false;
        document.getElementById('aisle').focus();             
      }else if(bay == ''){
        alert('Bay  Tidak boleh Kosong !');
        valid = false;
        document.getElementById('bay').focus();             
      }else if(slot == ''){
        alert('Slot Tidak boleh Kosong !');
        valid = false;
        document.getElementById('slot').focus();             
      }
      */
      
      if(valid == true){
        
        $('#btn-simpan').button('loading');
        please_wait(function(){});

        $.ajax({
            dataType: "JSON",
            url :'<?php echo base_url('warehouse/rak/simpan')?>',
            type: "POST",
            data: {kode_rak      : kode_rak,
                   departemen   : departemen,
                   aisle        : aisle,
                   bay          : bay,
                   slot         : slot,
                   panah        : panah,
                   status       : status,
                   aksi         : 'baru'
                    },
            success: function(data){
             if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika ada form belum keiisi
              $('#btn-simpan').button('reset');
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();              
            }else{
             //jika berhasil disimpan/diubah
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                  window.location.replace('edit/'+data.isi);
                },1000); 
                });
              });
            }
            $('#btn-simpan').button('reset');
            },
            error: function (jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseText);
                $('#btn-simpan').button('reset');
                unblockUI( function() {});
            }
        });
        
      }
  });
  
</script>


</body>
</html>
