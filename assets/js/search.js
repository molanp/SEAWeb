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
            $('#search').on('input', search_);
        }
    })
}

function search_() {
    var search_data = {};
    var search = window.search;
    for (var type in search) {
        for (var plugin_name in search[type]) {
            search_data[plugin_name] = [search[type][plugin_name]['api_profile'], search[type][plugin_name]['path']];
        }
    }
    var data = search_data;
    var maxResults = 100;
    var displayedResults = 0;
    var ul = $('#search_result');
    var inp = $('#search');
    if (inp.val() == "") {
        ul.html('');
        return;
    }
    ul.html('');
    displayedResults = 0;
    for (var key in data) {
        if (displayedResults >= maxResults) {
            break;
        }
        if ((key.includes(inp.val()) || data[key][0].includes(inp.val())) && data[key][0] !== "") {
            var li = $('<li></li>').addClass('mdui-list-item');
            var content = `<div class="mdui-list-item-content">
                <div class="article-title"><span class="mdui-text-color-black">${key.replace(new RegExp(inp.val(), 'g'), "<span class='mdui-text-color-theme'><b>" + inp.val() + "</b></span>")}</span></div>
                <div class="mdui-list-item-text"><span class="mdui-text-color-black">${data[key][0].replace(new RegExp(inp.val(), 'g'), "<span class='mdui-text-color-theme'><b>" + inp.val() + "</b></span>")}</span></div>
            </div>`;
            li.html(content);
            (function(url) {
                $('li').on('click', function() {
                    window.location.href = $(this).data('url');
                  });                  
            })(data[key][1]);
            ul.append(li);
            displayedResults++;
        }
    }
    mdui.mutation();
}