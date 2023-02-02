// Sert btn H, H1, H-7, H-30 dipkai (report QC)
function setTgl(value) {
    var tgldari = $('#tgldari').val();
    var tglsampai = $('#tglsampai').val();

    if (value == 'h') { // set date H
        var myDate = new Date();
        months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        var set = (myDate.getDate()) + '-' + months[myDate.getMonth()] + '-' + myDate.getFullYear();
        $("#tgldari").val(set);
        $("#tglsampai").val(set);
    }

    if (value == 'h1') { // set date H.1
        var myDate = new Date(tglsampai);
        months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        var set = (myDate.getDate(), 1) + '-' + months[myDate.getMonth()] + '' + myDate.getFullYear();
        $("#tgldari").val(set);
    }

    if (value == 'h-7') { // set date H-7
        var myDate = new Date(tglsampai);
        months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        myDate.setDate(myDate.getDate() - 6);
        var set = (myDate.getDate()) + '-' + months[myDate.getMonth()] + '' + myDate.getFullYear();
        $("#tgldari").val(set);
    }

    if (value == 'h-30') { // set date H-30
        var myDate = new Date(tglsampai);
        months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            //var set =
        myDate.setDate(myDate.getDate() - 29);
        var set = (myDate.getDate()) + '-' + months[myDate.getMonth()] + '' + myDate.getFullYear();
        $("#tgldari").val(set);
    }

}


function alert_bootbox(message) {
    bootbox.alert({
        title: "<font color='red'><li class='fa fa-warning'></li></font> Warning !",
        message: message,
        size: 'small',
        buttons: {
            ok: {
                label: 'ok',
                className: 'btn-sm btn-primary'
            }
        }

    });
    return;
}