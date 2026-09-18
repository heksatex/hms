    <!-- 1. TOP HEADER BAR -->
    <div class="top-bar">
        <div class="top-left">
            <div class="top-btn-icon" onclick="location.reload()">
                <svg viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46A7.93 7.93 0 0 0 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74A7.93 7.93 0 0 0 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
            </div>
            <div class="status-badge">
                <span>Produksi sedang berlangsung</span>
                <span class="status-circle"></span>
            </div>
        </div>

        <!-- Tag Mesin di Posisi Tengah -->
        <div class="top-center">
            <div class="machine-tag" title="<?= htmlentities($nama_mesin['nama_mesin']) ?>"><?= $nama_mesin['nama_mesin'] ?></div>
        </div>

        <!-- Jam dan Tanggal Realtime di Kanan -->
        <div class="top-right">
            <div class="datetime-box">
                  <span class="datetime-time" id="live-time">00:00:00</span>
                <span class="datetime-date" id="live-date">--/--/----</span>
               
            </div>
            <div class="top-right-item">
               <div class="item-icon">
                    <i class="fa fa-user"></i>
                </div>
                <div class="item-info">
                    <small> <?= strtoupper($operator_name) ?> </small>
                </div>
            </div>
            <!-- Logout -->
            <a href="<?= site_url('login/logout') ?>" title="Log out">
                <div class="top-btn-icon logout-btn">
                    <i class="fa fa-sign-out"></i>
                </div>
            </a>
        </div>
    </div>