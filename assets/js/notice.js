function notice() {
    $.get("/v2/notice")
    .done(function(data) {
        if (data.status==200) {
            data = data.data;
            mdui.dialog({
                headline: '公告 | '+data.time,
                body: marked.parse(data.notice),
                actions: [
                    {
                      text: "OK",
                    }
                ]
            });
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data){
        alert(data.responseJSON.data)
    });
}