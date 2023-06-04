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
        console.log("切换到home");
    } else if(hash=='#status') {
        load_status();
        console.log("切换到api管理");
    }
}
function save_api() {
    var checkboxes = document.getElementsByClassName('checkbox');
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
                content.innerHTML = `
                <h3>修改api状态</h3>
                <br>
                <div class="api_list"></div>
                <button onclick='save_api()'
                class='login-button mdui-btn mdui-btn-raised mdui-ripple'>保存</button>`;
                for(key in data) {
                    if (data[key]==true) {
                        list += `<tr><td>${key}</td><td>
                        <input type="checkbox" id="${key}" class="checkbox" checked>
                        </td></tr>`;
                    } else {
                        list += `<tr><td>${key}</td><td>
                        <input type="checkbox" id="${key}" class="checkbox">
                        </td></tr>`;
                    }
                };
                list += "</tbody></table>"
                var api_list = document.getElementsByClassName('api_list')[0];
                api_list.innerHTML = list;
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
                content.innerHTML = `
                <blockquote>SEAWeb版本:<span name="version"></span>(最新版本:<span name="latest"></span>)</blockquote>
                <h3>修改网页信息</h3>
                <br>
                网站标题：<p><textarea id='editor' name='index_title'>正在加载...</textarea></p>
                网站简介信息：<p><textarea id='editor' name='index_description'>正在加载...</textarea></p>
                网站公告：<p><textarea id='editor' name='notice'>正在加载...</textarea></p>
                网站底部版权信息：<p><textarea id='editor' name='copyright'>正在加载...</textarea></p>
                网页备案号：<p><textarea id='editor' name='record'>正在加载...</textarea></p>
                友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea id='editor' name='links'>正在加载...</textarea></p>
                网站keywords(逗号分隔)：<p><textarea id='editor' name='keywords'>正在加载...</textarea></p>
                <button onclick='save()'
                class='login-button mdui-btn mdui-btn-raised mdui-ripple'>保存</button>`
                var data = data.data;
                document.title = data.index_title + '-后台管理';
                document.getElementsByName("index_title")[0].value = data.index_title;
                document.getElementsByName("index_description")[0].value = data.index_description;
                document.getElementsByName("notice")[0].innerHTML = data.notice.data;
                document.getElementsByName("copyright")[0].value = data.copyright;
                document.getElementsByName("record")[0].value = data.record;
                document.getElementsByName("links")[0].value = data.links;
                document.getElementsByName("keywords")[0].value = data.keywords;
                document.getElementsByName("version")[0].innerHTML = data.version;
            }
        }
    );
    $.get(
        url="https://api.github.com/repos/molanp/seaweb/releases/latest",
        function(data,status) {
            if (status=='success') {
                latest = data.name;
                latest = latest.match(/v(\w+)/)[1];
                document.getElementsByName("latest")[0].innerHTML = `<a href='${data.html_url}' target='_blank'>${latest}<a>`;
            }
        }
    )
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