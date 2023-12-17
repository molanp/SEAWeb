$(function () {
  load();
})

function load() {
  var url = window.location.pathname;
  if (url.indexOf("/sw-ad/") === 0) {
    var url = url.replace("/sw-ad/", "");
  }
  switch (url) {
    case '':
      $("#data").html(`<div class="grid">
        <mdui-card variant="outlined">
            功能区
            <br>
            <mdui-button onclick="sendData('/v2/auth/cache',{},function (responseData) {message(responseData.data);});">清理缓存</mdui-button>
            <mdui-button onclick="sendData('/v2/info',{for: 'update'},function (responseData) {message(responseData.data);});">更新设置列表</mdui-button>
        </mdui-card>
        <mdui-card variant="outlined">
            本地SEAWeb版本: <span id="version">
                <mdui-chip loading></mdui-chip>
            </span>
            <br>
            网络版本:<span id="latest_version"><a href="javascript:check_update()">检查更新</a></span>
            <mdui-text-field readonly autosize label="更新内容" id="update_info"></mdui-text-field>
        </mdui-card>
        <mdui-card variant="outlined">
            <mdui-list-subheader>调用排行</mdui-list-subheader>
            <mdui-list id="api-rank-list">
                <br>
                <mdui-circular-progress></mdui-circular-progress>
            </mdui-list>
        </mdui-card>
        <mdui-card variant="outlined" style="overflow-x: auto;white-space: nowrap;">
            调用统计
            <canvas id="api-trend-chart">
                <br>
                <mdui-circular-progress></mdui-circular-progress>
            </canvas>
        </mdui-card>
      </div>`);
      $.get(
        url = '/v2/info',
        data = { "for": "web" },
        function (data, status) {
          if (status == 'success') {
            $('#version').html(data.data.version);
          }
        })
      RankList();
      TrendChart();
      break;
    case 'log':
      sendData("/v2/auth/log", {mode: 'log'}, function (data) {
        var dataList = data.data;
        var list = `<div class="mdui-table">
                        <table>
                          <thead>
                            <tr>
                              <th>时间</th>
                              <th>级别</th>
                              <th>内容</th>
                            </tr>
                          </thead>
                          <tbody>`;
        $.each(dataList, function (index, item) {
          if (item.level === 'INFO') {
            color = 'green';
          } else if (item.level === 'WARN') {
            color = 'orange';
          } else if (item.level === 'ERROR') {
            color = 'red';
          } else if (item.level === 'DEBUG') {
            color = 'blue';
          } else {
            color = 'black';
          }
          list += `<tr style="color: ${color};">
                       <td>${item.time}</td>
                       <td>${item.level}</td>
                       <td>${item.content}</td>
                     </tr>`;
        });
        list += "</tbody></table></div>"
        $('#data').html(list);
      });
      break;
    case 'web':
      $.get(
        url = '/v2/info',
        data = { "for": "web" },
        function (data, status) {
          if (status == 'success') {
            var data = data.data;
            $('#data').html(`
            <mdui-text-field autosize label="网站标题" value="${data.index_title}" id="index_title"></mdui-text-field>
            <hr>
            <a href="javascript:preview('index_description')">预览简介</a>
            <mdui-text-field autosize label="网站简介" value="${data.index_description}" id="index_description"></mdui-text-field>
            <hr>
            <a href="javascript:preview('notice')">预览公告</a>
            <mdui-text-field autosize label="网站公告" value="${data.notice}" id="notice"></mdui-text-field>
            <hr>
            <mdui-text-field autosize label="网站底部版权信息" value="${data.copyright}" id="copyright"></mdui-text-field>
            <hr>
            <mdui-text-field autosize label="网页备案号" value="${data.record}" id="record"></mdui-text-field>
            <hr>
            <mdui-text-field autosize label="友情链接" helper="例如[链接1](http://xxx)，一行一个" value="${data.links}" id="links"></mdui-text-field>
            <hr>
            <mdui-text-field autosize label="网站关键词" helper="英文逗号分隔" value="${data.keywords}" id="keywords"></mdui-text-field>`);
          }
        }
      );
      break;
    case 'api':
      $.get(
        url = '/v2/info',
        data = { "for": "status" },
        function (data, status) {
          if (status == 'success') {
            var data = data.data;
            var list = `<div class="mdui-table">
            <table><thead>
            <tr><th>API Name</th>
            <th>Status</th>
            </tr></thead><tbody>`;
            for (var key in data) {
              if (data[key] == "true") {
                list += `<tr><td>${key}</td><td>
                  <mdui-switch id="${key}" name="checkbox" checked></mdui-switch>
                </td></tr>`;
              } else {
                list += `<tr><td>${key}</td><td>
                  <mdui-switch id="${key}" name="checkbox"></mdui-switch>
                </td></tr>`;
              }
            };
            list += "</tbody></table></div>"
            $('#data').html(list);
          }
        }
      );
      break;
    case 'settings':
      $.post(
        url = '/v2/info',
        data = {
          for: "setting",
          token: getCookie('token')
        },
        function (data, status) {
          if (status == 'success') {
            var setting = `<div class="mdui-table">
            <table><thead>
            <tr><th>Name</th>
            <th>Description</th>
            <th></th>
            </tr></thead><tbody>`;
            var data = data.data;
            for (var key in data) {
              var value = data[key][0];
              if (value === "true") {
                setting += `
                <tr><td>${key}</td>
                <td>${data[key][1]}</td>
                <td>
                <mdui-switch id="${key}" checked></mdui-switch>
                </td></tr>`;
              } else if (value === "false") {
                setting += `
                <tr><td>${key}</td>
                <td>${data[key][1]}</td>
                <td>
                <mdui-switch id="${key}"></mdui-switch>
                </td></tr>`;
              } else {
                setting += `
                <tr><td>${key}</td>
                <td>${data[key][1]}</td>
                <td>
                <mdui-text-field id="${key}" value="${value}"></mdui-text-field>
                </td></tr>`;
              }
            }
            setting += `</tbody></table></div>`;
            $('#data').html(setting);
          }
        }
      )
      break;
    default:
      break;
  }
}

