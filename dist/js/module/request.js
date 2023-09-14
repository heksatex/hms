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
                        unblockUI(function (){})
                        resolve(data)
                    },
                    error: function (error) {
                        dialog.modal('hide');
                        unblockUI(function (){})
                        reject(error)
                    }
                });

            }
        });

    });


}

export {requests, requestDelete}