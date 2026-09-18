<div class="hmi-footer">

    <?php if($step >= 1){ ?>
    <a href="<?= site_url('manufacturing/produksi') ?>"
       class="footer-menu <?= $active_menu=='departemen'?'active':'' ?>">
        <i class="fa fa-industry"></i>
        <span>Departement</span>
    </a>
    <?php } ?>

    <?php if($step >= 2){ ?>
    <a href="<?= site_url('manufacturing/produksi/produksiMesin/'.$id_dept) ?>"
       class="footer-menu <?= $active_menu=='mesin'?'active':'' ?>">
        <i class="fa fa-cog"></i>
        <span>Machine</span>
    </a>
    <?php } ?>

    <?php if($step >= 3){ ?>
    <a href="<?= site_url('manufacturing/produksi/listMO/'.$id_dept.'/'.$mc_id) ?>"
       class="footer-menu <?= $active_menu=='mo'?'active':'' ?>">
        <i class="fa fa-list-alt"></i>
        <span>MO</span>
    </a>
    <?php } ?>

    <?php if($step >= 4){ ?>
    <a href="<?= site_url('manufacturing/produksi/produksiHPH/'.$id_dept.'/'.$mc_id.'/'.$kode_mo) ?>"
       class="footer-menu <?= $active_menu=='produksihph'?'active':'' ?>">
        <i class="fa fa-industry"></i>
        <span>Production</span>
    </a>
    <?php } ?>

    <a href="<?= site_url('manufacturing/produksi/setting') ?>"
       class="footer-menu <?= $active_menu=='setting'?'active':'' ?>">
        <i class="fa fa-sliders"></i>
        <span>Setting</span>
    </a>

</div>