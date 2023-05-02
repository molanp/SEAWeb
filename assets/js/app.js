
window.onload = function() {
    //夜间模式
    let darkMode = getCookie("theme");
    let theme = document.querySelector("#theme");
    if (darkMode === "dark") enableDarkMode();
    //侧边栏
    let box = document.getElementById("aside")
    let btn = document.getElementById("aside_btn")
    btn.onclick = function() {
        if (box.offsetLeft == 0) {
            box.style['margin-left'] = -1*document.body.clientWidth + "px"
        } else {
            box.style['margin-left'] = 0 + "px"
        }
    }
    btn.style.marginLeft="-20px";
}
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

function enableDarkMode() {
    theme.classList.add("dark");
    setCookie("theme", "dark");
};
function disableDarkMode() {
    theme.classList.remove("dark");
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