<div class="hmi-header">
  <div class="row">

      <div class="col-sm-4">
        <div class="machine-header-left">

            <!-- REFRESH -->
            <div class="top-btn-icon" onclick="location.reload()" title="Refresh">
                <svg viewBox="0 0 24 24">
                    <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46A7.93 7.93 0 0 0 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74A7.93 7.93 0 0 0 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
                </svg>
            </div>

            <!-- NAMA MESIN -->
            <?php if (!empty($nama_mesin['nama_mesin'])): ?>
                <div class="machine-tag" data-toggle="tooltip" title="<?= htmlentities($nama_mesin['nama_mesin']) ?>">
                    <?= strtoupper($nama_mesin['nama_mesin']) ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- CLOCK -->
    <div class="col-sm-4 text-center">
      <div class="clock-wrapper">
        <div id="clock">00:00:00</div>
        <div id="tanggal">--/--/----</div>
      </div>
    </div>

    <!-- OPERATOR -->
    <div class="col-sm-4">
      <div class="operator">

        <div class="operator-top">
          <div class="operator-icon">
            <i class="fa fa-user"></i>
          </div>

          <div class="operator-info">
            <small><?= strtoupper($operator_name) ?></small>
          </div>
        </div>

        <div class="operator-action">

          <!-- <a href="javascript:void(0)"onclick="location.reload();"
             class="btn-hmi-refresh">
            <i class="fa fa-refresh"></i>
            <span>Refresh</span>
          </a> -->

          <a href="<?= site_url('login/logout') ?>" title="Log out"
             class="btn-hmi-logout">
            <i class="fa fa-sign-out"></i>
            <span>Logout</span>
          </a>

        </div>

      </div>
    </div>

  </div>
</div>