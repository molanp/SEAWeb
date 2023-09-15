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
    load();
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
    sessionStorage.removeItem("theme");
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
    document.getElementById("title").innerHTML = data.index_title;
    document.getElementById("version").innerHTML = "Version "+data.version+"<br/>";
    document.getElementById("copyright").innerHTML = "&copy;" +data.copyright;
    document.getElementById("record").innerHTML = data.record;
}

function _load(data) {
    table = "";
    num = 0
    for (var item in data) {
            num = num + 1;
            table+=`<tr>
            <td>${num}</td>
            <td><a href="${data[item]["url"].replace(/\/api/g, "")}">${data[item]["name"]}</a></td>
            <td>${data[item]["count"]}</td>
        </tr>`
    }
    console.log(table)
    document.getElementById("rank").innerHTML = table;
}


function load() {
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
        url=window.location.origin+'/v2/hot',
    )
    .done(function(data,status) {
        if (data.status==200) {
            _load(data.data);
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data,status){
        alert(`信息加载失败 code:${status}`)
    });
}