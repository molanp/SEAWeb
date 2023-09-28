function notice() {
    $.get("/v2/notice")
    .done(function(data) {
        if (data.status==200) {
            data = data.data;
            mdui.dialog({
                title: '公告<code>'+data.time+'</code>',
                content: marked.parse(data.notice),
                buttons: [{
                    text: '确定',
                }]
            });
        } else {
            alert(JSON.stringify(data.data));
        }
    })
    .fail(function(data){
        alert(data.responseJSON.data)
    });
}