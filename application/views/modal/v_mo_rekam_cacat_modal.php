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
  <table class="table table-condesed table-hover table-responsive rlstable" id="tabel_cacat"> 
    <label>Lot : <?php echo $lot;?></label>
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
    <!--   <tr class="num">
        <td></td>
        <td data-content="edit" data-id="point_cacat" data-isi="<?php echo $row->point_cacat;?>" ><?php echo $row->point_cacat;?></td>
        <td data-content="edit" data-id="cacat" data-isi="<?php echo $row->kode_nama;?>" data-cacat="<?php foreach($list_cacat as $val){ if($val['kode_cacat'] == $row->kode_cacat){ echo '<option value='.$val['kode_cacat'].' selected>'.$val['kode_nama'].'</option>';} else {echo '<option value='.$val['kode_cacat'].'>'.$val['kode_nama'].'</option>';}} ?>" ><?php echo $row->kode_nama;?></td>
        <td>
            <?php if($status_mo != 'done'){//jika status mo tidak sama dengan done maka tampilkan 
            ?>
            <a class="edit_cacat" href="javascript:void(0)" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
            <a class="delete_cacat"  onclick="delete_rekam_cacat('<?php echo $row->row_order;?>')" href="javascript:void(0)" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
            <a class="cancel_cacat" href="javascript:void(0)" title="Cancel" data-toggle="tooltip"><i class="fa fa-close"></i></a>
          <?php }?>
        </td>
        <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order;?>"></td>
      </tr> -->
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

    reloadBodyRekamCacat();

    //disable btn-tambah jika status mo == done
    var status_mo = '<?php echo $status_mo;?>';
    if(status_mo == 'done'){
      $('#btn-tambah').attr("disabled", true);
    }

    // Edit row on edit button click
    $(".edit_cacat").off("click").on("click", function(){  
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

        $(this).parents("tr").find(".edit_cacat").toggle();
        $(this).parents("tr").find(".cancel_cacat, .delete_cacat").toggle();      
    });

    //update data rekam cacat
    $(document).on("click", ".update", function(){  
        $(".cacat").each(function(index, element) {
          var a = $(element).parents("tr").find("#cacat").val();
          alert(a);
        });

    });

    //btn batal edit
    $(".cancel_cacat").off("click").on("click",function(e) {
      $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')!="row_order"){
           $(this).html($(this).attr('data-isi'));
          }
      });
      $(this).parents("tr").find(".edit_cacat").toggle();
      $(this).parents("tr").find(".delete_cacat, .cancel_cacat").toggle();

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
                        }else{
                            $("#tab_1").load(location.href + " #tab_1");
                            $("#foot").load(location.href + " #foot");
                            $("#status_bar").load(location.href + " #status_bar");
                            $('#tambah_data').modal('hide');
                            alert_notify(data.icon,data.message,data.type);
                         }
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert(xhr.responseText);
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

            $.each(data.items, function(key, value) {
                empty = false;
                tr    += '<tr>';
                tr    += '<td data-content="edit" data-id="point_cacat" data-isi="'+value.point_cacat+'" >'+value.point_cacat+'</td>';
                tr    += '<td data-content="edit" data-id="cacat" data-isi="'+value.kode_nama+'" >'+value.kode_nama+'</td>';
                tr    += '<td></td>';
                tr    += '<td data-content="edit" data-id="row_order" data-isi="'+value.row_order+'"></td>';
                tr    += '</tr>';
                tbody.append(tr);
            });

            if(empty == true){
					    var tr = $("<tr>").append($("<td colspan='3' align='center'>").text('Tidak ada Data'));
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

    //tambah baris cacat
    function tambah_cacat(){

      var tambah = true;
      var point_cacat = document.getElementsByName('point_cacat');
      var inx_point = point_cacat.length-1;

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

      if(tambah == true){
        var row = ' ';
          row  = '<tr>'
               + '<td ></td>'
               +  '<td><input type="text" name="point_cacat" id="point_cacat" class="form-control input-sm point_cacat" autocomplete="off" onkeypress="enter(event);"/></td>'
               +  '<td><select type="text" class="form-control input-sm cacat" name="cacat" id="cacat" style="width:100%;"><?php foreach($list_cacat as $row){ echo '<option value='.$row['kode_cacat'].'>'.$row['kode_nama'].'</option>';}?></select></td>'
               +  '<td><a class="hapus_baris"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red" data-toggle="tooltip" title="Hapus"></i> </a</td>'
               + '<td></td>'
               +  '</tr>'
          $('#tabel_cacat tbody').append(row);       
          $('[data-toggle="tooltip"]').tooltip();
          //select2 kode_cacat 
          $('.cacat').select2({});
          point_cacat[inx_point+1].focus();
      }

    }

    //hapus baris tambah
    $(document).on('click', '.hapus_baris', function(){
      $(this).closest('tr').remove();
    });

    $("#btn-tambah").off("click").on("click",function(e) {
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
                      $('#btn-tambah').button('reset');                   
                      //window.location.replace(data.kode);
                      alert_notify(data.icon,data.message,data.type);

                    }
                    
                },error: function (jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  $('#btn-tambah').button('reset');
                }
            });
          }
        }
        e.stopImmediatePropagation();
    });
 

</script>