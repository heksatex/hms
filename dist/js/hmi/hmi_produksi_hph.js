function renderHistoryList() {
    let container = document.getElementById('history-list');
    container.innerHTML = '';

    savedLogs.forEach((item) => {
        let card = document.createElement('div');
        card.className = 'history-card';
        card.innerHTML = `
                    <div class="history-card-top">
                        <span class="history-beam">${item.beam}</span>
                        <span class="history-time">â± ${item.timeStr}</span>
                    </div>
                    <div style="font-size:0.75rem; color:#64748B; font-family:monospace; margin-bottom:2px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">
                        ${item.lot}
                    </div>
                    <div class="history-card-details">
                        <div class="history-detail-item">GRADE: <span class="grade-badge">${item.grade}</span></div>
                        <div class="history-detail-item">QTY: <span class="val">${item.qty.toLocaleString()} Mtr</span></div>
                        <div class="history-detail-item">QTY 2: <span class="val">${item.qty2} Kg</span></div>
                        <div class="history-detail-item">UI: <span class="val">${item.ui}</span></div>
                        <div class="history-detail-item">UA: <span class="val">${item.ua}</span></div>
                        <div class="history-detail-item">W: <span class="val">${item.waste} Kg</span></div>
                    </div>
                `;
        container.appendChild(card);
    });

    // Update jumlah counter badge
    document.getElementById('history-count').innerText = savedLogs.length + ' LOT';
    document.getElementById('toolbar-count').innerText = savedLogs.length;
}

$(document).ready(function () {

    updateClock2();
    setInterval(updateClock2, 1000);

    $('#grade').select2({
        placeholder: 'Pilih ',
        allowClear: true
    });

    $('#kodeCacat').select2({
        placeholder: 'Pilih ',
        allowClear: true,
        dropdownParent: $('#kodeCacat').closest('.defect-field')

    });


    $("#beam").select2({
        width: "100%",
        placeholder: "Pilih",
        minimumInputLength: 1,
        allowClear: true,
        ajax: {
            url: LOT_CONFIG.loadUrlBeam,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $("#beam").on("select2:select", function (e) {

        var lot_prefix = LOT_CONFIG.lot_prefix;
        var item = e.params.data;
        $("#lot").val(lot_prefix + item.id);
        $("#ui").val(item.ui);

    });

    // clear beam
    $('#beam').on('select2:clear', function () {
        clearBeamInfo();
    });

    // unselect (untuk jaga-jaga jika event ini terpanggil)
    $('#beam').on('select2:unselect', function () {
        clearBeamInfo();
    });

    function clearBeamInfo() {
        $('#lot').val('');
        $('#ui').val('');
    }



});

const numberFormatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
});


loadLot('');

function loadLot(newLot) {

    var $lotList = $('.lot-list');

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: LOT_CONFIG.loadUrl,
        data: {
            kode: LOT_CONFIG.kodeMO,
            id_dept: LOT_CONFIG.idDept
        },

        beforeSend: function () {

            $lotList.html(`<div class="lot-empty" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                    <span>Memuat LOT...</span>
                                </div >
                `);

        },
        success: function (response) {
            $lotList.empty();
            // Tidak ada data
            if (!response || response.data.length === 0) {

                $lotList.html(`<div class="lot-empty" >
                                    <i class="fa fa-inbox"></i>
                                    <span>Belum ada LOT tersimpan</span>
                                </div >
                    `);
                return;
            }


            $(".lot-count").html(response.data.length + ' LOT');

            // Ada data
            $.each(response.data, function (i, lot) {
                var html = `
                        <div class="lot-item ${(lot.lot_adj == true) ? 'lot_adj' : ''}" data-lot="${lot.lot}" data-quant="${lot.quant_id}">
                            <div class="lot-selected-check">
                                <i class="fa fa-check"></i>
                            </div>
                            <!--HEADER LOT -->
                            <div class="lot-item-header">
                                <strong class="lot-number">
                                    ${lot.seri_beam}
                                </strong>
                                ${(lot.lot_adj == true) ? ' <small>ADJ</small>' : ''}
                                <span class="lot-time">
                                    ${lot.time}
                                </span>
                            </div>

                            <!--REFF / KODE LOT -->
                            <div class="lot-code">
                                ${lot.lot}
                            </div>

                            <!--INFO -->
                            <div class="lot-info-grid">

                                <!-- GRADE -->
                                <div class="lot-info-row">
                                    <span class="lot-label">
                                        GRADE:
                                    </span>
                                    <strong class="lot-grade">
                                        ${lot.grade}
                                    </strong>
                                </div>

                                <!-- QTY -->
                                <div class="lot-info-row">
                                    <span class="lot-label">
                                        QTY:
                                    </span>
                                    <strong>
                                        ${numberFormatter.format(lot.qty)}
                                        <small>${lot.uom}</small>
                                    </strong>
                                </div>

                                <!-- QTY 2 -->
                                <div class="lot-info-row">
                                    <span class="lot-label">
                                        QTY 2:
                                    </span>
                                    <strong>
                                        ${numberFormatter.format(lot.qty2)}
                                        <small>${lot.uom2}</small>
                                    </strong>
                                </div>

                                <!-- UI -->
                                <div class="lot-info-row">
                                    <span class="lot-label">
                                        UI:
                                    </span>
                                    <strong>
                                        ${lot.ui}
                                    </strong>
                                </div>

                                <!-- UA -->
                                <div class="lot-info-row">
                                    <span class="lot-label">
                                        UA:
                                    </span>
                                    <strong>
                                        ${lot.ua}
                                    </strong>
                                </div>

                                <!-- W -->
                                <div class="lot-info-row">
                                    <span class="lot-label">
                                        W:
                                    </span>
                                    <strong>
                                        ${lot.w}
                                    </strong>
                                </div>

                            </div>
                    </div >
                    `;
                $lotList.append(html);
            });

            $lotList.off('click', '.lot-item').on('click', '.lot-item', function () {

                $(this).toggleClass('selected');

                updateSelectedLot();

                console.log('Selected LOT:', getSelectedLots());
            });
            if (newLot) {
                markNewLot(newLot);
            }

        },

        error: function (xhr, status, error) {

            console.log(xhr.responseText);

            $lotList.html(`<div class="lot-empty" >
                            <i class="fa fa-exclamation-circle"></i>
                            <span>Gagal memuat LOT</span>
                        </div >
                `);
        }
    });
}

