function login() {
    var username = $("#username").val();
    var password = $("#password").val();

    if (username == "" || password == "") {
        popups.tips.add("用户名或密码不能为空", "error_outline");
        return;
    }
    else {
        var send = {
            "username": username,
            "password": password
        };
        sendData("/v2/auth/login", send, function (data) {
            cookie.set("user", data.data.user, 60*60);
            cookie.set("token", data.data.token, 60*60);
            location.reload();
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
        popups.dialog(error);
    }
}