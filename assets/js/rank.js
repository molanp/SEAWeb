window.onload = function () {
    let darkMode = sessionStorage.theme;
    if (darkMode == 1) enableDarkMode();

    marked.setOptions({
        gfm: true,//默认为true。 允许 Git Hub标准的markdown.
        tables: true,//默认为true。 允许支持表格语法。该选项要求 gfm 为true。
        breaks: true,//默认为false。 允许回车换行。该选项要求 gfm 为true。
        pedantic: false,//默认为false。 尽可能地兼容 markdown.pl的晦涩部分。不纠正原始模型任何的不良行为和错误。
        sanitize: false,//对输出进行过滤（清理）
        smartLists: true,
        smartypants: false,//使用更为时髦的标点，比如在引用语法中加入破折号。
        mangle: false,//因warning禁用
        headerIds: false//因warning禁用
    });
    load();
}

function enableDarkMode() {
    $('body').addClass("mdui-theme-layout-dark");
    document.cookie = "theme=1;";
};

function disableDarkMode() {
    $('body').removeClass("mdui-theme-layout-dark");
    document.cookie = `theme=0;`;
};

function changeTheme() {
    darkMode = getCookie('theme');
    if (darkMode == 1) {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
};

window
    .matchMedia("(prefers-color-scheme: dark)")
    .addListener(e => (e.matches ? enableDarkMode() : disableDarkMode()))

function web(data) {
    $("#title").html(data.index_title);
    $("#version").html("Version " + data.version + "<br/>");
    $("#copyright").html("&copy;" + data.copyright);
    $("#record").html(data.record);
}

function rank(data) {
    let table = "";
    let num = 0;

    Object.keys(data).forEach((item, index) => {
        num++;
        table += `
        <tr>
          <td>${num}</td>
          <td><a href="/docs${data[item].url.replace(/\/api/, "")}">${data[item].name}</a></td>
        <td>${data[item].count}</td>
        </tr>
      `;
    });

    if (!table) {
        table = `
        <tr>
          <td>1</td>
          <td>无数据</td>
          <td>N/A</td>
        </tr>
      `;
    }

    $("#rank").html(table);
}



function load() {
    $.get(
        url = '/v2/info',
        data = { "for": "web" },
    )
        .done(function (data) {
            if (data.status == 200) {
                web(data.data);
            } else {
                alert(JSON.stringify(data.data));
            }
        })
        .fail(function (data) {
            alert(data.responseJSON.data)
        });
    $.get(
        url = '/v2/hot',
    )
        .done(function (data) {
            if (data.status == 200) {
                rank(data.data);
            } else {
                alert(JSON.stringify(data.data));
            }
        })
        .fail(function (data) {
            alert(data.responseJSON.data)
        });
}