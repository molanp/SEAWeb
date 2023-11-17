$(function () {
    mdui.setColorScheme('#39c5bc');
    if (getCookie('theme') == 1) mdui.setTheme('dark');
});



window
    .matchMedia("(prefers-color-scheme: dark)")
    .addListener(e => (e.matches ? mdui.setTheme('dark') : mdui.setTheme('light')))

function changeTheme() {
    darkMode = getCookie('theme');
    if (darkMode == 1) {
        deleteCookie("theme");
        mdui.setTheme('light');
    } else {
        mdui.setTheme('dark');
        setCookie("theme", 1)
    }
};