function save() {
  i = 0;
  var url = window.location.pathname;
  if (url.indexOf("/sw-ad/") === 0) {
    var url = url.replace("/sw-ad/", "");
  }
  console.log(url)
  switch (url) {
    case "settings":
      var setting_list = [];
      $('#data [id]').each(function () {
        var id = $(this).attr('id');
        if ($(this).is('mdui-switch')) {
          var value = $(this).prop('checked');
        } else {
          var value = $(this).val();
        }
        var settingObj = {};
        settingObj[id] = value;
        setting_list.push(settingObj);
      });
      var send = {
        for: 'setting',
        data: setting_list
      };
      i = 1;
      break;
    case "web":
      var send = {
        for: 'web',
        record: $('#record').val(),
        index_title: $('#index_title').val(),
        copyright: $('#copyright').val(),
        index_description: $('#index_description').val(),
        notice: $('#notice').val(),
        keywords: $('#keywords').val(),
        links: $('#links').val()
      };
      i = 1;
      break;
    case "api":
      var checkboxStatus = {};
      $('#data [name="checkbox"]').each(function () {
        var checkbox = $(this);
        checkboxStatus[checkbox.attr('id')] = checkbox.prop('checked');
      });
      var send = {
        for: 'status',
        data: checkboxStatus
      };
      i = 1;
      break;
    default:
      message("无需操作");
  }
  if (i == 1) {
    sendData("/v2/edit", send, function (data) {
      if (data.status == 200) {
        message(data.data);
      } else {
        message(data.data);
      }
    });
  }
}

function check_update() {
  $("#latest_version").html("<mdui-chip loading></mdui-chip>");
  $.ajax({
    url: "https://api.github.com/repos/molanp/seaweb/releases/latest",
    method: "GET",
    success: function (data, status) {
      if (status === "success") {
        $("#latest_version").html(`<a href="${data.html_url}" target="_blank">${data.name}(点击查看)</a>`);
        $("#update_info").val(data.body);
      }
    },
    error: function () {
      $("#latest_version").html("Error loading latest version.");
    }
  });
}

function resetpassword() {
  var content = `
    <mdui-text-field clearable label="新的密码" id="new"></mdui-text-field>
    <mdui-text-field clearable label="再输一次" id="again"></mdui-text-field>`;

  mdui.dialog({
    headline: '修改密码',
    body: content,
    actions: [{
      text: '取消'
    },
    {
      text: '确定',
      onClick: function () {
        var newPassword = $('#new').val();
        var newPasswordAgain = $('#again').val();
        if (newPassword === '' || newPasswordAgain === '') {
          message('密码不能为空');
          return;
        }

        sendData('/v2/auth/login', {
          'type': 'pass',
          'token': getCookie("token"),
          'new': newPassword,
          'again': newPasswordAgain
        }, function (data) {
          if (data.status === 200) {
            mdui.dialog({
              body: data.data,
              actions: [{
                text: 'OK',
                onClick: function (inst) {
                  loginout();
                }
              }]
            });
          } else {
            message(data.data);
          }
        });
      }
    }]
  });
}

function loginout() {
  sendData('/v2/auth/logout',
    { "token": getCookie("token") },
    function () { })
  deleteCookie("user");
  deleteCookie("token");
  location.reload();
}

function RankList() {
  $.get(url = '/v2/hot')
    .done(function (data) {
      if (data.status == 200) {
        ShowRankList(data.data);
      } else {
        message(data.data);
      }
    })
}

function TrendChart() {
  $.get(url = '/v2/hot', data = { token: getCookie('token') })
    .done(function (data) {
      if (data.status == 200) {
        ShowTrendChart(data.data);
      } else {
        message(data.data);
      }
    })
}

function ShowRankList(data) {
  $('#api-rank-list').html('');
  rankData = [];
  items = [];
  for (var item in data) {
    rankData.push({ api: data[item]["name"], count: data[item]["count"], url: data[item]["url"] });
  }
  rankData.forEach(function (data) {
    items.push(`<mdui-list-item href="/docs${data.url.replace(/\/api/g, "")}">${data.api}(${data.count}次调用)</mdui-list-item>`);
  });
  $('#api-rank-list').html(items);
}

function ShowTrendChart(data) {
  var dates = [];
  var values = [];
  for (var item in data) {
    dates.push(data[item]["date"]);
    values.push(data[item]["count"]);
  }

  var ctx = document.getElementById('api-trend-chart').getContext('2d');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: dates,
      datasets: [{
        label: 'API调用次数',
        data: values,
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(0, 0, 0, 0)'
      }]
    },
    options: {
      responsive: false
    }
  });
}

function preview(id) {
  content = marked.parse($("#" + id).val());
  message(content, "预览内容");
}