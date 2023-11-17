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

window
    .matchMedia("(prefers-color-scheme: dark)")
    .addListener(e => (e.matches ? enableDarkMode() : disableDarkMode()))

function web(data) {
    $(".title").html(data.index_title);
    $("#index_description").html(DOMPurify.sanitize(marked.parse(data.index_description)));;
    var link_list = '';
    var links = data.links.split(/[\r\n]+/);
    for (var i = 0; i < links.length; i++) {
        var title = links[i].match(/\[(.*?)\]/)[1];
        var link = marked.parse(links[i]).match(/\"(.*?)\"/)[1];
        link_list += `<mdui-chip href="${link}" target="_blank" elevated>${title}</mdui-chip>`;
    }
    $("#links").html(link_list);;
    $("#version").html("Version " + data.version + "<br>");;
    $("#copyright").html("&copy;" + data.copyright);;
    $("#record").html(data.record);;
}

function api(data) {
    window.search = data;
    item = '';
    for (var type in data) {
        for (var name in data[type]) {
            if (data[type][name].status === 'false') {
                status = `<mdui-badge style="background-color:#D80000;">维护</mdui-badge>`
            } else {
                status = `<mdui-badge style="background-color:#39C5BB;">正常</mdui-badge>`
            }
            item += `
            <mdui-card class="item" target="_blank" href="docs${data[type][name].path}">
                <h3>${name}${status}</h3>
                <small>
                <i class="material-icons" style="font-size:12px;">equalizer</i>累计调用：${data[type][name].count}次|
                <i class="material-icons" style="font-size:12px;">folder</i>分类：${type}
                </small>
                <br>
                <p>${marked.parse(data[type][name].api_profile)}</p>
        </mdui-card>`
        }
    }
    $("#app_api").html(item);
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
        url = '/v2/info'
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
}
