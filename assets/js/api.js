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

function load() {
    $.get(
        url = "/v2/info",
        data = { url: window.location.pathname, for: "api" }
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
        url = "/v2/info",
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
    $("#title_bar").html(data.index_title);
    $("#version").html("Version " + data.version);
    $("#copyright").html("&copy;" + data.copyright);
    $("#record").html(data.record);
}

function api(api_data) {
    $("#api_name").html(DOMPurify.sanitize(api_data.name));
    $("#api_count").html(api_data.count);
    $("#response").html(DOMPurify.sanitize(marked.parse(api_data.response)));
    $("#request").html(DOMPurify.sanitize(marked.parse(api_data.request)));
    path = window.location.pathname.match(/\/docs(.*)\//)[1];
    $("#api_address").html(DOMPurify.sanitize(marked.parse(`|Method|Url|\n|--|--|\n|${api_data.method}|<a target="_blank" href="/api${path}">/api${path}</a>|`)));
    $("#author").html(DOMPurify.sanitize(api_data.author));
    $("#api_version").html(DOMPurify.sanitize(api_data.version));
    $("#api_profile").html(DOMPurify.sanitize(marked.parse(api_data.profile)));
    $("#urlInput").attr("value", window.location.host + "/api" + path);
}