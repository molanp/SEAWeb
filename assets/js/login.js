function getCookie(cname)
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) 
    {
        var c = ca[i].trim();
        if (c.indexOf(name)==0) return c.substring(name.length,c.length);
    }
    return "";
}

function login() {
    var username = $("#username").val();
    var password = $("#password").val();

    if (username == "" || password == "") {
        regFail("用户名或密码不能为空");
        return;
    }
    else{
        var send = {
            'username':username,
            'password':password
        };
        sendData("/v2/login", send, function(data, status) {
            try {
                if (status === 'success') {
                    if (data.data.login == 'success') {
                        document.cookie=`user=${data.data.user};`;
                        document.cookie=`token=${data.data.token};`;
                        location.reload();
                    } else {
                        regFail(JSON.stringify(data.data.msg));
                    }
                } else {
                    regFail(JSON.stringify(data.data));
                }
            } catch {
                regFail(JSON.stringify(data));
              }
        })
    }
}

function sendData(url, data, callback) {
    try {
      $.post(url, data, function(data, status) {
        callback(data, status);
      });
    } catch(error) {
      regFail("TimeOutError");
    }
  }

function regFail(reason) {
    mdui.dialog({
        content: reason,
        buttons: [{
            text: '确定',
        }]
    });
}

function regsuc(data) {
    mdui.dialog({
        content: data,
        buttons: [{
            text: '确定',
        }]
    });
    }