function markNewLot(newLot) {

    // Bersihkan tanda sebelumnya
    $(".lot-item").removeClass("lot-new");

    // Ambil semua item dengan LOT tersebut
    var $items = $(".lot-item").filter(function () {
        return $(this).attr("data-lot") == newLot;
    });

    if ($items.length) {

        // Ambil yang PALING AKHIR
        var $newItem = $items.first();

        $newItem.addClass("lot-new");

        // Scroll ke LOT yang baru
        $newItem[0].scrollIntoView({
            behavior: "smooth",
            block: "nearest"
        });

        // Hilangkan tanda setelah 5 detik
        setTimeout(function () {
            $newItem.removeClass("lot-new");
        }, 5000);
    }
}


$(document).on('focus', '.select2', function (e) {
    if (e.originalEvent) {
        var s2element = $(this).siblings('select');
        s2element.select2('open');

        // Set focus back to select2 element on closing.
        s2element.on('select2:closing', function (e) {
            s2element.select2('focus');
        });
    }
});




$('#btn-print-lot').off('click').on('click', function () {
    var $btn = $(this);
    var selectedLots = getSelectedLots();
    if (selectedLots.length === 0) {
        return;
    }
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: LOT_CONFIG.loadUrlPrint,
        data: {
            kode: LOT_CONFIG.kodeMO,
            id_dept: LOT_CONFIG.idDept,
            lots: selectedLots
        },
        beforeSend: function () {
            $btn.prop('disabled', true);
            $btn.find('i').removeClass('fa-print').addClass('fa-spinner fa-spin');
        },
        success: function (response) {
            if (response && response.type == 'success') {
                console.log('Print berhasil:', response);
                alert_notify("fa fa-check", response.message, response.type, function () { })
                // nanti di sini kita lanjutkan proses print
                // atau tampilkan preview label
            } else {
                console.log('Print gagal:', response);
                alert(response.message || 'Gagal memproses LOT untuk print.');
            }
        },
        error: function (xhr, status, error) {
            console.log('AJAX ERROR:', xhr.responseText);
            alert('Terjadi kesalahan saat mengirim data print.');
        },
        complete: function () {
            $btn.prop('disabled', getSelectedLots().length === 0);
            $btn.find('i').removeClass('fa-spinner fa-spin').addClass('fa-print');
        }
    });
});

function getSelectedLots() {

    var selectedLots = [];

    $('.lot-list .lot-item.selected').each(function () {

        selectedLots.push({
            lot: $(this).data('lot'),
            quant_id: $(this).data('quant')
        });

    });

    return selectedLots;
}


$('#check-all-lot').off('change').on('change', function () {

    var checked = $(this).is(':checked');

    $('.lot-list .lot-item').each(function () {
        $(this).toggleClass('selected', checked);
    });

    updateSelectedLot();
});

function updateSelectedLot() {

    var selectedLots = getSelectedLots();

    $('#selected-lot-count').text(
        selectedLots.length + ' LOT DIPILIH'
    );

    $('#btn-print-lot').prop(
        'disabled',
        selectedLots.length === 0
    );

    // Update Check All
    var totalLots = $('.lot-list .lot-item').length;

    $('#check-all-lot').prop(
        'checked',
        totalLots > 0 && selectedLots.length === totalLots
    );
}


$('#btnChecklistLot').on('click', function () {

    var $btn = $(this);
    var $icon = $btn.find('i');

    $btn.toggleClass('active');

    var isPrintLot = $btn.hasClass('active');

    $btn.attr(
        'data-print-lot',
        isPrintLot ? '1' : '0'
    );

    if (isPrintLot) {

        $icon
            .removeClass('fa-square-o')
            .addClass('fa-check-square-o');

    } else {

        $icon
            .removeClass('fa-check-square-o')
            .addClass('fa-square-o');

    }

});