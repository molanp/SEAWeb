window.onload = function() {
    let darkMode = getCookie("theme");
    if (darkMode === "dark") enableDarkMode();

    marked.setOptions({
        gfm: true,//默认为true。 允许 Git Hub标准的markdown.
        tables: true,//默认为true。 允许支持表格语法。该选项要求 gfm 为true。
        breaks: true,//默认为false。 允许回车换行。该选项要求 gfm 为true。
        pedantic: false,//默认为false。 尽可能地兼容 markdown.pl的晦涩部分。不纠正原始模型任何的不良行为和错误。
        sanitize: false,//对输出进行过滤（清理）
        smartLists: true,
        smartypants: false,//使用更为时髦的标点，比如在引用语法中加入破折号。
        mangle: false,//因warning禁用
        headerIds: false//因warning禁用
    });
    api_info();

    //document.getElementById('search').addEventListener('input', Search);
    window.search_data = {};
    var search = JSON.parse(getCookie('search'));
    for (var type in search) {
        for (var plugin_name in search[type]) {
            search_data[plugin_name] = [search[type][plugin_name]['api_profile'], search[type][plugin_name]['path']];
        }
    }
    mdui.mutation();
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) 
    {
        var c = ca[i].trim();
        if (c.indexOf(name)==0) return c.substring(name.length,c.length);
    }
    return null;
}

function setCookie(cname,cvalue) {
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

function api_info() {
    $.get(url=window.location.origin+'/v2/info')
    .done(function(data,status) {
        if (data.status==200) {
            var data = data.data;
            setCookie('search',data);
            path = window.location.pathname.match(/(.*)$/)[1];
            for (var type in data) {
                for (var plugin in data[type]) {
                    if (window.location.pathname.match(/(.*)$/)[1]==data[type][plugin]["path"]) {
                        var api_name = plugin;
                        var api_data = data[type][plugin];
                    }
                }
            }
            document.getElementsByName("api_name")[0].innerHTML = DOMPurify.sanitize(api_name);
            document.getElementsByName("return_parameters")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.return_parameters));
            document.getElementsByName("request_parameters")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.request_parameters));
            document.getElementsByName("api_address")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.api_address));
            document.getElementsByName("author")[0].innerHTML = DOMPurify.sanitize(api_data.author);
            document.getElementsByName("api_version")[0].innerHTML = DOMPurify.sanitize(api_data.version);
            document.getElementsByName("api_profile")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.api_profile));

        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
    $.get(
        url=window.location.origin+'/v2/info',
        data={"for":"web"},
    )
    .done(function(data,status) {
        if (data.status==200) {
            var data = data.data;
            cookie_web(data);
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
}

function cookie_web(data) {
    document.getElementById("title").innerHTML = data.web.index_title;
    document.getElementById("version").innerHTML = "Version "+data.version+"<br/>";
    document.getElementsByName("copyright")[0].innerHTML = "&copy;" +data.web.copyright;
    document.getElementsByName("record")[0].innerHTML = data.web.record;
}