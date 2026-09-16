<!DOCTYPE html>
<html>
  <head>
        <?php $this->load->view("produksi/hmi/head") ?>
  </head>
  <body> 

    <?php $this->load->view('produksi/hmi/header', $header); ?> 
    <div class="container-fluid hmi-body">
        <div class="page-header">
            <div class="page-header-top">
                <a href="<?= site_url('manufacturing/produksi/produksiMesin/'.$id_dept) ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="page-title">
                    <h2>
                        <i class="fa fa-list-alt"></i>ANTRIAN MANUFACTURING ORDER (MO) 
                        <small>
                            Departemen <?= $nama_departemen ?>
                        </small>
                        <small>
                            <?= $nama_mesin['nama_mesin']?>
                        </small>
                    </h2>
                </div>
            </div>
        </div>
        <div class="row"> 
            <span class="empty-data">Departemen <?= $nama_departemen ?> masih dalam pengembangan </span>
        </div>
    </div> 
    
    <?php $this->load->view('produksi/hmi/footer'); ?> 
    <?php $this->load->view("produksi/hmi/js") ?>
  </body>
</html>