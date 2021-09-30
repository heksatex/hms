
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
          <h3 class="box-title"><b>Form Edit </b></h3>
          
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
                  <div class="col-xs-4"><label>Name</label></div>
                  <div class="col-xs-8">
                    <input type="hidden" name="id" id="id" value="<?php echo $partner->id?>">
                    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $partner->nama;?>" >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Invoice Street</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm"name="invoice_street" id="invoice_street" ><?php echo $partner->invoice_street?></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Invoice Country</label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="invoice_country" id="invoice_country"></select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Invoice State</label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="invoice_state" id="invoice_state" ></select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Invoice City</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="invoice_city" id="invoice_city" value="<?php echo $partner->invoice_city?>" >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Invoice Zip </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="invoice_zip" id="invoice_zip"  value="<?php echo $partner->invoice_zip?>"  >
                  </div>                                    
                </div>
                &nbsp;
                &nbsp;
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Buyer Code</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="buyer_code" id="buyer_code"  value="<?php echo $partner->buyer_code?>" >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Website</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="website" id="website"  value="<?php echo htmlentities($partner->website)?>"  placeholder="e.g.  www.heksatex.co.id">
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tax Name</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="tax_name" id="tax_name" ><?php echo $partner->tax_nama?>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tax Address</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="tax_address" id="tax_address"  value="<?php echo $partner->tax_address?>" ></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tax City</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="tax_city" id="tax_city"  value="<?php echo $partner->tax_city?>" >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>NPWP</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="npwp" id="npwp"  value="<?php echo $partner->npwp?>" >
                  </div>                                    
                </div>
              </div>


              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Contact Person</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="contact_person" id="contact_person"  value="<?php echo $partner->contact_person?>"  >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Phone</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="phone" id="phone"  value="<?php echo $partner->phone?>"  >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Mobile</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="mobile" id="mobile"  value="<?php echo $partner->mobile?>" placeholder="e.g. 081234568" onkeyup="validAngka(this)" >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Fax</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="fax" id="fax" value="<?php echo $partner->fax?>"  >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Email</label></div>
                  <div class="col-xs-8">
                    <input type="email" class="form-control input-sm" name="email" id="email" value="<?php echo $partner->email?>" placeholder="e.g. hms@heksatex.co.id" >
                  </div>                                    
                </div>
                &nbsp;
                &nbsp;
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Customer</label></div>
                  <div class="col-xs-8">
                    <?php if($partner->customer == 1){
                    ?>
                    <input type="checkbox" name="check_customer" id="check_customer" value="1" checked>
                  <?php }else{ ?>
                    <input type="checkbox" name="check_customer" id="check_customer" value="1" >
                  <?php } ?>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Supplier</label></div>
                  <div class="col-xs-8">
                    <?php if($partner->supplier == 1){
                    ?>
                    <input type="checkbox" name="check_supplier" id="check_supplier" value="1" checked>
                  <?php } else { ?>
                    <input type="checkbox" name="check_supplier" id="check_supplier" value="1" >
                  <?php } ?>
                  </div>                                    
                </div>
              </div>
           
            </div>

            <div class="form-group">
              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Street</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm"name="delivery_street" id="delivery_street"><?php echo $partner->delivery_street?></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Country</label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="delivery_country" id="delivery_country" ></select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery State</label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="delivery_state" id="delivery_state" ></select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery City</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="delivery_city" id="delivery_city" value="<?php echo $partner->delivery_city ?>" >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Zip </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="delivery_zip" id="delivery_zip" value="<?php echo $partner->delivery_zip ?>">
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
   <div id="foot">
     <?php $this->load->view("admin/_partials/footer.php") ?>
   </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    function IsEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
    }

    function validAngka(a){
      if(!/^[0-9.]+$/.test(a.value)){
        a.value = a.value.substring(0,a.value.length-1000);
        alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger',function(){});
      }
    }


    // >>> INVOICE



    $('#invoice_state').select2({
      placeholder : "Select State"
    });

    //select 2 invoice country
    $('#invoice_country').select2({
      allowClear: true,
      placeholder: "Select Country",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>sales/partner/get_country_select2",
            //delay : 250,
            data : function(params){
              return{
                name:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.id,
                    text:item.name
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


    //jika invoice country diubah
    $("#invoice_country").change(function(){

       $("#invoice_state").html('');
        //select 2 invoice state
        $('#invoice_state').select2({
          allowClear: true,
          placeholder: "Select State",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/partner/get_states_select2",
                //delay : 250,
                data : function(params){
                  return{
                    id : $("#invoice_country").val(),
                    name:params.term,
                  };
                }, 
                processResults:function(data){
                  var results = [];
                  $.each(data, function(index,item){
                    results.push({
                        id:item.id,
                        text:item.name
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

    });

    var id_country   = '<?php echo $inv_id_country ?>'; // id country
    var name_country = '<?php echo $inv_nm_country ?>'; // nama country

    //untuk event selected select2 invoice country
    var $newOption = $("<option></option>").val(id_country).text(name_country);
    $("#invoice_country").empty().append($newOption).trigger('change');

    var id_state   = '<?php echo $inv_id_state ?>'; // id state
    var name_state = '<?php echo $inv_nm_state ?>'; // nama state

    //untuk event selected select2 invoice country
    var $newOption = $("<option></option>").val(id_state).text(name_state);
    $("#invoice_state").empty().append($newOption).trigger('change');

    // <<< INVOICE

    // >>> DELIVERY

   
    $('#delivery_state').select2({
       placeholder : "Select State"
    });

    //select 2 delivery country
    $('#delivery_country').select2({
      allowClear: true,
      placeholder: "Select Country",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>sales/partner/get_country_select2",
            //delay : 250,
            data : function(params){
              return{
                name:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.id,
                    text:item.name
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


    //jika  delivery diubah
    $("#delivery_country").change(function(){
        
        $("#delivery_state").html('');
        //select 2 delivery state
        $('#delivery_state').select2({
          allowClear: true,
          placeholder: "Select State",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/partner/get_states_select2",
                //delay : 250,
                data : function(params){
                  return{
                    id : $("#delivery_country").val(),
                    name:params.term,
                  };
                }, 
                processResults:function(data){
                  var results = [];
                  $.each(data, function(index,item){
                    results.push({
                        id:item.id,
                        text:item.name
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

    });

    var id_country   = '<?php echo $dv_id_country ?>'; // id country
    var name_country = '<?php echo $dv_nm_country ?>'; // nama country

    //untuk event selected select2 delivery country
    var $newOption = $("<option></option>").val(id_country).text(name_country);
    $("#delivery_country").empty().append($newOption).trigger('change');

    var id_state   = '<?php echo $dv_id_state ?>'; // id state
    var name_state = '<?php echo $dv_nm_state ?>'; // nama state

    //untuk event selected select2 delivery country
    var $newOption = $("<option></option>").val(id_state).text(name_state);
    $("#delivery_state").empty().append($newOption).trigger('change');


    // <<< DELIVERY

    //klik button simpan
    $('#btn-simpan').click(function(){

      var simpan = true;

      //jika email tidak kosong
      if($('#email').val() != ''){
        if(!IsEmail($('#email').val())){ //email tidak valid
          alert_notify('fa fa-warning','Email Tidak valid','danger',function(){});
          simpan = false;
        }
      }


      if(simpan == true){

        var check_supplier = 0;
        var check_customer = 0;

        if($('#check_supplier').is(':checked')){
          check_supplier = 1;
        }

        if($('#check_customer').is(':checked')){
          check_customer = 1;
        }

        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
           type: "POST",
           dataType: "json",
           url :'<?php echo base_url('sales/partner/simpan')?>',
           beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
           },
           data: {id  :$('#id').val(),
                  name: $('#name').val(),
                  invoice_street    : $('#invoice_street').val(),
                  invoice_city      : $('#invoice_city').val(),
                  invoice_state     : $('#invoice_state').val(),
                  invoice_country   : $('#invoice_country').val(),
                  
                  invoice_zip       : $('#invoice_zip').val(),
                  buyer_code        : $('#buyer_code').val(),
                  website           : $('#website').val(),
                  tax_name          : $('#tax_name').val(),
                  tax_address       : $('#tax_address').val(),
                  tax_city          : $('#tax_address').val(),
                  npwp              : $('#npwp').val(),
                  contact_person    : $('#contact_person').val(),
                  phone             : $('#phone').val(),
                  mobile            : $('#mobile').val(),
                  fax               : $('#fax').val(),
                  email             : $('#email').val(),
              
                  delivery_street  : $('#delivery_street').val(),
                  delivery_city    : $('#delivery_city').val(),
                  delivery_country : $('#delivery_country').val(),
                  delivery_state   : $('#delivery_state').val(),
                  delivery_zip     : $('#delivery_zip').val(),
                  status           : 'edit',
                  check_supplier   : check_supplier,
                  check_customer   : check_customer,


            },success: function(data){
              if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('index');
              }else if(data.status == "failed"){
                //jika ada form belum keiisi
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                document.getElementById(data.field).focus();
              }else{

               //jika berhasil disimpan/diubah
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                $("#foot").load(location.href + " #foot>*");
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