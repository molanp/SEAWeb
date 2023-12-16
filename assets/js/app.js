$(function () {
    marked.setOptions({
        gfm: true,
        tables: true,
        breaks: true,
        pedantic: false,
        sanitize: false,
        smartypants: true,
        headerIds: false
    });
    load();
})

function web(data) {
    $("#title").html(data.index_title);
    $("#title_bar").html(data.index_title);
    $("#index_description").html(DOMPurify.sanitize(marked.parse(data.index_description)));
    var link_list = "";
    var links = data.links.split(/[\r\n]+/);
    for (var i = 0; i < links.length; i++) {
        var title = links[i].match(/\[(.*?)\]/)[1];
        var link = marked.parse(links[i]).match(/\"(.*?)\"/)[1];
        link_list += `<mdui-chip href="${link}" target="_blank" elevated>${title}</mdui-chip>`;
    }
    $("#links").html(link_list);
    $("#copyright").html("&copy;" + data.copyright);
    $("#version").html("Version " + data.version);
    $("#record").html(data.record);
}

function api(data) {
    window.search = data;
    item = "";
    for (var type in data) {
        for (var name in data[type]) {
            if (data[type][name].status === "false") {
                status = `<mdui-badge style="background-color:#D80000;">维护</mdui-badge>`
            } else {
                status = `<mdui-badge style="background-color:#39C5BB;">正常</mdui-badge>`
            }
            item += `
            <mdui-card variant="outlined" target="_blank" href="docs${data[type][name].path}">
                <h3>${name}&nbsp;${status}</h3>
                <small>
                <i class="material-icons" style="font-size:12px;">equalizer</i>累计调用：${data[type][name].count}次|
                <i class="material-icons" style="font-size:12px;">folder</i>分类：${type}
                </small>
                <br>
                <div id="line-block">${marked.parse(data[type][name].api_profile)}</div>
        </mdui-card>`
        }
    }
    $("#app_api").append(item);
}

function load() {
    $.get("/v2/sitemap");
    $.get(
        url = "/v2/info",
        data = { for: "web" },
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
        url = "/v2/info",
        data={
            page: 1
        }
    )
        .done(function (data) {
            if (data.status == 200) {
                if (data.data.length != 0) {
                    $("#app_api").html("");
                    api(data.data);
                    $("#lazyload").html("<mdui-button id='2' onclick='lazyload(this)'>继续加载</mdui-button>")
                } else {
                    mdui.snackbar({
                        message: "运气不佳，加载失败了呢",
                        placement: "top",
                        closeable: true
                    })
                }
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

function lazyload(x) {
    id = parseInt(x.id ?? 0);
    x.id = id + 1;
    $.get(
        url = "/v2/info",
        data={
            page: id
        }
    )
        .done(function (data) {
            if (data.status == 200) {
                if (data.data.length != 0) {
                    api(data.data);
                } else {
                    x.style.display = "none";
                    mdui.snackbar({
                        message: "已经到顶了哦！",
                        placement: "top",
                        closeable: true
                    })
                }
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