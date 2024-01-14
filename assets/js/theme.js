$(function() {
	mdui.setColorScheme("#39c5bc");
	theme(cookie.get("theme")??"auto");
});
	
function theme_menu (x=cookie) {
    let value = x.get("theme");
    let body = `
    <p>深色模式</p>
    <mdui-segmented-button-group selects="single" value="${value}">
    <mdui-segmented-button value="light" onclick="theme('light')">浅色</mdui-segmented-button>
    <mdui-segmented-button value="auto" onclick="theme('auto')">自动</mdui-segmented-button>
    <mdui-segmented-button value="dark" onclick="theme('dark')">深色</mdui-segmented-button>
    </mdui-segmented-button-group>
    `
    popups.dialog(body, "主题设置")
}

function theme(theme) {
  cookie.set("theme", theme);
  mdui.setTheme(theme);
  return theme;
}

$.ajaxSetup({
	error: function(xhr) {
		if (xhr.status !== 200) {
			popups.dialog(xhr.responseJSON ? xhr.responseJSON.data : xhr.status, "HTTP ${xhr.status}");
		}
	}
});

const popups = {
	dialog: function(data, title = "") {
		mdui.dialog({
			headline: title,
			body: data,
			closeOnOverlayClick: false,
			closeOnEsc: true,
			actions: [{
				text: "确定"
			}]
		});
	},
	tips: {
		id: function() {
			return Math.random()
				.toString(36)
				.substr(2, 9);
		},
		add: function(content, icon = "") {
			var _Container = $('#tips-container');

			if (!_Container.length) {
				_Container = $('<div id="tips-container"></div>')
					.attr('style', 'position: fixed;top: 10px;right: 10px;');
				$('body')
					.append(_Container);
			}

			var id = this.id();

			var tips = $('<mdui-card>')
				.attr('id', id)
				.attr('variant', 'outlined')
				.attr('style', 'max-width: 300px;margin-bottom: 10px;display: flex;align-items: center;position: relative;text-align: left;padding: 0;');

			var iconEl = $('<mdui-icon>')
				.attr("name", icon);

			var tipsContent = $('<div>')
				.attr('style', 'padding: 20px 20px 20px 0;');

			var contentEl = $('<div>')
				.html(content);

			var closeBtn = $('<mdui-icon>')
				.attr("name", 'close')
				.attr('style', 'cursor: pointer;position: absolute;top: 1px;right: 0;')
				.click(function() {
					popups.tips.remove(id);
				});

			tipsContent.append(contentEl);
			tips.append(iconEl, tipsContent, closeBtn);
			_Container.prepend(tips);

			setTimeout(function() {
				popups.tips.remove(id);
			}, 3000);
		},
		remove: function(id) {
			$('#' + id)
				.fadeOut('slow');
			setTimeout(function() {
				$('#' + id)
					.remove();
			}, 1000);
		}
	},
	snaker: {
		add: function(content) {
			var _Container = $('body');

			var snaker = $('<div>')
				.attr({
					'class': 'snaker',
					'style': 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background-color:rgba(0,0,0,0.5);padding:20px;z-index:9999;'
				})
				.html(content);

			_Container.append(snaker);

			setTimeout(function() {
				popups.snaker.remove();
			}, 3000);
		},
		remove: function() {
			$('.snaker')
				.fadeOut('slow');
			setTimeout(function() {
				$('.snaker')
					.remove();
			}, 1000);
		}
	}
};