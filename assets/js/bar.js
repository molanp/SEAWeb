var scripts = [
    "/assets/js/theme.js",
    "/assets/js/search.js"
];
var csses = [
    "/assets/css/style.css",
    "/assets/css/mark.css",
    "https://fonts.googleapis.com/icon?family=Material+Icons+Outlined",
    "https://fonts.googleapis.com/icon?family=Material+Icons"
];
for (var i = 0; i < scripts.length; i++) {
    var script = document.createElement("script");
    script.src = scripts[i];
    document.head.appendChild(script);
}
for (var i = 0; i < csses.length; i++) {
    var link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = csses[i];
    document.head.appendChild(link);
}

$(function () {
    try {
        $("#bar").html(`
    <mdui-top-app-bar-title style="display: inline-block;">
        <span id="title_bar" onclick="window.location.href="/"">title</span>
    </mdui-top-app-bar-title>
    <div style="flex-grow: 1"></div>
    <mdui-button-icon href="javascript:output_search()" icon="search"></mdui-button-icon>
    <mdui-button-icon href="/page/rank.html" icon="equalizer"></mdui-button-icon>
    <mdui-button-icon mdui-tooltip="{content: "公告", position: "bottom"}" onclick="notice()" icon="announcement--outlined"></mdui-button-icon>
    <mdui-dropdown>
        <mdui-button-icon slot="trigger" icon="light_mode--outlined" id="theme"></mdui-button-icon>
        <mdui-menu selects="single" value="auto">
            <mdui-menu-item href="javascript:theme_light()" value="light">亮色模式</mdui-menu-item>
            <mdui-menu-item href="javascript:theme_dark()" value="dark">暗色模式</mdui-menu-item>
            <mdui-divider></mdui-divider>
            <mdui-menu-item href="javascript:theme_auto()" value="auto">跟随系统</mdui-menu-item>
        </mdui-menu>
    </mdui-dropdown>
    <mdui-dropdown>
        <mdui-button-icon slot="trigger" icon="more_vert"></mdui-button-icon>
        <mdui-menu>
            <mdui-menu-item>
                <mdui-button href="/sw-ad" icon="person">登录</mdui-button>
            </mdui-menu-item>
            <mdui-menu-item id="version"></mdui-menu-item>
        </mdui-menu>
    </mdui-dropdown>`);
    } catch (e) {
        console.error(e);
    }
})

function notice() {
    $.get("/v2/notice")
    .done(function(data) {
        if (data.status==200) {
            data = data.data;
            mdui.dialog({
                headline: "公告 | "+data.time,
                body: marked.parse(data.notice),
                actions: [
                    {
                      text: "OK",
                    }
                ]
            });
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data){
        alert(data.responseJSON.data)
    });
}