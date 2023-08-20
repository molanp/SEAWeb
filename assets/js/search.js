function Search() {
    search_data = {};
    var search = JSON.parse(sessionStorage.getItem('data_api'));
    for (var type in search) {
        for (var plugin_name in search[type]) {
            search_data[plugin_name] = [search[type][plugin_name]['api_profile'], search[type][plugin_name]['path']];
        }
    }
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
        var title = "<span class='mdui-text-color-theme'>" + key.replace(new RegExp(inp.value, 'g'), "<span class='mdui-text-color-green'><b>" + inp.value + "</b></span>") + "</span>";
        var text = "<span class='mdui-text-color-theme'>" + data[key][0].replace(new RegExp(inp.value, 'g'), "<span class='mdui-text-color-green'><b>" + inp.value + "</b></span>") + "</span>";
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
    if (sessionStorage.getItem('theme')!= 1) {
        ul.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
    } else {
        ul.style.backgroundColor = 'rgba(48, 48, 48, 0.8)';

    }
    mdui.mutation();
}