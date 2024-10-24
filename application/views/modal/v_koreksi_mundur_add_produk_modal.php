<form name="input" class="form-horizontal" role="form" method="POST">
    <div class="col-md-6">
        <div class="form-group"> 
           <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Departemen</label></div>
                <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="departemen" id="departemen"  style="width:100% !important;" >
                    </select>
                </div>                                    
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Koreksi Apa</label></div>
                <div class="col-xs-8">
                    <select type="text" class="form-control input-sm select2" name="koreksi_apa" id="koreksi_apa" style="width:100% !important;" >
                            <option value=''></option>
                            <option value='mo'>MO / MG</option>
                            <option value='out'>Pengiriman</option>
                            <option value='in'>Penerimaan</option>
                    </select>
                </div>                                    
            </div>
            <span id="show_tipe" style="display: none;">
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Tipe</label></div>
                <div class="col-xs-8">
                    <select type="text" class="form-control input-sm select2" name="tipe" id="tipe" style="width:100% !important;" >
                            <option value=''></option>
                            <option value='con'>Bahan Baku</option>
                            <option value='prod'>Barang Jadi</option>
                    </select>
              </div>                                    
            </div>

            </span>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kode</label></div>
                <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="kode" id="kode"  style="width:100% !important;" >
                    </select>
                </div>                                    
            </div>
        </div>
        
    </div>
    <div class="col-md-6">
      <div class="form-group"> 
         <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Koreksi Lebih / Kurang </label></div>
                <div class="col-xs-8">
                   <select type="text" class="form-control input-sm select2" name="koreksi_lebih_kurang" id="koreksi_lebih_kurang" style="width:100% !important;" >
                            <option value=''></option>
                            <option value='kurang'>Kurang</option>
                            <option value='lebih'>Lebih</option>
                    </select>
                </div>       
          </div>
          <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Koreksi Qty1 </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm" name="koreksi_qty1" id="koreksi_qty1"  data-decimal="2" onkeyup="validAngka(this)" oninput="enforceNumberValidation(this)">
                </div>       
          </div>
          <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Koreksi Qty2 </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm" name="koreksi_qty2" id="koreksi_qty2" ata-decimal="2" onkeyup="validAngka(this)"  oninput="enforceNumberValidation(this)">
                </div>       
          </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label> </label></div>
                <div class="col-xs-8 col-md-8">
                    <button type="button" class="btn btn-sm btn-default" id="btn-search">Cari</button>                     
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">    
        <div class="col-xs-12 table-responsive">
            <table id="example2" class="table table-striped table-hover rlstable">
            <thead>
                <tr>
                <th class="no">No</th>
                <th>Kode Produk</th>
                <th>Product</th>
                <th>Lot</th>
                <th>Qty</th>
                <th>Qty2</th>
                <th>Grade</th>
                <th>Reff Notes</th>
                <th>Status Move</th>
                <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            </table>
        </div>
    </div>
</form>

