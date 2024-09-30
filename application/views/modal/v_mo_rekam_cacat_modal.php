<style type="text/css">
  .modal-open .select2-dropdown {
    z-index: 10060;
  }

  .modal-open .select2-close-mask {
    z-index: 10055;
  }
  
  .error{
    border:  1px solid red;
  } 

  table.table td .cancel_cacat {
        display: none;
        color : red;
        min-width:  24px;
  }
</style>


<form class="form-horizontal" id="form_cacat" name="form_cacat">
  <input type="hidden" name="deptid" id="deptid" class="form-control input-sm " value="<?php echo $deptid;?>" />
  <input type="hidden" name="quant_id" id="quant_id" class="form-control input-sm " value="<?php echo $quant_id;?>" />
 
  <div class="form-group">
		<div class="col-md-12">
			<div class="col-md-12 col-xs-12">
	        <label>Input Cacat Batch</label>
		  </div>
		</div>
	</div>

	<div class="form-group ">
		<div class="col-md-8">
			<div class="col-md-12 col-xs-12">
	        <div class="col-sm-3"><label>Dari</label></div>
	        <div class="col-sm-3">
				    <input type="number" name="inpt_dari" id="inpt_dari" class="form-control input-sm" onkeyup="validAngka(this)" />
	        </div>  
          <div class="col-sm-3"><label>Sampai</label></div>
	        <div class="col-sm-3">
				    <input type="number" name="inpt_sampai" id="inpt_sampai" class="form-control input-sm" onkeyup="validAngka(this)" />
	        </div>  
		  </div>
		</div>
		<div class="col-md-8">
		    <div class="col-md-12 col-xs-12">
          <div class="col-sm-3"><label>Kode Cacat</label></div>
          <div class="col-sm-4">
              <select type="text"  class="form-control cacat" id="inpt_kode" name="inpt_kode"   style="width:100%;">
                <?php 
                  echo "<option></option>";
                  foreach($list_cacat as $val){
                    echo '<option value='.$val['kode_cacat'].'>'.$val['kode_nama'].'</option>';
                  }
                ?>
              </select>
          </div>  
          <div class="col-sm-4">
					  <button type="button" id="btn-terapkan" class="btn btn-primary btn-sm">Terapkan</button>
          </div>
		    </div>
		</div>
	</div>

  <table class="table table-condesed table-hover table-responsive rlstable" id="tabel_cacat"> 
    <thead>
        <tr>
        <th class="style no">No.</th>
        <th class="style" style="width: 200px;">Point Cacat</th>
        <th class="style" style="width: 300px;">Kode Cacat</th>
        <th class="style" style="width: 40px;"></th>
        <th class="style no"></th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $item_empty= TRUE;
      foreach ($rekam_cacat as $row) {
        $item_empty = FALSE;
      ?>
      <tr class="num">
        <td></td>
        <td data-content="edit" data-id="point_cacat" data-isi="<?php echo $row->point_cacat;?>" ><?php echo $row->point_cacat;?></td>
        <td data-content="edit" data-id="cacat" data-isi="<?php echo $row->kode_nama;?>" data-cacat="<?php foreach($list_cacat as $val){ if($val['kode_cacat'] == $row->kode_cacat){ echo '<option value='.$val['kode_cacat'].' selected>'.$val['kode_nama'].'</option>';} else {echo '<option value='.$val['kode_cacat'].'>'.$val['kode_nama'].'</option>';}} ?>" ><?php echo $row->kode_nama;?></td>
        <td>
            <?php if($status_mo == 'done' || $status_mo == 'ready'){//jika status mo tidak sama dengan done maka tampilkan 
            ?>
            <a class="edit_cacat" href="javascript:void(0)" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
            <a class="delete_cacat"  onclick="delete_rekam_cacat('<?php echo $row->row_order;?>')" href="javascript:void(0)" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
            <a class="cancel_cacat" href="javascript:void(0)" title="Cancel" data-toggle="tooltip"><i class="fa fa-close"></i></a>
          <?php }?>
        </td>
        <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order;?>"></td>
      </tr>
      <?php 
      }
      if($item_empty == TRUE){
        echo '<tr><td colspan=4" align="center">Tidak ada Data</tr>';
      }
      ?>
    </tbody>      
    <tfoot>
      <tr>
        <td colspan="4">
          <?php if($status_mo == 'ready' || $status_mo == 'draft'){
            ?>
            <a href="javascript:void(0)" onclick="tambah_cacat()"><i class="fa fa-plus"></i> Tambah Data</a>
          <?php
            }
          ?>
        </td>
      </tr>
    </tfoot>          
  </table>
  <div class="example1_processing_cacat table_processing" style="display: none">
    Processing...
	</div>
