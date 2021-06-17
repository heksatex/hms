
  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
      alert_notify('fa fa-warning','Maaf, Inputan Hanya Berupa Angka !','danger');
    }
  }

  // Append table with add row form on add new button click
  $(document).on("click", ".add-new", function(){

    var status = $("#status").val();

    if(status == 'draft' || status == 'waiting_date' || status =='waiting_color'){

    $(".add-new").hide();
    var index = $("#contract_lines tbody tr:last-child").index();
    var row   ='<tr class="num">'
          + '<td></td>'
          + '<td><select type="text" class="form-control input-sm prod" name="Product" id="product"></select></td>'
          + '<td><input type="text" class="form-control input-sm description" name="Description" id="description"></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
          + '<td><input type="text" class="form-control input-sm" name="Qty" id="qty" onkeyup="validAngka(this)"></td>'
          + '<td><input type="text" class="form-control input-sm uom" name="Uom" id="uom"></td>'
          + '<td><input type="text" class="form-control input-sm" name="roll" id="roll"></td>'
          + '<td><input type="text" class="form-control input-sm" name="Unit Price" id="price" onkeyup="validAngka(this)"></td>'
          + '<td><select type="text" class="form-control input-sm tax" name="taxes" id="taxes"><option value="">-Taxes-</option><?php foreach($tax as $row){?><option value="<?php echo $row->id; ?>"><?php echo $row->nama;?></option>"<?php }?></select></td>'
          + '<td></td>'
          + '<td></td>'
          + '<td><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '</tr>';


        $('#contract_lines tbody').append(row);
        $("#contract_lines tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        //select 2 product
        $('.prod').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/salescontract/get_produk_select2",
                //delay : 250,
                data : function(params){
                  return{
                    prod:params.term
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_produk,
                          text:item.nama_produk
                      });
                  });
                  return {
                    results:results
                  };
                },
                error: function (xhr, ajaxOptions, thrownError){
                  alert('Error data');
                  alert(xhr.responseText);
                }
          }
        });

      $(".prod").change(function(){
          $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('sales/salescontract/get_prod_by_id') ?>',
                type: "POST",
                data: {kode_produk: $(this).parents("tr").find("#product").val() },
                success: function(data){
                  //alert(data.nama_produk);
                  $('.prodhidd').val(data.nama_produk);
                  $('.description').val(data.nama_produk);
                  $('.uom').val(data.uom);
                },
                error: function (xhr, ajaxOptions, thrownError){
                  alert('Error data');
                  alert(xhr.responseText);
                }
          });
      });
    }else{
      alert_modal_warning('Maaf, Data items tidak bisa Ditambah !');
    }
  });


    // simpan / edit row data ke database
    $(".add").unbind( "click" );
    $(document).on("click", ".add", function(){
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty2 = true;
        }
      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='roll'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty = true;
        }
      });

      if(!empty && !empty2){
        var kode  =  "<?php echo $salescontract->sales_order; ?>";
        var kode_prod  = $(this).parents("tr").find("#product").val();
        var prod  = $(this).parents("tr").find("#prodhidd").val();
        var desc  = $(this).parents("tr").find("#description").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var roll  = $(this).parents("tr").find("#roll").val();
        var price = $(this).parents("tr").find("#price").val();
        var taxes = $(this).parents("tr").find("#taxes").val();
        var row_order = $(this).parents("tr").find("#row_order").val();
        var dat = $(this).parents("tr").find('input[type="text"]').val();

        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('sales/salescontract/simpan_detail') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_prod  : kode_prod,
                prod  : prod,
                desc  : desc, 
                qty   : qty,
                uom   : uom,
                roll  : roll,
                price : price,
                taxes : taxes,
                row_order : row_order  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else{
                $("#tab_1").load(location.href + " #tab_1");
                $("#foot").load(location.href + " #foot");
                //$("#total").load(location.href + " #total");
                $(".add-new").show();                   
                alert_notify(data.icon,data.message,data.type);
             }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
        });
        
      }   
    });


    // Edit row on edit button click
    $(document).on("click", ".edit", function(){  
      var status = $("#status").val();

       if(status == 'draft' || status == 'waiting_date' || status == 'date_assigned'){

        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
          }else if($(this).attr('data-id')=="taxes"){
            $(this).html($(this).attr('data-isi'));
          }else if($(this).attr('data-id')=='qty' || $(this).attr('data-id')=='price'){
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }else{
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"> ');
          }

        });  

        $(this).parents("tr").find(".add, .edit").toggle();
        $(this).parents("tr").find(".cancel, .delete").toggle();
        $(".add-new").hide();
      }else{
         alert_modal_warning('Maaf, Data tidak bisa diubah !')
      }
    });
    
    // batal add row on batal button click
    $(document).on("click", ".batal", function(){
      var input = $(this).parents("tr").find('.prod');
      input.each(function(){
       $(this).parent("td").html($(this).val());
      }); 
      
      $(this).parents("tr").remove();
      $(".add-new").show();
    });

    
    //delete row di database
    $(document).on("click", ".delete", function(){ 

     var status = $("#status").val();
     if(status == 'draft' || status == 'waiting_date'){

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      var kode  =  "<?php echo $salescontract->sales_order; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  
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
                      url : '<?php echo site_url('sales/salescontract/hapus_detail') ?>',
                      type: "POST",
                      data: {kode : kode, 
                            row_order : row_order  },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else{
                            $("#tab_1").load(location.href + " #tab_1");
                            $("#foot").load(location.href + " #foot");
                            //$("#total").load(location.href + " #total");
                            $(".add-new").show();                   
                            alert_notify(data.icon,data.message,data.type);
                         }
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert('Error data');
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
    }else{
      alert_modal_warning('Maaf, Data tidak bisa di Hapus !')
    }
    });

    //btn cancel edit
    $(document).on("click", ".cancel", function(){
        $("#tab_1").load(location.href + " #tab_1");
        //$("#total").load(location.href + " #total");
        $(".add-new").show();
        /*
      var input = $(this).parents("tr").find('input[type="text"]');
      input.each(function(){
        $(this).parent("td").html($(this).attr('value'));
      }); 
      $(this).parents("tr").find(".edit, .add").toggle();
      $(this).parents("tr").find(".delete, .cancel").toggle();
        */
    });



  /* START COLOR LINES */
  
  // Append table with add row form on add new button click
  $(document).on("click", ".add-new-color-lines", function(){
    
  	//no SO
    var kode  =  "<?php echo $salescontract->sales_order; ?>";
    var status = $("#status").val();
    //alert(status);

    if(status == 'waiting_color'){

    $(".add-new-color-lines").hide();
    var index = $("#color_lines tbody tr:last-child").index();
    var row   ='<tr class="num">'
          + '<td></td>'
          + '<td><select type="text" class="form-control input-sm prod_color" name="Product" id="product"></select></td>'
          + '<td><input type="text" class="form-control input-sm description_color" name="Description" id="description_color"></select><input type="hidden" class="form-control input-sm prodhidd_color" name="prodhidd" id="prodhidd_color"></td>'
          + '<td><select type="text" class="form-control input-sm color" name="Color" id="color"></select></td>'
          + '<td><input type="text" class="form-control input-sm" name="Color Name" id="color_name"></td>'
          + '<td><input type="text" class="form-control input-sm" name="Qty" id="qty" onkeyup="validAngka(this)"></td>'
          + '<td><input type="text" class="form-control input-sm" name="Piece Info" id="piece_info"></td>'
          + '<td><input type="text" class="form-control input-sm uom_color" name="Uom" id="uom"></td>'
          + '<td><button type="button" class="btn btn-primary btn-xs add-color-lines width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit-color-lines" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal-color-lines width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '</tr>';


        $('#color_lines tbody').append(row);
        $("#color_lines tbody tr").eq(index + 1).find(".add-color-lines, .edit-color-lines").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        //select 2 product
        $('.prod_color').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/salescontract/get_produk_color_select2",
                //delay : 250,
                data : function(params){
                  return{
                  	kode : kode,
                    prod:params.term
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_produk,
                          text:item.nama_produk
                      });
                  });
                  return {
                    results:results
                  };
                },
                error: function (xhr, ajaxOptions, thrownError){
                  alert('Error data');
                  alert(xhr.responseText);
                }
          }
        });

        $(".prod_color").change(function(){
          $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('sales/salescontract/get_prod_by_id') ?>',
                type: "POST",
                data: {kode_produk: $(this).parents("tr").find("#product").val() },
                success: function(data){
                  //alert(data.nama_produk);
                  $('.prodhidd_color').val(data.nama_produk);
                  $('.description_color').val(data.nama_produk);
                  $('.uom_color').val(data.uom);
                },
                error: function (xhr, ajaxOptions, thrownError){
                  alert('Error data');
                  alert(xhr.responseText);
                }
          });
        });

          //select 2 color
        $('.color').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/salescontract/get_color_select2",
                //delay : 250,
                data : function(params){
                  return{
                    prod:params.term
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_warna,
                          text:item.kode_warna
                      });
                  });
                  return {
                    results:results
                  };
                },
                error: function (xhr, ajaxOptions, thrownError){
                  alert('Error data');
                  alert(xhr.responseText);
                }
          }
        });	

    }else{
      alert_modal_warning('Maaf, Data items tidak bisa Ditambah !');
    }
  });    


 	// simpan / edit row data ke database COLOR LINES
    $(".add-color-lines").unbind( "click" );
    $(document).on("click", ".add-color-lines", function(){
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='Color'){
           alert_notify('fa fa-warning', $(this).attr('name')+ ' Harus Diisi !', 'danger');
        }
      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='Piece Info'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty = true;
        }
      });

      if(!empty && !empty2){
        var kode  =  "<?php echo $salescontract->sales_order; ?>";
        var kode_prod  = $(this).parents("tr").find("#product").val();
        var prod  = $(this).parents("tr").find("#prodhidd_color").val();
        var desc  = $(this).parents("tr").find("#description_color").val();
        var color  = $(this).parents("tr").find("#color").val();
        var color_name = $(this).parents("tr").find("#color_name").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var piece_info  = $(this).parents("tr").find("#piece_info").val();
        var row_order = $(this).parents("tr").find("#row_order").val();
        //var dat = $(this).parents("tr").find('input[type="text"]').val();
              
        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('sales/salescontract/simpan_detail_color_lines') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_prod  : kode_prod,
                prod  : prod,
                color : color,
                color_name : color_name,
                desc  : desc, 
                qty   : qty,
                uom   : uom,
                piece_info: piece_info,
                row_order : row_order  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else if(data.status == 'failed'){
            	alert_modal_warning(data.message);
            }else{
                $("#tab_2").load(location.href + " #tab_2");
                $("#foot").load(location.href + " #foot");
                //$("#total").load(location.href + " #total");
                $(".add-new-color-lines").show();                   
                alert_notify(data.icon,data.message,data.type);
             }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
        });

      }   
    });

    // batal add row on batal button click COLOR LINES
    $(document).on("click", ".batal-color-lines", function(){
      var input = $(this).parents("tr").find('.prod_color');
      input.each(function(){
       $(this).parent("td").html($(this).val());
      }); 
      
      $(this).parents("tr").remove();
      $(".add-new-color-lines").show();
    });


    $(document).on("click", ".delete-color-lines", function(){ 

     var status = $("#status").val();
     if(status == 'waiting_color'){

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="text" class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
     

      var kode  =  "<?php echo $salescontract->sales_order; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  

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
                        url : '<?php echo site_url('sales/salescontract/hapus_detail_color_lines') ?>',
                        type: "POST",
                        data: {kode : kode, 
                              row_order : row_order  },
                        success: function(data){
                          if(data.sesi=='habis'){
                              //alert jika session habis
                              alert_modal_warning(data.message);
                              window.location.replace('../index');
                          }else if(data.status == 'failed'){
  							              $("#tab_2").load(location.href + " #tab_2");
                              $("#foot").load(location.href + " #foot");
                              $(".add-new-color-lines ").show();
                              alert_modal_warning(data.message);
                          }else{
                              $("#tab_2").load(location.href + " #tab_2");
                              $("#foot").load(location.href + " #foot");
                              $(".add-new-color-lines ").show();   
                              alert_notify(data.icon,data.message,data.type);
                           }
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                          alert('Error data');
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
    }else{
      alert_modal_warning('Maaf, Data tidak bisa di Hapus !')
    }
    });


    // Edit row on edit button click COLOR LINES
    $(document).on("click", ".edit-color-lines", function(){  
      var status = $("#status").val();

       if(status == 'waiting_color'){

        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
          }else if($(this).attr('data-id')=='qty'){
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }else{
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"> ');
          }

        });  

        $(this).parents("tr").find(".add-color-lines, .edit-color-lines").toggle();
        $(this).parents("tr").find(".cancel-color-lines, .delete-color-lines").toggle();
        $(".add-new-color-lines").hide();
      }else{
         alert_modal_warning('Maaf, Data tidak bisa diubah !')
      }
    });

    //btn cancel edit COLOR LINES
    $(document).on("click", ".cancel-color-lines", function(){
        $("#tab_2").load(location.href + " #tab_2");
        $(".add-new-color-lines").show();
    });


  /* END COLOR LINES */

    //klik button simpan
    $('#btn-simpan').click(function(){
      var status = $("#status").val();

      if(status == 'draft' || status == 'waiting_date' || status == 'date_assigned'){

        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
           type: "POST",
           dataType: "json",
           url :'<?php echo base_url('sales/salescontract/simpan')?>',
           beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
           },
           data: {sales_order: $('#sales_order').val(),
                  customer   : $('#customer').val(),
                  invoice_address  : $('#invoice_address').val(),
                  delivery_address : $('#delivery_address').val(),
                  buyer_code : $('#buyer_code').val(),
                  type       : $('#type').val(),
                  order_production : $('#order_production').val(),
                  tgl        : $('#tgl').val(),
                  reference  : $('#reference').val(),
                  warehouse  : $('#warehouse').val(),
                  currency   : $('#currency').val(),
                  delivery_date   : $('#delivery_date').val(),
                  time_ship  : $('#time_ship').val(),
                  incoterm   : $('#incoterm').val(),
                  paymentterm   : $('#paymentterm').val(),
                  destination   : $('#destination').val(),
                  bank     : $('#bank').val(),
                  clause   : $('#clause').val(),
                  note     : $('#note').val(),

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
                document.getElementById(data.field).focus();
              }else{
               //jika berhasil disimpan/diubah
                unblockUI( function() {
                  setTimeout(function() { 
                    alert_notify(data.icon,data.message,data.type, function(){
                  },1000); 
                  });
                });
                $("#foot").load(location.href + " #foot");
                $("#total").load(location.href + " #total");

              }
              $('#btn-simpan').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.responseText);
              unblockUI( function(){});
              $('#btn-simpan').button('reset');
            }
        });
          window.setTimeout(function() {
         $(".alert").fadeTo(500, 0).slideUp(500, function(){
          $(this).remove(); });
        }, 3000);

      }else{
        alert_modal_warning('Maaf, Data tidak bisa diubah !')
      }
    });


    //modal mode print
  $(document).on('click','#btn-print',function(e){
      e.preventDefault();
      var kode = $('#kode').val();
      $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#print_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Pilih Bahasa ?');
       var  so = '<?php echo $salescontract->sales_order?>';
      $.post('<?php echo site_url()?>sales/salescontract/mode_print_modal',
        { so : so},
          function(html){
            setTimeout(function() {$(".print_data").html(html);  },1000);
        }   
      );
  });

   //klik button confirm contract
    $('#btn-confirm').click(function(){
      $('#btn-confirm').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('sales/salescontract/confirm_contract')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
              
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika details masih kosong
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
             $('#btn-confirm').button('reset');
             
            }else{
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                },1000); 
                });
              });
              $("#ref_status").load(location.href + " #ref_status");
              $("#btn-header").load(location.href + " #btn-header");
              $("#status_bar").load(location.href + " #status_bar");
              $("#foot").load(location.href + " #foot");
              $("#total").load(location.href + " #total");

            }
            $('#btn-confirm').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-confirm').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });

    //klik button approve contract
    $('#btn-approve').click(function(){
      $('#btn-approve').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('sales/salescontract/approve_contract')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
              
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika details masih kosong
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
             $('#btn-approve').button('reset');
             
            }else{
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                },1000); 
                });
              });
              $("#ref_status").load(location.href + " #ref_status");
              $("#btn-header").load(location.href + " #btn-header");
              $("#status_bar").load(location.href + " #status_bar");
              $("#foot").load(location.href + " #foot");
              $("#total").load(location.href + " #total");

            }
            $('#btn-approve').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-approve').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });


    //klik button approve color
    $('#btn-approve-color').click(function(){
      $('#btn-approve-color').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('sales/salescontract/approve_color')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
              
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika details masih kosong
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
             $('#btn-approve-color').button('reset');
             
            }else{
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                },1000); 
                });
              });
              $("#ref_status").load(location.href + " #ref_status");
              $("#btn-header").load(location.href + " #btn-header");
              $("#status_bar").load(location.href + " #status_bar");
              $("#foot").load(location.href + " #foot");
              $("#total").load(location.href + " #total");

            }
            $('#btn-approve-color').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-approve-color').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });


  // modal Create Color
  $("#btn-create-color").unbind( "click" );
  $(document).on('click','#btn-create-color',function(e){
      e.preventDefault();
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Create Color');
        $.post('<?php echo site_url()?>sales/salescontract/create_color_modal',
          {txtProduct      : $('#product').val() },
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },1000);
          }   
       );
  });


    //btn simpan create color
    $('#btn-tambah').click(function(){
      $('#btn-tambah').button('loading');
      please_wait(function(){});
      
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('lab/dti/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {tanggal    : $('#tgl').val(),
                warna      : $('#warna').val(),
                note       : $('#notes').val(),
                status     : 'tambah'

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
              document.getElementById(data.field).focus();
            }else{
             //jika berhasil disimpan
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                  $("#tab_2").load(location.href + " #tab_2");
                },1000); 
                });
              });
              $('#tambah_data').modal('hide');
            }
            $('#btn-tambah').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-tambah').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
      
    });

  $("#tambah_data").on("hidden.bs.modal", function () {
    ///alert('tes');
    $('#form_create_color')[0].reset();
  });
    