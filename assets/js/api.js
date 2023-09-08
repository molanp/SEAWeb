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
    api_info();
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

function api_info() {
    $.get(
    url=window.location.origin+'/v2/api_info',
    data = {"url": window.location.pathname}
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
    $.get(
        url=window.location.origin+'/v2/info',
        data={"for":"web"},
    )
    .done(function(data,status) {
        if (data.status==200) {
            var data = data.data;
            _web(data);
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
}

function _web(data) {
    document.getElementById("title").innerHTML = data.index_title;
    document.getElementById("version").innerHTML = "Version "+data.version+"<br/>";
    document.getElementsByName("copyright")[0].innerHTML = "&copy;" +data.copyright;
    document.getElementsByName("record")[0].innerHTML = data.record;
}

function _api(api_data) {
    document.getElementsByName("api_name")[0].innerHTML = DOMPurify.sanitize(api_data.name);
    document.getElementsByName("api_count")[0].innerHTML = DOMPurify.sanitize(api_data.count);
    document.getElementsByName("response")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.response));
    document.getElementsByName("request")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.request));
    document.getElementsByName("api_address")[0].innerHTML = DOMPurify.sanitize(marked.parse(`|Method|Url|\n|--|--|\n|${api_data.method}|<a target='_blank' href='/api${window.location.pathname}'>/api${window.location.pathname}</a>|`));
    document.getElementsByName("author")[0].innerHTML = DOMPurify.sanitize(api_data.author);
    document.getElementsByName("api_version")[0].innerHTML = DOMPurify.sanitize(api_data.version);
    document.getElementsByName("api_profile")[0].innerHTML = DOMPurify.sanitize(marked.parse(api_data.profile));
}