<script type="text/javascript">

  $(function () {
    // untuk focus after select2 close
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

    $('.select2').select2({
      placeholder: "Pilih",
      clear : true,
    });

    $("#koreksi_apa").on('change', function (e) {
        $("#kode").empty().trigger('change')
    });

    $("#koreksi_apa").on('change', function (e) {
        if($(this).val() == 'mo'){
          $('#show_tipe').show();
        }else{
          $('#show_tipe').hide();
        }
    });

    $("#departemen").on('select2:unselect', function (e) {
        $("#kode").empty().trigger('change')
    });

    $("#departemen").on('change', function (e) {
        $("#kode").empty().trigger('change')
    });

    //select 2 Departemen
    $('#departemen').select2({
      allowClear: true,
      placeholder: "Pilih",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>ppic/koreksimundur/get_departement_select2",
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

    //select 2 kode
    $('#kode').select2({
      allowClear: true,
      placeholder: "Pilih",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>ppic/koreksimundur/get_kode_transaksi",
            //delay : 250,
            data : function(params){
              return{
                koreksi_apa:$('#koreksi_apa').val(),
                departemen : $('#departemen').val(),
                nama       :params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.kode,
                    text:item.kode
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

    var f_filter = [];
    $('#example2').DataTable({});

    $('#btn-search').click(function(){
        var departemen  = $('#departemen').val();
        var koreksi_apa = $('#koreksi_apa').val();
        var kode        = $('#kode').val();
        if(koreksi_apa == 'mo'){
          var tipe        = $('#tipe').val();
        }else{
          var tipe        = "";
        }

        if(departemen == null){
            alert_notify('fa fa-warning','Departemen Harus Diisi !','danger',function(){});
        }else if(koreksi_apa.length === 0 || koreksi_apa === null){
            alert_notify('fa fa-warning','Koreksi Apa Harus Diisi !','danger',function(){});
        }else if(kode  == null){
          alert_notify('fa fa-warning','Kode Harus Diisi !','danger',function(){});
        }else{
          // $('#btn-search').button('loading');
          $('#example2').DataTable().destroy();
          f_filter = [];
          f_filter.push({departemen:departemen, koreksi_apa:koreksi_apa, tipe:tipe, kode:kode});
          // dTable.search("").draw();
          fetch_data();
        }

    });



    function fetch_data(){
      const  dTable = $('#example2').DataTable({ 
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
                "url": "<?php echo site_url('ppic/koreksimundur/search_produk_koreksi')?>",
                "type": "POST",
                "data":{f_filter : f_filter},
            },
            "columnDefs": [
              {
                "targets" : 9,
                
                'checkboxes': {
                    'selectRow': true
                 },
                'createdCell':  function (td, cellData, rowData, row, col){
                   var rowId = rowData[8];
                   if(rowId == 'ready' || rowId == 'cancel'){  
                      this.api().cell(td).checkboxes.disable();
                   }
                }, 
              },
              { 
                  "targets": [0], 
                  "orderable": false, 
              },
              // { 
              //     "targets": [10], 
              //     "visible": false, 
              // },
              {
                  "targets":[1],
                  render: function (data, type, full, meta) {
                        return "<div class='width-80'>" + data + "</div>";
                  }
              },
              {
                  "targets":[3],
                  render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-150'>" + data + "</div>";
                  }
              },
              {
                  "targets" : [2],
                  render: function (data, type, full, meta) {
                          return "<div class='text-wrap width-300'>" + data + "</div>";
                  }
              },
              {
                  "targets" : [4,5],
                  render: function (data, type, full, meta) {
                          return "<div class='text-wrap width-50 text-right'>" + data + "</div>";
                  }
              },
            ],
             "select": {
              'style': 'multi'
            },
            'rowCallback': function(row, data, dataIndex){
               // Get row ID
               var rowId = data[8];
                // If row ID is in the list of selected row IDs
                if(rowId == 'ready' || rowId == 'cancel'){      
                    $(row).find('input[type="checkbox"]').prop('disabled', true);
               }
            }
        });
    }

    $("#btn-tambah").off("click").on("click",function(e) {

      // var rows_selected =  $('#example2').DataTable().rows();
      var rows_selected =  $('#example2').DataTable().column(9).checkboxes.selected();
      var rows_selected_arr = new Array();
      var message       = 'Silahkan pilih data terlebih dahulu !';
      var kode_koreksi  = "<?php echo $kode_koreksi ?>";
      var qty1_koreksi  = $("#koreksi_qty1").val();
      var qty2_koreksi  = $("#koreksi_qty2").val();
      var koreksi_lebih_kurang  = $("#koreksi_lebih_kurang").val();

      // Iterate over all selected checkboxes'
      $.each(rows_selected, function(index, rowId){        
        rows_selected_arr.push(rowId);
      });
      
      if(f_filter.length == 0){
        alert_notify("fa fa-warning","Silahkan Filter terlebih dahulu !","danger",function(){});
      }else if(qty1_koreksi.length == 0 && qty2_koreksi.length == 0){
        alert_notify("fa fa-warning","Koreksi Qty1 atau Koreksi Qty2 Harus Diisi !","danger",function(){});
      }else if(koreksi_lebih_kurang.length == 0){
        alert_notify("fa fa-warning","Koreksi Lebih / Kurang Harus diisi !","danger",function(){});
      }else  if(rows_selected_arr == ''){
        alert_notify("fa fa-warning",message,"danger",function(){});
      }else{
        $('#btn-tambah').button('loading');
        $.ajax({
            type: "POST",
            url :'<?php echo base_url('ppic/koreksimundur/simpan_produk_koreksi_modal')?>',
            dataType: 'JSON',
            data: {filter : f_filter,
                  kode_koreksi : kode_koreksi,
                  qty1_koreksi : qty1_koreksi,
                  qty2_koreksi : qty2_koreksi,
                  arr_data : rows_selected_arr,
                  koreksi_lebih_kurang :koreksi_lebih_kurang  
                  },
            success: function(data){
              if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
                $('#btn-tambah').button('reset');
              }else if(data.status == 'failed'){
                //var pesan = "Lot "+data.lot+ " Sudah diinput !"       
                alert_modal_warning(data.message);
                $('#btn-tambah').button('reset');
              }else{
                // $('#tambah_data').modal('hide');
                $('#btn-tambah').button('reset');

                if(data.msg2 == 'Yes'){
                  alert_modal_warning(data.message2);
                }
                alert_notify(data.icon,data.message,data.type,function(){});
              }

            },error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.responseText);
              $('#btn-tambah').button('reset');
          }
        });
      } 
      
      return false;
    });

  });

   // validasi only angka
  function validAngka(a){   
      if(!/^[0-9.]+$/.test(a.value)){
          a.value = a.value.replace(/[^0-9.-]/, '')
          return true;
      }
  }

  // validasi decimal
  function enforceNumberValidation(ele) {
            if ($(ele).data('decimal') != null) {
                // found valid rule for decimal
                var decimal = parseInt($(ele).data('decimal')) || 0;
                var val = $(ele).val();
                if (decimal > 0) {
                    var splitVal = val.split('.');
                    if (splitVal.length == 2 && splitVal[1].length > decimal) {
                        // user entered invalid input
                        $(ele).val(splitVal[0] + '.' + splitVal[1].substr(0, decimal));
                    }
                } else if (decimal == 0) {
                    // do not allow decimal place
                    var splitVal = val.split('.');
                    if (splitVal.length > 1) {
                        // user entered invalid input
                        $(ele).val(splitVal[0]); // always trim everything after '.'
                    }
                }
            }
    }
   

</script>