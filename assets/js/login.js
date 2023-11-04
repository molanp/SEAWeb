function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function login() {
    var username = $("#username").val();
    var password = $("#password").val();

    if (username == "" || password == "") {
        message("用户名或密码不能为空");
        return;
    }
    else {
        var send = {
            'username': username,
            'password': password
        };
        sendData("/v2/auth/login", send, function (data, status) {
            try {
                if (status === 'success') {
                    if (data.data.login == 'success') {
                        document.cookie = `user=${data.data.user};`;
                        document.cookie = `token=${data.data.token};`;
                        location.reload();
                    } else {
                        message(data.data.msg);
                    }
                } else {
                    message(data.data);
                }
            } catch {
                message(data);
            }
        })
    }
}

function sendData(url, data, callback) {
    try {
        $.post(url, data, function (data, status) {
            callback(data, status);
        });
    } catch (error) {
        message(error);
    }
}

function message(data) {
    mdui.dialog({
        body: data,
        actions: [{
            text: '确定',
        }]
    });
}

