<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
    <a href="<?= !$disabled ? $url : 'javascript:void(0)' ?>">
     <div class="dept-card <?= $disabled ? 'disabled' : '' ?>">
        <div class="dept-icon">
            <i class="fa <?= $icon ?>"></i>
        </div>
        <div class="dept-name">
            <?= strtoupper($nama) ?>
        </div>
      </div>
    </a>
</div>