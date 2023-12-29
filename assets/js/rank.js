$(function () {
    marked.setOptions({
        gfm: true,
        tables: true,
        breaks: true,
        pedantic: false,
        sanitize: false,
        smartypants: true,
        headerIds: false
    });
    $.get(
        url = "/v2/info",
        data = { "for": "web" },
    )
        .done(function (data) {
            web(data.data);
        })
    $("#rank").html('');
    $.get(
        url = "/v2/hot",
    )
        .done(function (data) {
            rank(data.data);
        })
})

function web(data) {
    $("#title_bar").html(data.index_title);
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