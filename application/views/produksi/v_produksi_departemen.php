<!DOCTYPE html>
<html>
  <head>
        <?php $this->load->view("produksi/hmi/head") ?>
        <link rel="stylesheet" href="<?=base_url()?>dist/css/hmi/departement.css">
  </head>
  <body> 

    <?php $this->load->view('produksi/hmi/header', $header); ?> 
    <div class="container-fluid hmi-body">
        <div class="page-header">
            <div class="page-header-top">
                <a href="<?= site_url('/') ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="page-title">
                    <h2><i class="fa fa-building"></i>DEPARTEMENT</h2>
                </div>
                <span class="page-total"> <?= count($departemen); ?> Total Dept </span>
            </div>
        </div>
        <div class="row"> 
            <?php foreach($departemen as $d){ ?> 
            <?php $this->load->view('produksi/v_produksi_department_card',$d); ?> <?php } ?> 
        </div>
    </div> 
    
    <?php $this->load->view('produksi/hmi/footer'); ?> 
    <?php $this->load->view("produksi/hmi/js") ?>
  </body>
</html>