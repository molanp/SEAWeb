function notice() {
    data = JSON.parse(sessionStorage.getItem('data_web'))['web'];
    mdui.dialog({
        title: '公告<code>'+data.notice.latesttime+'</code>',
        content: marked.parse(data.notice.data),
        buttons: [{
            text: '确定',
        }]
    });
}