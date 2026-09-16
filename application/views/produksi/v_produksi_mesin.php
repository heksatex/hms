<!DOCTYPE html>
<html>
  <head>
        <?php $this->load->view("produksi/hmi/head") ?>
        <link rel="stylesheet" href="<?=base_url()?>dist/css/hmi/page-mesin.css">

  </head>
  <body> 

    <?php $this->load->view('produksi/hmi/header', $header); ?> 

    <div class="container-fluid hmi-body">
       <div class="page-header">
            <div class="page-header-top">
                <a href="<?= site_url('manufacturing/produksi') ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="page-title">
                    <h2>
                        <i class="fa fa-cog"></i>
                        MESIN <?= strtoupper($nama_departemen) ?>
                    </h2>
                    <!-- <p>
                        <i class="fa fa-sitemap"></i>
                        Pilih salah satu mesin untuk melanjutkan
                    </p> -->
                </div>
                <div class="page-header-actions">

                    <div class="mc-search">
                        <i class="fa fa-search"></i>

                        <input 
                            type="text" 
                            id="searchMesin"
                            placeholder="Cari Mesin ..."
                            autocomplete="off"
                        >

                        <button type="button" id="clearSearch" title="Clear">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <span class="page-total" id="totalMC">
                        <?= count($mesin); ?> Total MC
                    </span>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="machine-grid" id="mc-list">
                <?php 
                    foreach($mesin as $m){ 
                        $this->load->view(
                            'produksi/v_produksi_mesin_card',
                            [
                                'mc'      => $m,
                                'id_dept' => $id_dept
                            ]
                        );
                    }
                ?>
            </div>
            <div id="mc-empty" class="empty-data" style="display:none;">
                Data Tidak ditemukan
            </div>
        </div>
    </div> 
    
    <?php $this->load->view('produksi/hmi/footer'); ?> 
    <?php $this->load->view("produksi/hmi/js") ?>
    <!-- bootbox -->
    <script src="<?php echo base_url('dist/bootbox/bootbox.min.js') ?>"></script>

    <<script>
         $(document).ready(function() {
                const $searchBox = $('.mc-search');
                const $search = $('#searchMesin');
                const $clear = $('#clearSearch');
                const $cards = $('.machine-card');
                const $total = $('#totalMC');
                /* =========================
                SEARCH ICON
                ========================= */
                $searchBox.on('click', '> i', function(e) {
                    // hanya berlaku untuk <= 990px
                    if ($(window).width() <= 990) {
                        e.stopPropagation();
                        $searchBox.addClass('active');
                        setTimeout(function() {
                            $search.focus();
                        }, 100);
                    }
                });
                /* =========================
                SEARCH
                ========================= */
                $search.on('input', function() {
                    const keyword = $.trim($(this).val().toLowerCase());
                    let visibleCount = 0;
                    $cards.each(function() {
                        const searchText = ($(this).attr('data-search') || '').toLowerCase();
                        if (keyword === '' || searchText.indexOf(keyword) !== -1) {
                            $(this).show();
                            visibleCount++;
                        } else {
                            $(this).hide();
                        }
                    });
                    $total.text(visibleCount + ' Total MC');
                    /* tampilkan clear */
                    if (keyword !== '') {
                        $clear.show();
                    } else {
                        $clear.hide();
                    }
                });
                /* =========================
                CLEAR
                ========================= */
                $clear.on('click', function(e) {
                    e.stopPropagation();
                    $search.val('').trigger('input').focus();
                });
                /* =========================
                CLICK OUTSIDE
                ========================= */
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.mc-search').length) {
                        // hanya collapse pada <= 990
                        if ($(window).width() <= 990 && $search.val().trim() === '') {
                            $searchBox.removeClass('active');
                        }
                    }
                });
                
                /* =========================
                ESC
                ========================= */
                $search.on('keydown', function(e) {
                    if (e.key === 'Escape') {
                        $search.val('').trigger('input');
                        if ($(window).width() <= 990) {
                            $searchBox.removeClass('active');
                        }
                        $search.blur();
                    }
                });

                /* =========================
                RESPONSIVE
                ========================= */
                $(window).on('resize', function() {
                    if ($(window).width() >= 991) {
                        // desktop/tablet besar → selalu tampil
                        $searchBox.addClass('active');
                    } else {
                        // tablet kecil → icon saja kalau kosong
                        if ($search.val().trim() === '') {
                            $searchBox.removeClass('active');
                        }
                    }
                });

                let searchTimer;
                $('#searchMesin').on('input', function() {
                    let keyword = $(this).val().trim();
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(function() {
                        $.ajax({
                            url: '<?= site_url("manufacturing/produksi/searchMesin") ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                keyword: keyword,
                                id_dept: '<?= $id_dept ?>',
                            },
                            success: function(response) {
                                $('#mc-list').html(response.html);
                                $('#totalMC').text(response.total+ ' Total MC');
                                if (response.total == 0) {
                                    $('#mc-empty').show();
                                } else {
                                    $('#mc-empty').hide();
                                }
                            }
                        });
                    }, 300);
                });


                function setDefaultMachine(deptId, mcId) {
                    let defaults = JSON.parse(localStorage.getItem('default_machine') || '[]');
                    // Pastikan selalu array
                    if (!Array.isArray(defaults)) {
                        defaults = [];
                    }
                    // Hapus kombinasi yang sama
                    defaults = defaults.filter(function(item) {
                        return !(String(item.dept_id) === String(deptId) && String(item.mc_id) === String(mcId));
                    });
                    defaults.push({
                        dept_id: deptId,
                        mc_id: mcId
                    });
                    localStorage.setItem('default_machine', JSON.stringify(defaults));
                }
                
                $(document).on('click', '.machine-card', function(e) {
                    e.preventDefault();
                    const $card = $(this);
                    const mcId = $card.data('mc-id');
                    const machineName = $card.data('machine');
                    const targetUrl = $card.attr('href');
                    bootbox.confirm({
                        title: '<i class="fa fa-cog"></i> Default Mesin',
                        message: 'Apakah mesin <strong>' + machineName + '</strong> ingin dijadikan mesin default?',
                        buttons: {
                            cancel: {
                                label: '<i class="fa fa-times"></i> Tidak',
                                className: 'btn-default'
                            },
                            confirm: {
                                label: '<i class="fa fa-check"></i> Ya, Jadikan Default',
                                className: 'btn-primary'
                            }
                        },
                        callback: function(result) {
                            if (result) {
                                setDefaultMachine('<?= $id_dept ?>', mcId);
                            }
                            window.location.href = targetUrl;
                        }
                    });
                });


                const defaultMachine = localStorage.getItem('default_machine');
                if (!defaultMachine) {
                    return;
                }
                try {
                    const defaults = JSON.parse(defaultMachine);
                    // Pastikan formatnya array
                    if (!Array.isArray(defaults)) {
                        localStorage.removeItem('default_machine');
                        return;
                    }
                    const currentDeptId = String('<?= $id_dept ?>');
                    // Cari default machine untuk department ini
                    const defaultData = defaults.find(function(item) {
                        return String(item.dept_id) === currentDeptId;
                    });
                    // Tidak ada default untuk department ini
                    if (!defaultData) {
                        return;
                    }
                    const deptId = String(defaultData.dept_id);
                    const mcId = String(defaultData.mc_id);
                    if (mcId) {
                        window.location.href = '<?= site_url("manufacturing/produksi/listMO") ?>/' + deptId + '/' + mcId;
                    }
                } catch (e) {
                    localStorage.removeItem('default_machine');
                    console.log('Default machine tidak valid');
                }
           

        });
    </script>

  </body>
</html>