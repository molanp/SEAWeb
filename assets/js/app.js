window.onload = function() {
    let darkMode = sessionStorage.theme;
    if (darkMode == 1) enableDarkMode();

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
    mdui.mutation();
}

function enableDarkMode() {
    var $ = mdui.$;
    $('body').addClass("mdui-theme-layout-dark");
    sessionStorage.setItem("theme", 1);
};

function disableDarkMode() {
    var $ = mdui.$;
    $('body').removeClass("mdui-theme-layout-dark");
    sessionStorage.setItem("theme", 0);
};

function changeTheme() {
    darkMode = sessionStorage.getItem('theme');
    if (darkMode == 1) {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
};

window
.matchMedia("(prefers-color-scheme: dark)")
.addListener(e=>(e.matches ? enableDarkMode() : disableDarkMode()))

function _web(data) {
    document.getElementsByName("title")[0].innerHTML = data.index_title;
    document.getElementsByName("title")[1].innerHTML = data.index_title;
    document.getElementsByName("index_description")[0].innerHTML = DOMPurify.sanitize(marked.parse(data.index_description));
    var link_list = '';
    links = data.links.split(/[\r\n]+/);
    for (var link in links) {
        link_list += `<div class="mdui-chip">
        <img class="mdui-chip-icon" src="/favicon.ico">
        <span class="mdui-chip-title">${marked.parse(links[link]).match(/<p>(.*?)<\/p>/)[1]}</span>
        </div>`;
    }
    document.getElementsByName("links")[0].innerHTML = link_list;
    document.getElementsByName("version")[0].innerHTML = "Version "+data.version+"<br/>";
    document.getElementsByName("copyright")[0].innerHTML = "&copy;" +data.copyright;
    document.getElementsByName("record")[0].innerHTML = data.record;
}

function _api(data) {
    window.search = data;
    item = '';
    for (var type in data) {
        for (var name in data[type]) {
            if (data[type][name].status == false) {
                status = `<div class="mdui-badge mdui-color-red-400 mdui-text-color-white">维护</div>`
            } else {
                status = `<div class="mdui-badge mdui-color-green-400 mdui-text-color-white">正常</div>`
            }
            item += `<div class="mdui-col-sm-6 mdui-col-md-4">
            <div class="mdui-card mdui-hoverable mdui-m-y-2" style="border-radius:10px">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                        ${name}${status}
                        <div class="mdui-card-primary-subtitle" style="font-size:12px;">
                        <i class="mdui-icon material-icons" style="font-size:12px;">equalizer</i>累计调用：N/A次
                            <br/>
                        <i class="mdui-icon material-icons" style="font-size:12px;">folder</i>分类：${type}</div>
                    </div>
                </div>
                <div class="mdui-card-content">${marked.parse(data[type][name].api_profile)}</div>
                <div class="mdui-card-actions">
                    <a class="mdui-btn mdui-ripple mdui-text-color-theme-accent mdui-float-right"
                        target="_blank" href="${data[type][name].path}">More</a>
                </div>
            </div>
        </div>`
        }
    }
    document.getElementById("app_api").innerHTML = item;
}

function load_info() {
    $.get(url=window.location.origin+'/v2/sitemap');
    $.get(
        url=window.location.origin+'/v2/info',
        data={"for":"web"},
    )
    .done(function(data,status) {
        if (data.status==200) {
            _web(data.data);
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
    $.get(
        url=window.location.origin+'/v2/info'
    )
    .done(function(data,status) {
        if (data.status==200) {
            _api(data.data);
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
}