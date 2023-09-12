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

export {requests}