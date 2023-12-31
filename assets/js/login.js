function login() {
    var username = $("#username").val();
    var password = $("#password").val();

    if (username == "" || password == "") {
        message("用户名或密码不能为空");
        return;
    }
    else {
        var send = {
            "username": username,
            "password": password
        };
        sendData("/v2/auth/login", send, function (data) {
            try {
                if (data.data.login == "success") {
                    cookie.set("user", data.data.user, 30);
                    cookie.set("token", data.data.token, 30);
                    location.reload();
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
        data["token"] = cookie.get("token");
        $.post(url, data, function (data, status) {
            callback(data, status);
        });
    } catch (error) {
        message(error);
    }
}