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
        sendData("/v2/auth/login", send, function (data, status) {
            try {
                if (status === "success") {
                    if (data.data.login == "success") {
                        setCookie("user",data.data.user, 30);
                        setCookie("token", data.data.token, 30);
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
        data["token"] = getCookie("token");
        $.post(url, data, function (data, status) {
            callback(data, status);
        });
    } catch (error) {
        message(error);
    }
}

function message(data, title="") {
    mdui.dialog({
        headline: title,
        body: data,
        actions: [{
            text: "确定",
        }]
    });
}

