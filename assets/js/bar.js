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
    <mdui-top-app-bar-title>
        <span id="title_bar" onclick="javascript:window.location.href='/'">title</span>
    </mdui-top-app-bar-title>
    <div style="flex-grow: 1"></div>
    <mdui-button-icon href="javascript:output_search()" icon="search"></mdui-button-icon>
    <mdui-button-icon href="/rank.html" icon="equalizer"></mdui-button-icon>
    <mdui-button-icon mdui-tooltip="{content: "公告", position: "bottom"}" onclick="notice()" icon="announcement--outlined"></mdui-button-icon>
    <mdui-button-icon href="javascript:theme_menu()" icon="color_lens--outlined"></mdui-button-icon>
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

$(document).ready(function () {
    $('body').append('<mdui-fab id="ToTop" icon="vertical_align_top"></mdui-fab>');
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#ToTop').fadeIn();
        } else {
            $('#ToTop').fadeOut();
        }
    });
    $('#ToTop').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 800);
        return false;
    });
});


function notice() {
    $.get("/v2/notice")
        .done(function (data) {
                mdui.dialog({
                    headline: "公告 | " + data.data.time,
                    body: marked.parse(data.data.notice),
                    actions: [
                        {
                            text: "OK",
                        }
                    ]
                });
        });
}