</form>

<script type="text/javascript">

    // validasi only angka
    function validAngka(a){   
        if(!/^[0-9.]+$/.test(a.value)){
            //a.value = a.value.substring(0,a.value.length-1);
            a.value = a.value.replace(/[^0-9.-]/, '')
            return true;
       }
    }

    //disable btn-tambah jika status mo == done
    var status_mo = '<?php echo $status_mo;?>';
    if(status_mo == 'done'){
      $('#btn-tambah').attr("disabled", true);
    }
    $('#inpt_kode').select2({});
    // Edit row on edit button click
    $(document).on('click', '.edit_cacat', function(){
        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          $('.cacat').select2({});

          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
          }else if($(this).attr('data-id')=="cacat"){
             $(this).html('<select type="text"  class="form-control cacat" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"   style="width:100%;"> "'+$(this).attr('data-cacat')+'"  </select>');
          }else{
            $(this).html('<input type="text"  class="form-control point_cacat" value="'+$(this).attr('data-isi') +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeypress="enter(event);"> ');
          }
      
        });  

        $(this).parents("tr").find(".edit_cacat").hide();
        $(this).parents("tr").find(".delete_cacat").hide();
        $(this).parents("tr").find(".cancel_cacat").show();
        // $(this).parent("tr").find(".delete_cacat").toggle();      
    });

    //update data rekam cacat
    $(document).on("click", ".update", function(){  
        $(".cacat").each(function(index, element) {
          var a = $(element).parents("tr").find("#cacat").val();
          alert(a);
        });

    });

    //btn batal edit
    $(document).on('click', '.cancel_cacat', function(){
      $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')!="row_order"){
           $(this).html($(this).attr('data-isi'));
          }
      });
      $(this).parents("tr").find(".edit_cacat").show();
      $(this).parents("tr").find(".cancel_cacat").hide();
      $(this).parents("tr").find(".delete_cacat").show();

    });

    //btn delete cacat
    function delete_rekam_cacat(row){

        var row_order = row; 
        var kode      = '<?php echo $kode; ?>';
        var lot       = '<?php echo $lot; ?>';
        var quant_id  = $("#quant_id").val();
        var deptid    = $("#deptid").val();
      
        bootbox.dialog({
        message: "Apakah Anda ingin menghapus data ?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                  please_wait(function(){});
                  $.ajax({
                      dataType: "JSON",
                      url : '<?php echo site_url('manufacturing/mO/delete_rekam_cacat_lot_modal') ?>',
                      type: "POST",
                      data: {kode : kode, lot : lot, quant_id : quant_id, row_order : row_order, deptid : deptid  },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else if(data.status == "failed"){
                            alert_modal_warning(data.message);
                            unblockUI( function(){});
                        }else{
                            // $("#tab_1").load(location.href + " #tab_1");
                            // $("#foot").load(location.href + " #foot");
                            // $("#status_bar").load(location.href + " #status_bar");
                            // $('#tambah_data').modal('hide');
                            unblockUI( function(){
                              setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                            });
                            reloadBodyRekamCacat();
                         }
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert(xhr.responseText);
                        unblockUI( function(){});
                      }
                    });
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                  $('.bootbox').modal('hide');
                }
          }
        }
        });
     
    }

    function reloadBodyRekamCacat(){

      var kode      = "<?php echo $kode; ?>";
      var lot       = "<?php echo $lot; ?>";
      var quant_id  = "<?php echo $quant_id; ?>";
     
      $.ajax({
        type	: "POST",
        dataType: "json",
        url 	:'<?php echo base_url('manufacturing/mO/get_body_rekam_cacat')?>',
              beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
                }
                $("#tabel_cacat tbody").remove();
                $(".example1_processing_cacat").css('display','block');
              },
        data: {kode:kode, lot:lot, quant_id:quant_id},
        success: function(data){
          if(data.sesi == "habis"){
            //alert jika session habis
            alert_modal_warning(data.message);
            window.location = baseUrl;//replace ke halaman login
				  }else{
            $(".example1_processing_cacat").css('display','none');
            var no    = 1;
            var empty = true;
            var tbody = $("<tbody />");
            var tr    = '';
            var btn  = '';

            $.each(data.items, function(key, value) {

                if(status_mo == 'draft' || status_mo == 'ready'){
                  btn   = '<a class="edit_cacat" href="javascript:void(0)" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a><a class="delete_cacat"  onclick="delete_rekam_cacat('+value.row_order+')" href="javascript:void(0)" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a><a class="cancel_cacat" href="javascript:void(0)" title="Cancel" data-toggle="tooltip"><i class="fa fa-close"></i></a>';
                }else{
                  btn = '';
                }

                empty = false;
                tr    = '<tr class="num">'
                      + '<td>'+no+'</td>'
                      + '<td data-content="edit" data-id="point_cacat" data-isi="'+value.point_cacat+'" >'+value.point_cacat+'</td>'
                      + '<td data-content="edit" data-id="cacat" data-isi="'+value.kode_nama+'" >'+value.kode_nama+'</td>'
                      + '<td>'+btn+'</td>'
                      + '<td data-content="edit" data-id="row_order" data-isi="'+value.row_order+'"></td>'
                      + '</tr>';
                no++;
                tbody.append(tr);
            });
              
            if(empty == true){
					    var tr = $("<tr>").append($("<td colspan='4' align='center'>").text('Tidak ada Data'));
              tbody.append(tr);
					  }

            $("#tabel_cacat").append(tbody);

          }

        },error: function (xhr, ajaxOptions, thrownError) { 
          alert(xhr.responseText);
          alert('error Reload');
					$(".example1_processing_cacat").css('display','none');
        }
      });

    }

    //fungsi panggil tambah_cacat() ketika enter di qty
    function enter(e){
      if(e.keyCode === 13){
            e.preventDefault(); 
            tambah_cacat(); //panggil fungsi tambah cacat
        }
    }
    
    $("#btn-terapkan").off("click").on("click",function(e) {  

        var inpt_dari   = $('#inpt_dari').val() == "" ?  0 : parseInt($('#inpt_dari').val());
        var inpt_sampai = $('#inpt_sampai').val() == "" ?  0 : parseInt($('#inpt_sampai').val());
        var inpt_kode   = $('#inpt_kode').val();
        
        var point_cacat = document.getElementsByName('point_cacat');
        var inx_point   = point_cacat.length;
        
        if((inpt_dari > 0 || inpt_sampai > 0 ) && inpt_kode != '' ){
          for ( i = inpt_dari; i <= inpt_sampai; i++) {
            var row = ' ';
            row  = '<tr class="num">'
                + '<td ></td>'
                +  '<td><input type="text" name="point_cacat" id="point_cacat" class="form-control input-sm point_cacat" autocomplete="off" onkeypress="enter(event);" value="'+i+'"/></td>'
                +  '<td><select type="text" class="form-control input-sm cacat" name="cacat" id="cacat" style="width:100%;"><?php foreach($list_cacat as $row){ echo '<option value='.$row['kode_cacat'].'>'.$row['kode_nama'].'</option>';}?></select></td>'
                +  '<td><a class="hapus_baris"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red" data-toggle="tooltip" title="Hapus"></i> </a></td>'
                + '<td></td>'
                +  '</tr>'
            $('#tabel_cacat tbody').append(row);       
            $('[data-toggle="tooltip"]').tooltip();
            //select2 kode_cacat 
            $('#tabel_cacat tbody .cacat').eq(inx_point).val(inpt_kode);
            inx_point++;
            $('.cacat').select2({});
          }
        }else if(inpt_kode.length == 0){
          alert('Silahkan Pilih Kode Cacat !');
        }else{
          alert('Silahkan inputkan point Cacat Dari dan Sampai !');
        }

    });

    //tambah baris cacat
    function tambah_cacat(){

      var tambah = true;
      var point_cacat = document.getElementsByName('point_cacat');
      var inx_point = point_cacat.length-1;

      //alert(inx_point);
      //cek point cacat apa ada yang kosong
      $('.point_cacat').each(function(index,value){
          if($(value).val()==''){
              alert('Point Cacat tidak boleh kosong');
              $(value).addClass('error'); 
              tambah = false;
          }else{
            $(value).removeClass('error'); 
          }
      });

      //cek kode cacat apa ada yang kosong
      $('.cacat').each(function(index,value){
          if($(value).val()==''){
              alert('Kode Cacat tidak boleh kosong');
              $(value).addClass('error'); 
              tambah = false;
          }else{
            $(value).removeClass('error'); 
          }
      });

      var last_kode = "";
      var last_text = "";
      if(inx_point >= 0){
        last_kode = $( ".cacat option:selected" ).eq(inx_point).val();
        last_text = $( ".cacat option:selected" ).eq(inx_point).text();
        // alert(last_kode);
      }

      if(tambah == true){
          var row = ' ';
          row  = '<tr class="num">'
               + '<td ></td>'
               +  '<td><input type="text" name="point_cacat" id="point_cacat" class="form-control input-sm point_cacat" autocomplete="off" onkeypress="enter(event);"/></td>'
               +  '<td><select type="text" class="form-control input-sm cacat" name="cacat" id="cacat" style="width:100%;"><?php foreach($list_cacat as $row){ echo '<option value='.$row['kode_cacat'].'>'.$row['kode_nama'].'</option>';}?></select></td>'
               +  '<td><a class="hapus_baris"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red" data-toggle="tooltip" title="Hapus"></i> </a></td>'
               + '<td></td>'
               +  '</tr>'
          $('#tabel_cacat tbody').append(row);       
          $('[data-toggle="tooltip"]').tooltip();
          //select2 kode_cacat 
          point_cacat[inx_point+1].focus();
          if(inx_point >= 0){
            // var $newOptionuom = $("<option></option>").val(last_kode).text(last_text);
            // alert(last_text)
            // $( ".cacat" ).eq(inx_point + 1).append(newOptionuom).trigger('change');  
            $('#tabel_cacat tbody .cacat').eq(inx_point + 1).val(last_kode);
          }
          $('.cacat').select2({});

      }

    }

    //hapus baris tambah
    $(document).on('click', '.hapus_baris', function(){
      $(this).closest('tr').remove();
    });

    $("#tambah_data .modal-dialog .modal-content .modal-footer").html('<button type="button" id="btn-tambah-cacat" class="btn btn-primary btn-sm"> Simpan</button> <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');

    $("#btn-tambah-cacat").off("click").on("click",function(e) {
        e.preventDefault();

        var kode   = '<?php echo $kode; ?>';
        var lot    = '<?php echo $lot; ?>';
        var valid  = true;
        //cek point cacat apa ada yang kosong
        $('.point_cacat').each(function(index,value){
          if($(value).val()==''){
              alert('Point Cacat tidak boleh kosong');
              $(value).addClass('error'); 
              valid = false;
          }else{
            $(value).removeClass('error'); 
          }
        });

        //cek kode cacat apa ada yang kosong
        $('.cacat').each(function(index,value){
          //alert($(".cacat").val());
          if($(value).val()==null){
              alert('Kode Cacat tidak boleh kosong');
              $(value).addClass('error'); 
              valid = false;
          }else{
              $(value).removeClass('error'); 
          }
        });

        if(valid == true){
          var list_cacat = false;
          var arr4 = [];
          $(".cacat").each(function(index, element) {
            if ($(element).val()!=="") {
              arr4.push({
                point_cacat :$(element).parents("tr").find("#point_cacat").val(),
                kode_cacat  :$(element).parents("tr").find("#cacat").val(),                         
                row_order   :$(element).parents("tr").find("#row_order").val(),
              });
              //alert (JSON.stringify(arr4));
              list_cacat = true;
            }
          });

          if(list_cacat == false){
              alert_modal_warning('Maaf, Rekam Cacat Masih Kosong !');
          }else{
            please_wait(function(){});
            $('#btn-tambah-cacat').button('loading');
            $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('manufacturing/mO/save_rekam_cacat_lot_modal') ?>',
                type: "POST",
                data: {rekam_cacat : arr4, kode : kode, deptid : $('#deptid').val(), lot : lot, quant_id  :$("#quant_id").val(),},
                success: function(data){

                    if(data.sesi == "habis"){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                    }else if(data.status == 'failed'){
                      alert_modal_warning(data.message);
                      $('#btn-tambah-cacat').button('reset');
                      unblockUI( function(){});
                    }else{
                      //jika berhasil disimpan
                      var rekam_cacat = '';
                      var kode        = '';
                      var deptid      = '';
                      var lot         = '';
                      var quant_id    = '';
                      $("#status_bar").load(location.href + " #status_bar");
                      $("#tab_1").load(location.href + " #tab_1");
                      $("#tab_2").load(location.href + " #tab_2");             
                      $("#foot").load(location.href + " #foot");
                      $('#tambah_data').modal('hide');
                      $('#btn-tambah-cacat').button('reset');                   
                      //window.location.replace(data.kode);
                      unblockUI( function(){
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                      });
                    }
                    
                },error: function (jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  $('#btn-tambah-cacat').button('reset');
  								unblockUI( function(){});
                }
            });
          }
        }
        e.stopImmediatePropagation();
    });
 

</script>