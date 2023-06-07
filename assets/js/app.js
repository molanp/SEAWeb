window.onload = function() {
    var inst = new mdui.Drawer('#drawer',overlay=true,swipe=true);
    //夜间模式
    let darkMode = getCookie("theme");
    if (darkMode === "dark") enableDarkMode();
    //侧边栏
    let btn = document.getElementById("aside_btn")
    btn.style.marginLeft="-5px";
    inst.close();
    //sider
    mdui.$('#aside_btn').on('click', function () {
        inst.toggle();
      });
      marked.setOptions({
        gfm: true,//默认为true。 允许 Git Hub标准的markdown.
        tables: true,//默认为true。 允许支持表格语法。该选项要求 gfm 为true。
        breaks: false,//默认为false。 允许回车换行。该选项要求 gfm 为true。
        pedantic: false,//默认为false。 尽可能地兼容 markdown.pl的晦涩部分。不纠正原始模型任何的不良行为和错误。
        sanitize: false,//对输出进行过滤（清理）
        smartLists: true,
        smartypants: false,//使用更为时髦的标点，比如在引用语法中加入破折号。
        mangle: false,//因warning禁用
        headerIds: false//因warning禁用
    });
    load_info();
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

function load_info() {
    $.get(
        url=window.location.origin+'/v2/info',
        data={"for":"web"},
    )
    .done(function(data,status) {
        if (status=='success') {
            var data = data.data;
            if (window.location.pathname=='/') {
                document.getElementsByName("title")[1].innerHTML = data.index_title;
                document.getElementsByName("index_description")[0].innerHTML = DOMPurify.sanitize(marked.parse(data.index_description));
                document.getElementsByName("notice")[0].innerHTML = DOMPurify.sanitize(marked.parse(data.notice.data));
                document.getElementsByName("latesttime")[0].innerHTML = data.notice.latesttime;
                var link_list = '';
                links = data.links.split(/[\r\n]+/);
                for (var link in links) {
                    link_list += `<div class="mdui-chip">
                    <span class="mdui-chip-title">🤣${marked.parse(links[link]).match(/<p>(.*?)<\/p>/)[1]}</span>
                    </div>`;
                }
                document.getElementsByName("links")[0].innerHTML = link_list;
            }
            document.getElementsByName("title")[0].innerHTML = data.index_title;
            document.getElementsByName("copyright")[0].innerHTML = "&copy;" +data.copyright;
            document.getElementsByName("copyright")[1].innerHTML = "&copy;" +data.copyright;
            document.getElementsByName("version")[0].innerHTML = data.version;
            document.getElementsByName("record")[0].innerHTML = data.record;

        } else {
            console.error("Loading Info Error!");
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
    $.get(
        url=window.location.origin+'/v2/info',
    )
    .done(function(data,status) {
        if(status=='success') {
            var data = data.data;
            var list = '';
            for (var type in data) {
                list += `<li class='mdui-subheader'>${type}</li>`;
                for (var plugin in data[type]) {
                    if (window.location.pathname!='/'&&window.location.pathname.match(/\/([^\/]+)\/?$/)[1]==data[type][plugin]["path"]) {
                        list += `<li class='mdui-list-item mdui-ripple' id='active'>
                        <a class='mdui-list-item-content' href='#'>
                        ${DOMPurify.sanitize(plugin)}
                        </a>
                        </li>`;
                        var api_name = plugin;
                        var api_data = data[type][plugin];
                    } else {
                        list += `<li class='mdui-list-item mdui-ripple'>
                        <a class='mdui-list-item-content' href='/i/${data[type][plugin]["path"]}'">
                        ${DOMPurify.sanitize(plugin)}
                        </a>
                        </li>`;
                    }
                }
            };
            document.getElementsByName("sider_list")[0].innerHTML = list;
            if (window.location.pathname!='/') {
                document.getElementsByName("api_name")[0].innerHTML = DOMPurify.sanitize(api_name);
                document.getElementsByName("return_parameters")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.return_parameters));
                document.getElementsByName("request_parameters")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.request_parameters));
                document.getElementsByName("api_address")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.api_address));
                document.getElementsByName("author")[0].innerHTML = DOMPurify.sanitize(api_data.author);
                document.getElementsByName("api_version")[0].innerHTML = DOMPurify.sanitize(api_data.version);
                document.getElementsByName("api_profile")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.api_profile));
            }

        } else {
            console.error("Loading Info Error!");
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
}