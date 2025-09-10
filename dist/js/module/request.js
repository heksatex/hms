const requests = function (form_name) {
    const formData = new FormData($('#' + form_name)[0]);
    const request = new XMLHttpRequest();

    return new Promise((resolve, reject) => {
        request.onreadystatechange = (e) => {
            if (request.readyState !== 4) {
                if (request.readyState === 1) {
                    request.setRequestHeader("_request", "json");
                }
                return;
            }

            resolve({status: request.status, data: JSON.parse(request.responseText)})
        };

        request.open($('#' + form_name).attr('method'), $('#' + form_name).attr('action'), true);

        request.send(formData);
    });

}

const requestDelete = function (uri, objectData = {}) {


    return new Promise((resolve, reject) => {
        let dialog = bootbox.confirm({
            size: 'small',
            message: 'Yakin Menghapus Dokumen?',
            callback: function (result) {
                if (!result) {

                    return true;
                }
                please_wait(function () {});
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: uri,
                    data: objectData,
                    success: function (data) {
                        dialog.modal('hide');
                        unblockUI(function () {})
                        resolve(data)
                    },
                    error: function (error) {
                        dialog.modal('hide');
                        unblockUI(function () {});
                        reject(error);
                    }
                });

            }
        });

    });


};


const inputPin = ((uri, callback = function() {}) => {
    return new Promise((resolve, reject) => {
        bootbox.prompt({
            title: 'Masukan Pin.',
            centerVertical: true,
            callback: function (result) {
                if (!result)
                    return;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: uri,
                    data: {
                        pin: result
                    },
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    success: function (data) {
                        callback();
                        resolve(data);
                    },
                    error: function (req, error) {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                            }, 500);
                        });
                        reject(error);
                    }
                });
            }
        });
    });

});

const ConfirmRequest = function (title, message, cb) {
    bootbox.confirm({
        title: title,
        message: message,
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> Tidak'
            },
            confirm: {
                label: '<i class="fa fa-check"></i> YA'
            }
        },
        callback: function (result) {
            if (result) {
                cb();
            }
        }
    });
};

export {requests, requestDelete, ConfirmRequest, inputPin}