window.onload = function() {
    $.get(
        url='/v2/info',
        data={
        "for":"setting",
        'apikey':getCookie('token')
        },
        function(data,status) {
            if (status=='success'&&data.status==200) {
                load();
                window.inst = new mdui.Drawer('#sider');
                mdui.mutation();
                RankList();
                TrendChart();
            } else {
                alert("身份验证失败");
                document.body.innerHTML = '';
                document.cookie=`token=; expires=Thu, 01 Jan 1970 00:00:00 GMT";`;
                document.cookie=`user=; expires=Thu, 01 Jan 1970 00:00:00 GMT";`;
                location.reload();
                throw new Error("非法访问");
            }
        })
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
        url='/v2/info',
        data= {"for":"web"},
        function(data,status) {
            if (status=='success') {
                var data = data.data;
                $('#web_info').html(`
                网站标题：<p><textarea style='width:75%;height: 200px;' id='index_title'>${data.index_title}</textarea></p>
                网站简介信息：<p><textarea style='width:75%;height: 200px;' id='index_description'>${data.index_description}</textarea></p>
                网站公告：<p><textarea style='width:75%;height: 200px;' id='notice'>${data.notice.data}</textarea></p>
                网站底部版权信息：<p><textarea style='width:75%;height: 200px;' id='copyright'>${data.copyright}</textarea></p>
                网页备案号：<p><textarea style='width:75%;height: 200px;' id='record'>${data.record}</textarea></p>
                友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea style='width:75%;height: 200px;' id='links'>${data.links}</textarea></p>
                网站keywords(逗号分隔)：<p><textarea style='width:75%;height: 200px;' id='keywords'>${data.keywords}</textarea></p>`);
                $('#version').html(data.version);
            }
        }
    );
    $.get(
        url='/v2/info',
        data={"for":"status"},
        function(data,status) {
            if (status=='success') {
                var data = data.data;
                var list = `<table class="mdui-table mdui-table-hoverable mdui-table-fluid"><thead>
                <tr><th>API Name</th>
                <th>Status</th>
                </tr></thead><tbody>`;
                for(var key in data) {
                    if (data[key]=="true") {
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
                $('#api_list').html(list);
            }
        }
    );
    $.get(
        url='/v2/info',
        data={
        "for":"setting",
        'apikey':getCookie('token')
        },
        function(data,status) {
            if (status=='success') {
                var setting = "";
                var data = data.data;
                for (var key in data) {
                    var value = data[key][0];
                    if (value === "true") {
                        setting += `
                        <div class="mdui-col">${key}<i class="mdui-icon material-icons" mdui-tooltip="{content: '${data[key][1]}', position: 'top'}">info_outline</i>
                        <label class="mdui-switch">
                        <input type="checkbox" id="${key}" name="checkbox" checked>
                        <i class="mdui-switch-icon"></i>
                        </label>
                        </div>`;
                    } else if (value === "false"){
                        setting += `
                        <div class="mdui-col">${key}<i class="mdui-icon material-icons" mdui-tooltip="{content: '${data[key][1]}', position: 'top'}">info_outline</i>
                        <label class="mdui-switch">
                        <input type="checkbox" id="${key}" name="checkbox">
                        <i class="mdui-switch-icon"></i>
                        </label>
                        </div>`;
                    }
                }
                $('#setting').html(setting);
            }
        }
    )
}

function save(mode) {
    if (mode == "setting") {
      var setting_list = [];
      $('#setting [id]').each(function() {
        var id = $(this).attr('id');
        var status = $(this).prop('checked');
        var settingObj = {};
        settingObj[id] = status;
        setting_list.push(settingObj);
      });
      var send = {
        'for': 'setting',
        'apikey': getCookie('token'),
        'data': setting_list
      };
    } else if (mode == "web") {
      var send = {
        'for': 'web',
        'apikey': getCookie('token'),
        'record': $('#record').val(),
        'index_title': $('#index_title').val(),
        'copyright': $('#copyright').val(),
        'index_description': $('#index_description').val(),
        'notice': $('#notice').val(),
        'keywords': $('#keywords').val(),
        'links': $('#links').val()
      };
    } else if (mode == "status") {
      var checkboxStatus = {};
      $('#api_control [name="checkbox"]').each(function() {
        var checkbox = $(this);
        checkboxStatus[checkbox.attr('id')] = checkbox.prop('checked');
      });
      var send = {
        'for': 'status',
        'apikey': getCookie('token'),
        'data': checkboxStatus
      };
    }
    sendData("/v2/edit", send, function(data, status) {
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
    window.inst.toggle();
}

function check_update() {
    $("#latest_version").html("Loading...");
    $.ajax({
        url: "https://api.github.com/repos/molanp/seaweb/releases/latest",
        method: "GET",
        success: function(data, status) {
            if (status === "success") {
                $("#latest_version").html(`<a href="${data.html_url}" target="_blank">${data.name}(点击查看)</a>`);
                $("#update_info").html(data.body);
            }
        },
        error: function() {
            $("#latest_version").html("Error loading latest version.");
        }
    });
}


function resetpassword() {
    var content = `
        <div class="form">
            <div class="mdui-textfield">
                <input id="new" class="mdui-textfield-input" type="password" placeholder="新的密码" required />
                <div class="mdui-textfield-error">密码不能为空</div>
            </div>
            <div class="mdui-textfield">
                <input id="again" class="mdui-textfield-input" type="password" placeholder="再输一次" required />
                <div class="mdui-textfield-error">密码不能为空</div>
            </div>
        </div>
    `;

    mdui.dialog({
        title: '修改密码',
        content: content,
        buttons: [{
            text: '提交',
            onClick: function(inst){
                var newPassword = $('#new').val();
                var newPasswordAgain = $('#again').val();

                if (newPassword === '' || newPasswordAgain === '') {
                    mdui.alert('密码不能为空');
                    return;
                }

                sendData('/v2/login', {
                    'type': 'pass',
                    'token': getCookie("token"),
                    'new': newPassword,
                    'again': newPasswordAgain
                }, function(data, status) {
                    if (status === 'success') {
                        if (data.status === 200) {
                            mdui.dialog({
                                content: data.data,
                                buttons: [{
                                    text: 'OK',
                                    onClick: function(inst){
                                        loginout();
                                    }
                                }]
                            });
                        } else {
                            regFail(data.data);
                        }
                    } else {
                        regFail("连接服务器失败");
                    }
                });
            }
        }]
    });
}

function loginout() {
    document.cookie=`token=; expires=Thu, 01 Jan 1970 00:00:00 GMT";`;
    document.cookie=`user=; expires=Thu, 01 Jan 1970 00:00:00 GMT";`;
    location.reload();
}

function RankList() {
    $.get(url='/v2/hot')
    .done(function(data,status) {
        if (data.status==200) {
            ShowRankList(data.data);
        } else {
            alert(JSON.stringify(data.data));
        }
    })
}

function TrendChart() {
    $.get(url='/v2/hot',data={apikey: getCookie('token')})
    .done(function(data,status) {
        if (data.status==200) {
            ShowTrendChart(data.data);
        } else {
            alert(JSON.stringify(data.data));
        }
    })
}

function ShowRankList(data) {
    $('#api-rank-list').html('');
    rankData = [];
    for (var item in data) {
        rankData.push({api: data[item]["name"], count: data[item]["count"], url: data[item]["url"]});
    }
    rankData.forEach(function(data) {
      var itemHtml = '<li class="mdui-list-item">' +
                       `<div class="mdui-list-item-content"><a href="${data.url.replace(/\/api/g, "")}" target="_blank">` + data.api + '</a></div>' +
                       '<div class="mdui-list-item-text">' + data.count + '次调用</div>' +
                     '</li>';
      $('#api-rank-list').append(itemHtml);
    });
}

function ShowTrendChart(data) {
    $('#api-trend-chart').html('');
    var trendData = [];
    var dates = [];
    var values = [];
    for (var item in data) {
        trendData.push({date: data[item]["date"], value: data[item]["count"]});
    }
    trendData.forEach(function(data) {
      dates.push(data.date);
      values.push(data.value);
    });
    
    Highcharts.chart('api-trend-chart', {
      title: {
        text: ''
      },
      xAxis: {
        categories: dates
      },
      yAxis: {
        title: {
          text: 'API调用次数'
        }
      },
      series: [{
        name: 'API调用次数',
        data: values
      }]
    });
}