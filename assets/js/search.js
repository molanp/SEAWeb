function output() {
    mdui.dialog({
        title: '<input class="mdui-textfield-input" type="text" placeholder="搜索api名称" id="search"/>',
        content: `<div class="mdui-panel mdui-panel-scrollable" style="min-height: 200px;">
            <ul class="mdui-list" id="search_result">
            </ul>
        </div>`,
        buttons: [{
            text: '关闭',
        }],
        onOpened: function() {
            document.getElementById('search').addEventListener('input', search_);
        }
    })
}
function search_() {
    search_data = {};
    var search = window.search;
    for (var type in search) {
        for (var plugin_name in search[type]) {
            search_data[plugin_name] = [search[type][plugin_name]['api_profile'], search[type][plugin_name]['path']];
        }
    }
    var data = search_data;
    var maxResults = 100;
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
            var title = "<span class='mdui-text-color-black'>" + key.replace(new RegExp(inp.value, 'g'), "<span class='mdui-text-color-theme'><b>" + inp.value + "</b></span>") + "</span>";
            var text = "<span class='mdui-text-color-black'>" + data[key][0].replace(new RegExp(inp.value, 'g'), "<span class='mdui-text-color-theme'><b>" + inp.value + "</b></span>") + "</span>";
            content += "<div class='article-title'>" + title + "</div><div class='mdui-list-item-text'>" + text + "</div>";
            li.innerHTML = content;
            (function(url) {
                li.addEventListener('click', function () {
                window.location.href = url;
                });
            })(data[key][1]);
            ul.appendChild(li);
            displayedResults++;
        }
    }
    mdui.mutation();
}