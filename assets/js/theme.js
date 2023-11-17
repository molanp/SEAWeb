function enableDarkMode() {
    mdui.$('body').addClass("mdui-theme-dark");
    setCookie("theme", 1, 0);
};

function disableDarkMode() {
    mdui.$('body').removeClass("mdui-theme-dark");
    setCookie("theme", 0, 0);
};

function changeTheme() {
    darkMode = getCookie('theme');
    if (darkMode == 1) {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
};