
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    .divListviewHead table  {
      display: block;
      height: calc( 100vh - 250px );
      overflow-x: auto;
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
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b>Efisiensi</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" action="<?=base_url()?>report/Efisiensi/export_excel">
              <div class="col-md-8">
                <div class="form-group">
                  <div class="col-md-12"> 
                    <div class="col-md-2">
                      <label>Tanggal </label>
                    </div>
                    <div class="col-md-4">
                      <div class='input-group'>
                        <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" required="">
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
                        <input type="text" class="form-control input-sm" name="tglsampai" id='tglsampai' required="">
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>    
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="col-md-2"><label>Departemen</label></div>
                    <div class="col-md-4">
                        <select type="text" class="form-control input-sm" name="departemen" id="departemen" required=""  >
                        </select>
                      </div>                                    
                  </div>
                </div>
               
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" >Generate</button>
                <button type="submit" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" > <i class="fa fa-file-excel-o"></i> Excel</button>
              </div>

            </form>

            <!-- table -->
            <div class="box-body">
            <div class="col-sm-12 table-responsive">
              <div class="table_scroll">
                <div class="table_scroll_head">
                  <div class="divListviewHead">
                      <table id="example1" class="table" border="0">
                          <thead>
                            <tr>
                              <th  class="style no"  rowspan="2">No. </th>
                              <th  class='style' rowspan="2"style="min-width: 80px">Tanggal</th>
                              <th  class='style' rowspan="2">Mesin</th>
                              <th  class='style' rowspan="2">MO</th>
                              <th  class='style' rowspan="2">SC</th>
                              <th  class='style' rowspan="2" style="min-width: 150px">Nama Produk</th>
                              <th  class='style' rowspan="2" style="min-width: 80px; word-wrap: break-word;">Target Efisiensi (Qty/Hari)</th>
                              <th  class='style' colspan="4" style="text-align: center;">HPH</th>
                              <th  class='style' colspan="4" style="text-align: center;">Efisiensi Produksi (%)</th>
                            </tr>
                            <tr>
                              <th class='style'>Hari</th>
                              <th class='style'>Pagi</th>
                              <th class='style'>Siang</th>
                              <th class='style'>Malam</th>
                              <th class='style'>Hari</th>
                              <th class='style'>Pagi</th>
                              <th class='style'>Siang</th>
                              <th class='style'>Malam</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="15" align="center">Tidak ada Data</td>
                            </tr>
                          </tbody>
                      </table>
                      <div id="example1_processing" class="table_processing" style="display: none">
                        Processing...
                      </div>
                  </div>
                </div>

              </div>
            </div>
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

   //show / hide collapse child in tabel
    $(document).on("hide.bs.collapse show.bs.collapse", ".child", function (event) {
        //alert('tes');
        $(this).prev().find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus");
        event.stopPropagation();
    });

  // set date tgldari
  $('#tgldari').datetimepicker({
      defaultDate : new Date(),
      format : 'D-MMMM-YYYY',
      ignoreReadonly: true
  });

  // set date tglsampai
  $('#tglsampai').datetimepicker({
      defaultDate : new Date(),
      format : 'D-MMMM-YYYY',
      ignoreReadonly: true
  });

  //select 2 Departementy
  $('#departemen').select2({
      allowClear: true,
      placeholder: "Select Departemen",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>report/efisiensi/get_departement_select2",
            //delay : 250,
            data : function(params){
              return{
                nama:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.kode,
                    text:item.nama
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

  // btn generate
  $("#btn-generate").on('click', function(){

      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      id_dept   = $('#departemen').val();

      if(tgldari == '' || tglsampai == ''){

        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if(id_dept == null){
        alert_modal_warning('Departemen Harus diisi !');
      }else{  
          $("#example1_processing").css('display',''); // show loading

          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/efisiensi/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept},
                success: function(data){

                  let no    = 1;
                  let empty = true;
                  let icon = '';
                  let style = '';

                  $.each(data.record, function(key, value){

                      empty = false;
                      let group = 'group-of-rows-'+no;
                      if(value.mesin.length > 0){
                        icon  = "<i class='glyphicon glyphicon-plus' ></i>";
                      }else{
                        icon = '';
                      }

                      // css cursor pointer
                      if(value.mesin.length > 0 ){
                        style = 'cursor:pointer';
                      }else{
                        style = '';
                      }

                      // parents
                      let tbody = $("<tbody />");
                      var tr = $("<tr class='collapsed'  data-toggle='collapse' href='#"+group+"' aria-controls='"+group+"' style='"+style+"' >").append(
                               $("<td class=''>").html(icon),
                               $("<td >").text(value.tgl),
                               $("<td colspan='9'>").text(''),
                               $("<td align='right'>").text(value.av_hari),
                               $("<td align='right'>").text(value.av_pagi),
                               $("<td align='right'>").text(value.av_siang),
                               $("<td align='right'>").text(value.av_malam),
                      );
                      no++

                      tbody.append(tr);
                      $("#example1").append(tbody); // append parents
                      jml_ef_hari = 0;

                      // child
                      if(value.mesin.length > 0){

                        let tbody2 = $('<tbody  id="'+group+'" class="collapse child">');
                        n = 1;
                        $.each(value.mesin, function(k, v){

                          var tr2 = $('<tr style="background-color: #f2f2f2;">').append(
                                   $("<td align='center'>").html(n),
                                   $("<td>").html(v.tgl),
                                   $("<td>").html(v.nama_mesin),
                                   $("<td colspan='3'>").text(''),
                                   $("<td  align='right'>").html(v.efisiensi),
                                   $("<td  align='right'>").html(v.hph_per_hari),
                                   $("<td  align='right'>").html(v.hph_pagi),
                                   $("<td  align='right'>").html(v.hph_siang),
                                   $("<td  align='right'>").html(v.hph_malam),
                                   $("<td  align='right'>").html(v.ef_per_hari),
                                   $("<td  align='right'>").html(v.ef_pagi),
                                   $("<td  align='right'>").html(v.ef_siang),
                                   $("<td  align='right'>").html(v.ef_malam),
                                  );

                          if(v.mrp.length > 0){
                            $.each(v.mrp, function(k, v2){

                               var tr3 = $('<tr style="background-color: #f2f2f2;">').append(
                                   $("<td align='center'>").html(n++),
                                   $("<td>").html(v2.tgl),
                                   $("<td>").html(v2.nama_mesin),
                                   $("<td>").html(v2.kode),
                                   $("<td>").html(v2.sc),
                                   $("<td>").html(v2.nama_produk),
                                   $("<td  align='right'>").html(v2.efisiensi),
                                   $("<td  align='right'>").html(v2.hph_per_hari),
                                   $("<td  align='right'>").html(v2.hph_pagi),
                                   $("<td  align='right'>").html(v2.hph_siang),
                                   $("<td  align='right'>").html(v2.hph_malam),
                                   $("<td  align='right'>").html(v2.ef_per_hari),
                                   $("<td  align='right'>").html(v2.ef_pagi),
                                   $("<td  align='right'>").html(v2.ef_siang),
                                   $("<td  align='right'>").html(v2.ef_malam),
                                  );
                                tbody2.append(tr3);


                            });
                          }else{
                            tbody2.append(tr2);
                            n++;
                          }

                        });
                        $("#example1").append(tbody2); // append child
                      }

                      
                  });

                if(empty == true){
                  var tr = $("<tr>").append($("<td colspan='12' align='center'>").text('Tidak ada Data'));
                  tbody.append(tr);
                }
                //$("#example1").append(tbody);

                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $('#btn-generate').button('reset');
                }
          });
      }
  });


</script>

</body>
</html>
