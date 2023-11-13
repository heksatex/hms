<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo /* SITE_NAME . */"HMS : " . ucfirst($this->uri->segment(1)) . " - " . ucfirst($this->uri->segment(2)) ?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Favicon -->
<link rel="shortcut icon"  href="<?php echo base_url('dist/img/favicon_heksa.ico') ?>">
<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="<?php echo base_url('bootstrap/css/bootstrap.min.css') ?>">
<!-- SELECT 2 -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/select2/css/select2.min.css') ?>">
<!-- Font Awesome -->
<link rel="stylesheet" href="<?php echo base_url('dist/fa/css/font-awesome.min.css') ?>">
<!-- Ionicons -->
<link rel="stylesheet" href="<?php echo base_url('dist/ionicons/css/ionicons.min.css') ?>">
<!-- Data Tabel -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css') ?>">
<!-- DaTables checkbox -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/checkbox/css/dataTables.checkboxes.css') ?>">
<!-- DaTables button -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/button/button.dataTables.min.css') ?>">
<!-- Data Tabel 1.10.18 -->
<!--link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/DataTables-1.10.18/css/dataTables.bootstrap.css') ?>"-->
<!-- Data table row group -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/DataTables-1.10.18/css/rowGroup.bootstrap4.min.css') ?>">
<!-- Theme style -->
<link rel="stylesheet" href="<?php echo base_url('dist/css/AdminLTE.css') ?>">
<!-- AdminLTE Skins. Choose a skin from the css/skins -->
<link rel="stylesheet" href="<?php echo base_url('dist/css/skins/_all-skins.min.css') ?>">
<!-- Date Time Picker -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/date/css/bootstrap-datetimepicker.min.css') ?>">
<!-- tags input -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/tags-input/bootstrap-tagsinput.css') ?>">
<!-- my CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/mycss.css') ?>">

<?php
date_default_timezone_set('Asia/Jakarta'); //default waktu jakarta
?>


<style type="text/css">

    /*untuk tampilan datetime picker*/
    .dtp {
        z-index: 999 !important;
    }


    /*css untuk ukutan font setiap tabel */
    th {
        font-size: 13px;
    }
    td {
        font-size: 12px;
    }

    /*atur lebar no di setiap tabel*/
    .no {
        width: 1%;
    }

    /*css untuk tabel di view edit */
    .style {

        background: #F0F0F0;

    }

    /*css untuk tabel view list */
    .table-striped tbody tr:nth-child(odd) td,
    .table-striped tbody tr:nth-child(odd) th {
        background-color: #DFF0D8;
    }

    /*css untuk modal view/edit/tambah data */
    @media screen and (min-width: 768px) {
        .lebar .modal-dialog  {
            width:80%;
        }
    }

    /*css untuk modal mode print data */
    @media screen and (min-width: 768px) {
        .lebar_mode .modal-dialog  {
            width:40%;
        }
    }

    form .required:after {
        content: " *";
        color: red;
        font-weight: 100;
    }

    /*create auto number di tabel*/
    .rlstable {
        counter-reset: row-num;
    }
    .rlstable tbody tr.num  {
        counter-increment: row-num;
    }

    .rlstable tr.num td:first-child::before {
        content: counter(row-num) ". ";
    }
    .rlstable tr.num td:first-child {
        text-align: center;
    }

    /*untuk body modal
    .modal-body {
      overflow-x: auto;
    }
    */


    /*wrap text list datatables*/
    .text-wrap{
        white-space:normal;
    }
    .width-400{
        width:400px;
    }
    .width-300{
        width:300px;
    }
    .width-220{
        width:220px;
    }
    .width-200{
        width:200px;
    }
    .width-160{
        width:160px;
    }
    .width-140{
        width:140px;
    }
    .width-150{
        width:150px;
    }
    .width-130{
        width:130px;
    }
    .width-120{
        width:120px;
    }
    .width-100{
        width:100px;
    }
    .width-80{
        width:80px;
    }
    .width-50{
        width:50px;
    }

    /*break word setiap table*/
    table td{
        word-wrap: break-word;
        max-width: 400px;
    }

</style>
<div id="printed" style="display: none">
</div>
