function output_search() {
    mdui.dialog({
        headline: "搜索..",
        body: `
        <mdui-text-field variant="outlined" type="search" label="输入API名称" id="search"></mdui-text-field>
        <div class="mdui-panel mdui-panel-scrollable" style="min-height: 200px;">
            <mdui-list id="search_result">
            </mdui-list>
        </div>`,
        actions: [{
            text: "关闭",
        }],
        onOpened: function () {
            $("#search").on("input", search_);
        }
    })
}

function search_() {
    var search_data = {};
    var search = window.search;
    for (var type in search) {
        for (var plugin_name in search[type]) {
            search_data[plugin_name] = [search[type][plugin_name]["api_profile"], search[type][plugin_name]["path"]];
        }
    }
    var data = search_data;
    var maxResults = 100;
    var displayedResults = 0;
    var ul = $("#search_result");
    var inp = $("#search");
    if (inp.val() == "") {
        ul.html("");
        return;
    }
    ul.html("");
    displayedResults = 0;
    for (var key in data) {
        if (displayedResults >= maxResults) {
            break;
        }
        if ((key.includes(inp.val()) || data[key][0].includes(inp.val())) && data[key][0] !== "") {
            var li = $("<span></span>");
            var content = `
            <mdui-list-item description="${data[key][0]}" description-line="2" target="_blank" href="/docs${data[key][1]}">${key}</mdui-list-item>
            `;
            li.html(content);
            ul.append(li);
            displayedResults++;
        }
    }
}