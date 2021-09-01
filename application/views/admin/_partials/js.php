<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url('plugins/jQuery/jquery-2.2.3.min.js') ?>"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url('bootstrap/js/bootstrap.min.js') ?>"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.min.js') ?>"></script>
<!-- FastClick -->
<script src="<?php echo base_url('plugins/fastclick/fastclick.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('dist/js/app.min.js') ?>"></script>
<!-- Jquery DataTables  1.10.18-->
<script type="text/javascript" src="<?php echo base_url('plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') ?>"></script>
<!-- Bootstrap Javascript -->
<script type="text/javascript" src="<?php echo base_url('plugins/DataTables-1.10.18/js/dataTables.bootstrap4.min.js') ?>"></script>
<!-- DaTables Checkbox -->
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/checkbox/js/dataTables.checkboxes.min.js') ?>"></script>
<!-- Data table row group -->
<!--script type="text/javascript" src="<?php echo base_url('plugins/DataTables-1.10.18/js/dataTables.rowGroup.min.js') ?>"></script-->
<!-- Date Time Picker Javascript -->
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/date/js/moment.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/date/js/bootstrap-datetimepicker.min.js') ?>"></script>
<!-- select2-->
<script type="text/javascript" src="<?php echo site_url('dist/select2/js/select2.min.js') ?>"></script>
<!-- bootbox -->
<script src="<?php echo base_url('dist/bootbox/bootbox.min.js') ?>"></script>
<!-- Block UI -->
<script src="<?php echo base_url('dist/blockui/jqueryblockUI.js') ?>"></script>
<!-- Notify -->
<script src="<?php echo base_url('dist/notify/bootstrap-notify.js') ?>"></script>
<script src="<?php echo base_url('dist/notify/bootstrap-notify.min.js') ?>"></script>
<!-- Tags Input -->
<script src="<?php echo base_url('dist/tags-input/bootstrap-tagsinput.js') ?>"></script>
 

<script type="text/javascript">
    $(function () {
        $('#datetimepicker1').datetimepicker({
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
        $('#datetimepicker2').datetimepicker({
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
        $('#datetimepicker3').datetimepicker({
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
        $('#datetimepicker4').datetimepicker({
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
    });

 
  //untuk alert notify
  function alert_notify(icon,message,type,callback){
    $.notify({
      icon: icon,
      message: message,  
    },{
      type : type,
      allow_dismiss: true,
      newest_on_top: false,
      showProgressbar: false,
      placement: {
        from: "top",
        align: "right"
      },
      z_index: 2000,
      //delay: 500,
      timer: 500,
    });
    callback();
  }

  //untuk  loading saat proses klik button
  function please_wait(callback){
    //$('#block-page').block({ 
   $.blockUI({ 
      message:  '<h4><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br> Please wait...</h4>',
      //theme: false,
      baseZ: 2000,
      css: { 
          border: 'none', 
          padding: '0px', 
          backgroundColor: '#000', 
              '-webkit-border-radius': '10px', 
              '-moz-border-radius': '10px', 
          opacity: .5, 
          color: '#fff',
          clear: "both",
    },

    });
    callback();
  }

  //unblock UI 
  function unblockUI(callback){
     setTimeout($.unblockUI, 1000);
     callback();
  }

  //alert modal Warning
  function alert_modal_warning(message){
    bootbox.alert({
            title: "<font color='red'><li class='fa fa-warning'></li></font> Warning !",
            message: message,
           // size: 'small',
            buttons:{
              ok: {
                label: "ok",
                className: 'btn-sm btn-primary',
              }
            }
    });
    //callback();
    return true;
  }

</script>