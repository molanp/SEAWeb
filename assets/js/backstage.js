window.onload = function() {
    load();
    mdui.mutation();
}

window.addEventListener('DOMContentLoaded', showTab);
window.addEventListener("hashchange", showTab);

function showTab() {
    var hash = window.location.hash;

    // 获取tabs容器
    var tabsContainer = document.querySelector('.tabs');

    // 获取所有子元素
    var tabs = tabsContainer.children;

    // 遍历并显示/隐藏标签
    for (var i = 0; i < tabs.length; i++) {
      var tab = tabs[i];

      if (hash === '') {
        if (tab.id === 'home') {
          tab.style.display = 'block';
        } else {
          tab.style.display = 'none';
        }
      } else if ('#' + tab.id === hash) {
        tab.style.display = 'block';
      } else {
        tab.style.display = 'none';
      }
    }
    mdui.mutation();
  }

function load() {
    $.get(
        url=window.location.origin+'/v2/info',
        data= {"for":"web"},
        function(data,status) {
            if (status=='success') {
                var data = data.data;
                document.getElementById('web_info').innerHTML = `
                网站标题：<p><textarea style='width:75%;height: 200px;' id='index_title'>${data.web.index_title}</textarea></p>
                网站简介信息：<p><textarea style='width:75%;height: 200px;' id='index_description'>${data.web.index_description}</textarea></p>
                网站公告：<p><textarea style='width:75%;height: 200px;' id='notice'>${data.web.notice.data}</textarea></p>
                网站底部版权信息：<p><textarea style='width:75%;height: 200px;' id='copyright'>${data.web.copyright}</textarea></p>
                网页备案号：<p><textarea style='width:75%;height: 200px;' id='record'>${data.web.record}</textarea></p>
                友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea style='width:75%;height: 200px;' id='links'>${data.web.links}</textarea></p>
                网站keywords(逗号分隔)：<p><textarea style='width:75%;height: 200px;' id='keywords'>${data.web.keywords}</textarea></p>;`
                document.getElementById('version').innerHTML = data.version
                var setting = "";
                for (var key in data["setting"]) {
                    var value = data["setting"][key];
                    if (value == true) {
                        setting += `
                        <div class="mdui-col">${key}
                        <label class="mdui-switch">
                        <input type="checkbox" id="${key}" name="checkbox" checked>
                        <i class="mdui-switch-icon"></i>
                        </label>
                        </div>`;
                    } else {
                        setting += `
                        <div class="mdui-col">${key}
                        <label class="mdui-switch">
                        <input type="checkbox" id="${key}" name="checkbox">
                        <i class="mdui-switch-icon"></i>
                        </label>
                        </div>`;
                    }
                }
                document.getElementById('setting').innerHTML = setting;
                mdui.mutation();
            }
        }
    );
    $.get(
        url=window.location.origin+'/v2/info',
        data={"for":"status"},
        function(data,status) {
            if (status=='success') {
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
                          </label>
                        </div>
                        </td></tr>`;
                    } else {
                        list += `<tr><td>${key}</td><td>
                        <div class="mdui-col">
                        <label class="mdui-switch">
                          <input type="checkbox" id="${key}" name="checkbox">
                          <i class="mdui-switch-icon"></i>
                          </label>
                        </div>
                        </td></tr>`;
                    }
                };
                list += "</tbody></table>"
                document.getElementById('api_list').innerHTML = list;
            }
        }
    );
}

function save() {
    var setting = document.querySelectorAll('#setting [id]');
    var setting_list = [];

    for (var i = 0; i < setting.length; i++) {
        var id = setting[i].getAttribute("id");
        var status = setting[i].checked;
        var settingObj = {};
        settingObj[id] = status;
        setting_list.push(settingObj);
    }
    var send = {
        'for':'edit_web',
        'token':getCookie('token'),
        'record':document.getElementById("record").value,
        'index_title':document.getElementById("index_title").value,
        'copyright':document.getElementById("copyright").value,
        'index_description':document.getElementById("index_description").value,
        'notice':document.getElementById("notice").value,
        'keywords':document.getElementById("keywords").value,
        'links':document.getElementById("links").value,
        'setting':setting_list
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
    var checkboxes = document.querySelectorAll("#api_control [name='checkbox']");
    var checkboxStatus = {};

    for (var i = 0; i < checkboxes.length; i++) {
    var checkbox = checkboxes[i];
    checkboxStatus[checkbox.id] = checkbox.checked;
    }
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
    document.getElementById("latest_version").innerHTML = `Loading...`;
    $.get(
        url="https://api.github.com/repos/molanp/seaweb/releases/latest",
        function(data,status) {
            if (status=='success') {
                document.getElementById("latest_version").innerHTML = `<a href='${data.html_url}' target='_blank'>${data.name}<a>`;
            }
        }
    );
}