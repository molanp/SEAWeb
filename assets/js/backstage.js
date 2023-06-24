window.onload = function() {
    load_home();
    hash(window.location.hash);
}

window.addEventListener("hashchange", function() {
    hash(window.location.hash);
});

function hash(hash) {
    if (hash=='') {
        load_home();
    } else if(hash=='#status') {
        load_status();
    }
}

function load_status() {
    $.get(
        url=window.location.origin+'/v2/info',
        data={"for":"status"},
        function(data,status) {
            if (status=='success') {
                let content = document.getElementsByClassName('content')[0];
                var data = data.data;
                var list = `<table><thead>
                <tr><th>API Name</th>
                <th>Status</th>
                </tr></thead><tbody>`;
                for(var key in data) {
                    if (data[key]==true) {
                        list += `<tr><td>${key}</td><td>
                        <div class="mdui-col">
                        <label class="mdui-switch">
                          <input type="checkbox" id="${key}" name="checkbox" checked>
                          <i class="mdui-switch-icon"></i>
                          </lable>
                        </div>
                        </td></tr>`;
                    } else {
                        list += `<tr><td>${key}</td><td>
                        <div class="mdui-col">
                        <label class="mdui-switch">
                          <input type="checkbox" id="${key}" name="checkbox">
                          <i class="mdui-switch-icon"></i>
                          </lable>
                        </div>
                        </td></tr>`;
                    }
                };
                list += "</tbody></table>"
                content.innerHTML = `
                <h3>Edit API Status</h3>
                <br>
                <span name="api_list">${list}</span>
                <br>
                <button onclick='save_api()'
                class='login-button mdui-btn mdui-btn-raised mdui-ripple'>Save</button>`;
            }
        }
    );
}

function load_home() {
    $.get(
        url=window.location.origin+'/v2/info',
        data= {"for":"web"},
        function(data,status) {
            if (status=='success') {
                let content = document.getElementsByClassName('content')[0];
                var data = data.data;
                content.innerHTML = `
                <blockquote>SEAWeb版本:<span name="version">${data.version}</span>(最新版本:<span name="latest"><a href="javascript:check_update()">Check Update</a></span>)</blockquote>
                <h3>修改网页信息</h3>
                <br>
                网站标题：<p><textarea style='width:75%;height: 200px;' name='index_title'>${data.index_title}</textarea></p>
                网站简介信息：<p><textarea style='width:75%;height: 200px;' name='index_description'>${data.index_description}</textarea></p>
                网站公告：<p><textarea style='width:75%;height: 200px;' name='notice'>${data.notice.data}</textarea></p>
                网站底部版权信息：<p><textarea style='width:75%;height: 200px;' name='copyright'>${data.copyright}</textarea></p>
                网页备案号：<p><textarea style='width:75%;height: 200px;' name='record'>${data.record}</textarea></p>
                友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea style='width:75%;height: 200px;' name='links'>${data.links}</textarea></p>
                网站keywords(逗号分隔)：<p><textarea style='width:75%;height: 200px;' name='keywords'>${data.keywords}</textarea></p>
                <button onclick='save()'
                class='login-button mdui-btn mdui-btn-raised mdui-ripple'>Save</button>`

            }
        }
    );
}

function save() {
    var send = {
        'for':'edit_web',
        'token':getCookie('token'),
        'record':document.getElementsByName("record")[0].value,
        'index_title':document.getElementsByName("index_title")[0].value,
        'copyright':document.getElementsByName("copyright")[0].value,
        'index_description':document.getElementsByName("index_description")[0].value,
        'notice':document.getElementsByName("notice")[0].value,
        'keywords':document.getElementsByName("keywords")[0].value,
        'links':document.getElementsByName("links")[0].value
    };
    sendData("/v2/info", send, function(data, status) {
        if (status === 'success') {
            if (data.status == 200) {
                regsuc(data.data);
            } else {
                regFail(data.data);
            }
        } else {
            regFail("连接服务器失败");
        }
});
}

function save_api() {
    var checkboxes = document.getElementsByName('checkbox');
    var checkboxStatus = {};

    for (var i = 0; i < checkboxes.length; i++) {
    var checkbox = checkboxes[i];
    checkboxStatus[checkbox.id] = checkbox.checked;
    }
    console.log(checkboxStatus);
    var send = {
        'for':'edit_status',
        'token':getCookie('token'),
        'data':checkboxStatus
    };
    sendData("/v2/info", send, function(data, status) {
        if (status === 'success') {
            if (data.status == 200) {
                regsuc(data.data);
            } else {
                regFail(data.data);
            }
        } else {
            regFail("连接服务器失败");
        }
    });
}

function sider() {
    var inst = new mdui.Drawer('#sider');
    inst.toggle();;
}

function check_update () {
    document.getElementsByName("latest")[0].innerHTML = `Loading...`;
    $.get(
        url="https://api.github.com/repos/molanp/seaweb/releases/latest",
        function(data,status) {
            if (status=='success') {
                latest = data.name;
                document.getElementsByName("latest")[0].innerHTML = `<a href='${data.html_url}' target='_blank'>${latest}<a>`;
            }
        }
    );
}