window.onload = function() {
    let darkMode = getCookie("theme");
    if (darkMode === "dark") enableDarkMode();

    let btn = document.getElementById("aside_btn")
    btn.style.marginLeft="-5px";

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
    load_info();

    document.getElementById('search').addEventListener('input', Search);
    window.search_data = {};
    var sider = JSON.parse(getCookie('sider'));
    for (var type in sider) {
        for (var plugin_name in sider[type]) {
            search_data[plugin_name] = [sider[type][plugin_name]['api_profile'], sider[type][plugin_name]['path']];
        }
    }

    var inst = new mdui.Drawer('#drawer',overlay=true,swipe=true);
    inst.close();
    mdui.$('#aside_btn').on('click', function () {
        inst.toggle();
    });
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

function goout(x) {
    x.style.backgroundColor='#eb6161';
}

function goin(x) {
    x.style.backgroundColor='#F08080';
}

function cookie_sider(data) {
    var list = '';
    for (var type in data) {
        list += `<li class='mdui-subheader'>${type}</li>`;
        for (var plugin in data[type]) {
            if (window.location.pathname!='/'&&window.location.pathname.match(/(.*)$/)[1]==data[type][plugin]["path"]) {
                list += `<li class='mdui-list-item mdui-ripple mdui-list-item-active'>
                <a class='mdui-list-item-content' href='#'>
                ${DOMPurify.sanitize(plugin)}
                </a>
                </li>`;
                var api_name = plugin;
                var api_data = data[type][plugin];
            } else {
                list += `<li class='mdui-list-item mdui-ripple'>
                <a class='mdui-list-item-content' href='${data[type][plugin]["path"]}'">
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
}

function cookie_web(data) {
    if (window.location.pathname=='/') {
        document.getElementsByName("title")[1].innerHTML = data.index_title;
        document.getElementsByName("index_description")[0].innerHTML = DOMPurify.sanitize(marked.parse(data.index_description));
        document.getElementsByName("notice")[0].innerHTML = DOMPurify.sanitize(marked.parse(data.notice.data));
        document.getElementsByName("latesttime")[0].innerHTML = data.notice.latesttime;
        var link_list = '';
        links = data.links.split(/[\r\n]+/);
        for (var link in links) {
            link_list += `<div class="mdui-chip">
            <img class="mdui-chip-icon" src="/favicon.ico">
            <span class="mdui-chip-title">${marked.parse(links[link]).match(/<p>(.*?)<\/p>/)[1]}</span>
            </div>`;
        }
        document.getElementsByName("links")[0].innerHTML = link_list;
    }
    document.getElementsByName("title")[0].innerHTML = data.index_title;
    document.getElementsByName("copyright")[0].innerHTML = "&copy;" +data.copyright;
    document.getElementsByName("copyright")[1].innerHTML = "&copy;" +data.copyright;
    document.getElementsByName("version")[0].innerHTML = data.version;
    document.getElementsByName("record")[0].innerHTML = data.record;
}

function load_info() {
    $.get(url=window.location.origin+'/v2/sitemap');
    if (getCookie('web')) {
        cookie_web(JSON.parse(getCookie('web')));
    } else {
        $.get(
            url=window.location.origin+'/v2/info',
            data={"for":"web"},
        )
        .done(function(data,status) {
            if (status=='success') {
                var data = data.data;
                cookie_web(data);
                setCookie('web',JSON.stringify(data));
            } else {
                console.error("Loading Info Error!");
            }
        })
        .fail(function(data,status){
            alert(`信息加载失败 code:${status}`)
        });
    };
    if (getCookie('sider')) {
        cookie_sider(JSON.parse(getCookie('sider')));
    } else {
        $.get(
            url=window.location.origin+'/v2/info',
        )
        .done(function(data,status) {
            if(status=='success') {
                var data = data.data;
                cookie_sider(data);
                setCookie('sider',JSON.stringify(data));
            } else {
                console.error("Loading Info Error!");
            }
        })
        .fail(function(data,status){
            alert(`信息加载失败 code:${status}`)
        });
    }
}

function Search() {
    var data = search_data;
    var maxResults = 3;
    var displayedResults = 0;
    var ul = document.getElementById('search_result');
    var inp = document.getElementById('search');
    if (inp.value == "") {
        ul.innerHTML = '';
        return;
    }
    ul.innerHTML = '';
    displayedResults = 0;
    for (var key in data) {
        if (displayedResults >= maxResults) {
        break;
        }
        if ((key.includes(inp.value) || data[key][0].includes(inp.value)) && data[key][0] !== "") {
        var li = document.createElement('li');
        li.classList.add('mdui-list-item');
        var content = "<div class='mdui-list-item-content'>";
        var title = "<span class='mdui-text-color-theme'>" + key.replace(new RegExp(inp.value, 'g'), "<span class='mdui-text-color-green'>" + inp.value + "</span>") + "</span>";
        var text = "<span class='mdui-text-color-theme'>" + data[key][0].replace(new RegExp(inp.value, 'g'), "<span class='mdui-text-color-green'>" + inp.value + "</span>") + "</span>";
        content += "<div class='article-title'>" + title + "</div><div class='mdui-list-item-text'>" + text + "</div>";
        li.innerHTML = content;
        (function(url) {
            li.addEventListener('click', function () {
            window.location.href = window.location.origin + url;
            });
        })(data[key][1]);
        ul.appendChild(li);
        displayedResults++;
        }
    }
    ul.style.position = "absolute";
    ul.style.zIndex = "9999";
    if (getCookie('theme')!= 'dark') {
        ul.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
    } else {
        ul.style.backgroundColor = 'rgba(48, 48, 48, 0.8)';

    }
    mdui.mutation();
}