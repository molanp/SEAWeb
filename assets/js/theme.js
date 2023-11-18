$(function () {
    mdui.setColorScheme("#39c5bc");
    if (getCookie("theme") == 1) theme_dark();
});

window
    .matchMedia("(prefers-color-scheme: dark)")
    .addListener(e => (e.matches ? theme_dark() : theme_light()))

function theme_light() {
    deleteCookie("theme");
    mdui.setTheme("light");
    $("#theme").attr("icon", "light_mode--outlined");
}

function theme_dark() {
    setCookie("theme", 1);
    mdui.setTheme("dark");
    $("#theme").attr("icon", "dark_mode--outlined");
}

function theme_auto() {
    deleteCookie("theme");
    mdui.setTheme("auto");
    $("#theme").attr("icon", "light_mode--outlined");
}