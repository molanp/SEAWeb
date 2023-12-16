$(function () {
  $.post(
    url = '/v2/info',
    data = {
      for: "setting",
      token: getCookie('token')
    },
    function (data, status) {
      if (status == 'success' && data.status == 200) {
        load();
        RankList();
        TrendChart();
      } else {
        deleteCookie("user");
        deleteCookie("token");
        location.reload();
      }
    })
})

$(document).ready(function () {
  showTab();
});

$(window).on("hashchange", function () {
  showTab();
});


function showTab() {
  var hash = window.location.hash;
  var tabsContainer = document.querySelector('.tabs');
  var tabs = tabsContainer.children;
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
    // 修复样式
    $('.tabs').children().each(function () {
      var $element = $(this);
      if ($element.css('display') === 'block') {
        $element.css('display', '');
        // $element.css('display', 'none');
      }
    });
}

function load() {
  $.get(
    url = '/v2/info',
    data = { "for": "web" },
    function (data, status) {
      if (status == 'success') {
        var data = data.data;
        $('#web_info').html(`
        <mdui-text-field autosize label="网站标题" value="${data.index_title}" id="index_title"></mdui-text-field>
        <hr>
        <a href="javascript:preview('index_description')">预览简介</a>
        <mdui-text-field autosize label="网站简介" value="${data.index_description}" id="index_description"></mdui-text-field>
        <hr>
        <a href="javascript:preview('notice')">预览公告</a>
        <mdui-text-field autosize label="网站公告" value="${data.notice.data}" id="notice"></mdui-text-field>
        <hr>
        <mdui-text-field autosize label="网站底部版权信息" value="${data.copyright}" id="copyright"></mdui-text-field>
        <hr>
        <mdui-text-field autosize label="网页备案号" value="${data.record}" id="record"></mdui-text-field>
        <hr>
        <mdui-text-field autosize label="友情链接" helper="例如[链接1](http://xxx)，一行一个" value="${data.links}" id="links"></mdui-text-field>
        <hr>
        <mdui-text-field autosize label="网站关键词" helper="英文逗号分隔" value="${data.keywords}" id="keywords"></mdui-text-field>`);
        $('#version').html(data.version);
      }
    }
  );
  $.get(
    url = '/v2/info',
    data = { "for": "status" },
    function (data, status) {
      if (status == 'success') {
        var data = data.data;
        var list = `<table><thead>
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
        list += "</tbody></table>"
        $('#api_list').html(list);
      }
    }
  );
  $.post(
    url = '/v2/info',
    data = {
      for: "setting",
      token: getCookie('token')
    },
    function (data, status) {
      if (status == 'success') {
        var setting = `<table><thead>
        <tr><th>Name</th>
        <th>Description</th>
        <th><mdui-button onclick="up_sys()">更新设置列表</mdui-button></th>
        </tr></thead><tbody>`;
        var data = data.data;
        for (var key in data) {
          var value = data[key][0];
          if (value === "true") {
            setting += `
            <tr><td>${key}</td>
            <td>${data[key][1]}</td>
            <td>
              <mdui-switch id="${key}" name="checkbox" checked></mdui-switch>
            </td></tr>`;
          } else if (value === "false") {
            setting += `
            <tr><td>${key}</td>
            <td>${data[key][1]}</td>
            <td>
              <mdui-switch id="${key}" name="checkbox"></mdui-switch>
            </td></tr>`;
          }
        }
        $('#options').html(setting);
      }
    }
  )
}

function save() {
  i = 0;
  switch (window.location.hash) {
    case "#settings":
      var setting_list = [];
      $('#setting [id]').each(function () {
        var id = $(this).attr('id');
        var status = $(this).prop('checked');
        var settingObj = {};
        settingObj[id] = status;
        setting_list.push(settingObj);
      });
      var send = {
        for: 'setting',
        data: setting_list
      };
      i = 1;
      break;
    case "#web":
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
    case "#api_control":
      var checkboxStatus = {};
      $('#api_list [name="checkbox"]').each(function () {
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

function up_sys() {
  sendData("/v2/info",
    {
      for: 'update'
    },
    function (responseData) {
      message(responseData.data);
    });
}

function preview(id) {
  content = marked.parse($("#" + id).val());
  message(content, "预览内容");
}