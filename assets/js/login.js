console.log("\n %c molanp \n\n","color: #fadfa3; background: #2f55d4; padding:5px 0;")

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
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

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
            if (status === 'success') {
                // 请求成功，处理响应数据
                //console.log(data.data);
                document.cookie=`user=${data.data.user};`;
                document.cookie=`token=${data.data.token};`;
                location.reload();
            } else {
                // 请求失败，处理错误信息
                console.error(data);
                regFail(JSON.stringify(data.data));
            }
          });

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
    swal.fire({
        icon: "error",
        title: "失败!",
        text: reason,
        footer: "必要时可询问提交Issues",
    });
}

function regsuc(data) {
    swal.fire({
        icon: "success",
        title: "修改成功",
        text: data,
        confirmButtonText: '确定'
    });
    }

function resetpassword() {
    swal.fire({
    title: '修改密码',
    html: `<div class="form"><div class="mdui-textfield">
    <input id="new" class="mdui-textfield-input" type="text" placeholder="新的密码" required />
    <div class="mdui-textfield-error">密码不能为空</div>
    </div>
    <div class="mdui-textfield">
    <input id="again" class="mdui-textfield-input" type="text" placeholder="再输一次" required />
    <div class="mdui-textfield-error">密码不能为空</div>
    </div></div>`,
    showCancelButton: true,
    confirmButtonText: '提交',
    preConfirm: () => {
        sendData('/v2/login',{
            'type':'pass',
            'token':getCookie("token"),
            'new':document.getElementById("new").value,
            'again':document.getElementById("again").value
        },function(data,status){
            if (status === 'success') {
                if (data.status == 200) {
                    regsuc(data.data);
                    loginout()
                } else {
                    regFail(data.data)
                }
            } else {
                // 请求失败，处理错误信息
                //console.error(data);
                regFail('连接服务器超时，请重试');
            }
        });
    }
  });
}

function loginout() {
    document.cookie=`token=; expires=Thu, 01 Jan 1970 00:00:00 GMT";`;
    document.cookie=`user=; expires=Thu, 01 Jan 1970 00:00:00 GMT";`;
    location.reload();
}