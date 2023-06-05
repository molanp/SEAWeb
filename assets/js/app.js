window.onload = function() {
    var $$ = mdui.$;
    var inst = new mdui.Drawer('#drawer',overlay=true,swipe=true);
    //夜间模式
    let darkMode = getCookie("theme");
    if (darkMode === "dark") enableDarkMode();
    //侧边栏
    let btn = document.getElementById("aside_btn")
    btn.style.marginLeft="-5px";
    inst.close();
    //sider
    $$('#aside_btn').on('click', function () {
        inst.toggle();
      });
    load_home_info();
    mdui.mutation();
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
    var $ = mdui.$;
    $('body').addClass("mdui-theme-layout-dark");
    setCookie("theme", "dark");
};
function disableDarkMode() {
    var $ = mdui.$;
    $('body').removeClass("mdui-theme-layout-dark");
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
.addListener(e=>(e.matches ? enableDarkMode() : disableDarkMode()))
//aside
function goout(x) {
    x.style.backgroundColor='#eb6161';
}
function goin(x) {
    x.style.backgroundColor='#F08080';
}

function load_home_info() {
    $.get(
        url=window.location.origin+'/v2/info',
        data= {"for":"web"},
        function(data,status) {
            if (status=='success') {
                var data = data.data;
                try {
                    document.getElementsByName("title")[0].innerHTML = data.index_title;
                    document.getElementsByName("title")[1].innerHTML = data.index_title;
                
                /*
                document.getElementsByName("index_description")[0].value = data.index_description;
                document.getElementsByName("notice")[0].innerHTML = data.notice.data;
                document.getElementsByName("copyright")[0].value = data.copyright;
                document.getElementsByName("record")[0].value = data.record;
                document.getElementsByName("links")[0].value = data.links;
                document.getElementsByName("keywords")[0].value = data.keywords;
                document.getElementsByName("version")[0].innerHTML = data.version;*/
                } catch(error) {
                    console.log(error)
                }
            }
        }
    );
}