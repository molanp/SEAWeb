$(function () {
    let darkMode = getCookie("theme");
    if (darkMode == 1) enableDarkMode();
    marked.setOptions({
        gfm: true,
        tables: true,
        breaks: true,
        pedantic: false,
        sanitize: false,
        smartypants: true,
        headerIds: false
    });
    load();
})

function web(data) {
    $("#title").html(data.index_title);
    $("#version").html("Version " + data.version + "<br>");
    $("#copyright").html("&copy;" + data.copyright);
    $("#record").html(data.record);
}

function rank(data) {
    let table = "";
    let num = 0;

    Object.keys(data).forEach((item, index) => {
        num++;
        table += `
        <tr>
          <td>${num}</td>
          <td><a href="/docs${data[item].url.replace(/\/api/, "")}">${data[item].name}</a></td>
        <td>${data[item].count}</td>
        </tr>
      `;
    });

    if (!table) {
        table = `
        <tr>
          <td>1</td>
          <td>无数据</td>
          <td>N/A</td>
        </tr>
      `;
    }

    $("#rank").html(table);
}



function load() {
    $.get(
        url = '/v2/info',
        data = { "for": "web" },
    )
        .done(function (data) {
            if (data.status == 200) {
                web(data.data);
            } else {
                alert(JSON.stringify(data.data));
            }
        })
        .fail(function (data) {
            alert(data.responseJSON.data)
        });
    $.get(
        url = '/v2/hot',
    )
        .done(function (data) {
            if (data.status == 200) {
                rank(data.data);
            } else {
                alert(JSON.stringify(data.data));
            }
        })
        .fail(function (data) {
            alert(data.responseJSON.data)
        });
}