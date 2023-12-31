$(function () {
    mdui.setColorScheme("#39c5bc");
    if (cookie.get("theme") == 1) theme_dark();
});

window
    .matchMedia("(prefers-color-scheme: dark)")
    .addListener(e => (e.matches ? theme_dark() : theme_light()))

function theme_light() {
    cookie.remove("theme");
    mdui.setTheme("light");
    $("#theme_select").val("light");
    $("#theme").attr("icon", "light_mode--outlined");
}

function theme_dark() {
    cookie.set("theme", 1);
    mdui.setTheme("dark");
    $("#theme_select").val("dark");
    $("#theme").attr("icon", "dark_mode--outlined");
}

function theme_auto() {
    cookie.remove("theme");
    mdui.setTheme("auto");
    $("#theme_select").val("auto");
    $("#theme").attr("icon", "light_mode--outlined");
}

$.ajaxSetup({
    statusCode: {
        '*': function (data) {
            mdui.dialog({
                body: data.responseJSON ? data.responseJSON.data : data.responseText,
                actions: [
                    {
                        text: "OK"
                    }
                ]
            });
        }
    }
});

function message(data, title = "") {
    mdui.dialog({
        headline: title,
        body: data,
        actions: [{
            text: "确定",
        }]
    });
}