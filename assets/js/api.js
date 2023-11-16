window.onload = function () {
    let darkMode = getCookie('theme');
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

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function enableDarkMode() {
    $('body').addClass("mdui-theme-dark");
    document.cookie = "theme=1;";
};

function disableDarkMode() {
    $('body').removeClass("mdui-theme-dark");
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

function load() {
    $.get(
        url = '/v2/api_info',
        data = { "url": window.location.pathname }
    )
        .done(function (data) {
            if (data.status == 200) {
                api(data.data);
            } else {
                mdui.dialog({
                    body: data.data,
                    actions: [
                        {
                            text: "OK"
                        }
                    ]
                });
            }
        })
        .fail(function (data) {
            mdui.dialog({
                body: data.responseJSON.data,
                actions: [
                    {
                        text: "OK"
                    }
                ]
            });
        });
    $.get(
        url = '/v2/info',
        data = { "for": "web" },
    )
        .done(function (data) {
            if (data.status == 200) {
                var data = data.data;
                web(data);
            } else {
                mdui.dialog({
                    body: data.data,
                    actions: [
                        {
                            text: "OK"
                        }
                    ]
                });
            }
        })
        .fail(function (data) {
            mdui.dialog({
                body: data.responseJSON.data,
                actions: [
                    {
                        text: "OK"
                    }
                ]
            });
        });
}

function web(data) {
    $("#title").html(data.index_title);
    $("#version").html("Version " + data.version + "<br>");
    $("#copyright").html("&copy;" + data.copyright);
    $("#record").html(data.record);
}

function api(api_data) {
    $("#api_name").html(DOMPurify.sanitize(api_data.name));
    $("#api_count").html(api_data.count);
    $("#response").html(DOMPurify.sanitize(marked.parse(api_data.response)));
    $("#request").html(DOMPurify.sanitize(marked.parse(api_data.request)));
    path = window.location.pathname.match(/\/docs(.*)\//)[1];
    $("#api_address").html(DOMPurify.sanitize(marked.parse(`|Method|Url|\n|--|--|\n|${api_data.method}|<a target='_blank' href='/api${path}'>/api${path}</a>|`)));
    $("#author").html(DOMPurify.sanitize(api_data.author));
    $("#api_version").html(DOMPurify.sanitize(api_data.version));
    $("#api_profile").html(DOMPurify.sanitize(marked.parse(api_data.profile)));
    $("#urlInput").attr('value', window.location.host + '/api' + path);
}