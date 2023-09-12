const login = function (urls) {
    unblockUI(function () {});
    let dialog = bootbox.dialog({
        title: 'Login',
        message: '<form id="form-login" class="form-horizontal" name="form-login">\
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" autocomplete="off" required /><br/>\
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="off" required />\
                            <button type="submit" id="form-login-submit" style="display: none;"></button> \
                          </form>',
        buttons: {
            ok: {
                label: "Login",
                className: 'btn-info',
                callback: function (result) {
                    if (result) {
                        please_wait(function () {});
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: urls,
                                    data: {
                                        username: $("#username").val(),
                                        password: $("#password").val()

                                    },
                            beforeSend: function (e) {
                                if (e && e.overrideMimeType) {
                                    e.overrideMimeType("application/json;charset=UTF-8");
                                }
                            }, success: function (data) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify(data.icon, data.message, data.type, function () {}, 1000);
                                    });
                                });
                                dialog.modal('hide');
                            }, error: function (xhr, ajaxOptions, thrownError) {
                                var data = xhr.responseJSON
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify(data.icon, data.message, data.type, function () {}, 1000);
                                    });
                                });
                                
                            }

                        });
                    }
                    
                return false;
                }
            }
        }
    });
    $('#form-login').submit(false);
}

export {login}