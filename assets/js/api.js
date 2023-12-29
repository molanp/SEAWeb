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
    $.get(
        url = "/v2/info",
        data = { url: window.location.pathname, for: "api" }
    )
        .done(function (data) {
            api(data.data);
        });
    $.get(
        url = "/v2/info",
        data = { "for": "web" },
    )
        .done(function (data) {
            web(data.data);
        })
})

function web(data) {
    $("#title_bar").html(data.index_title);
    $("#version").html("Version " + data.version);
    $("#copyright").html("&copy;" + data.copyright);
    $("#record").html(data.record);
}

function api(api_data) {
    $("#api_name").html(api_data.name);
    $("#api_count").html(api_data.count);
    $("#response").html(marked.parse(api_data.response));
    $("#request").html(marked.parse(api_data.request));
    path = window.location.pathname.match(/\/docs(.*)\//)[1];
    $("#api_address").html(marked.parse(`|Method|Url|\n|--|--|\n|${api_data.method}|<a target="_blank" href="/api${path}">/api${path}</a>|`));
    $("#author").html(api_data.author);
    $("#api_version").html(api_data.version);
    $("#api_profile").html(marked.parse(api_data.profile));
    $("#urlInput").attr("value", window.location.host + "/api" + path);
}