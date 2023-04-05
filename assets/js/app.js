//cookies
function getCookie(cname)
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) 
    {
        var c = ca[i].trim();
        if (c.indexOf(name)==0) return c.substring(name.length,c.length);
    }
    return "";
}
function setCookie(cname,cvalue)
{
    document.cookie = cname + "=" + cvalue + "; " + "path=/";
}
//夜间模式
let darkMode = getCookie("theme");
if (darkMode === "dark") enableDarkMode();
function enableDarkMode() {
    document.body.classList.add("dark");
    setCookie("theme", "dark");
};
function disableDarkMode() {
    document.body.classList.remove("dark");
    setCookie("theme", "light");
};
function changeTheme() {
    darkMode = getCookie("theme");
    if (darkMode === "dark") {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
};
window
.matchMedia("(prefers-color-scheme: dark)")
.addListener(e => (e.matches ? enableDarkMode() : disableDarkMode()))
//aside
function goout(x) {
    x.style.backgroundColor='#eb6161';
    x.style.marginLeft="-5px"
}
function goin(x) {
    x.style.backgroundColor='#F08080';
    x.style.marginLeft="-20px"
}