$(function() {
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
			data = {
				url: window.location.pathname,
				for: "api"
			}
		)
		.done(function(data) {
			api(data.data);
		});
	$.get(
			url = "/v2/info",
			data = {
				"for": "web"
			},
		)
		.done(function(data) {
			web(data.data);
		})
    $('#code').html(span_code('javascript'))
})

function web(data) {
	$("#title_bar")
		.html(data.index_title);
	$("#version")
		.html("Version " + data.version);
	$("#copyright")
		.html("&copy;" + data.copyright);
	$("#record")
		.html(data.record);
}

function api(api_data) {
	$("#api_name")
		.html(api_data.name);
	$("#api_count")
		.html(api_data.count);
	$("#response")
		.html(marked.parse(api_data.response));
	$("#request")
		.html(marked.parse(api_data.request));
	path = window.location.pathname.match(/\/docs(.*)\//)[1];
	$("#api_address")
		.html(marked.parse(`|Method|Url|\n|--|--|\n|${api_data.method}|<a target="_blank" href="/api${path}">/api${path}</a>|`));
	$("#author")
		.html(api_data.author);
	$("#api_version")
		.html(api_data.version);
	$("#api_profile")
		.html(marked.parse(api_data.profile));
	$("#urlInput")
		.attr("value", window.location.host + "/api" + path);
}

function createLightbox(imageSrc) {
	const lightbox = document.createElement('div');
	lightbox.classList.add('lightbox');

	const image = document.createElement('img');
	image.src = imageSrc;
	image.alt = 'Lightbox Image';

	lightbox.appendChild(image);
	document.body.appendChild(lightbox);

	return lightbox;
}

function openLightbox(imageSrc) {
	const lightbox = createLightbox(imageSrc);
	lightbox.classList.add('active');
	const image = lightbox.querySelector('img');
	let scale = 1;
	interact('.lightbox img')
		.gesturable({
			onmove: function(event) {
				scale += event.ds;
				image.style.transform = `scale(${scale})`;
			},
		})
		.styleCursor(false);
	image.addEventListener('click', (event) => {
		if (event.target === image) {
			closeLightbox();
		}
	});
	lightbox.addEventListener('click', (event) => {
		if (event.target === lightbox) {
			closeLightbox();
		}
	});
}

function closeLightbox() {
	const lightbox = document.querySelector('.lightbox');
	lightbox.classList.remove('active');
	lightbox.addEventListener('transitionend', () => {
		lightbox.remove();
	});
}

let paramIndex = 0;

$(document)
	.ready(function() {
		$("#methodSelect")
			.change(function() {
				if ($(this)
					.val() === "PUT") {
					$("#paramsTable")
						.html($("<input>")
							.attr({
								"type": "file",
								"name": "file",
								"id": "file",
								"placeholder": "选择文件"
							}));
				} else {
					$("#paramsTable")
						.html(
							`<thead>
    <tr>
        <th>参数名</th>
        <th>值</th>
        <th><a href="javascript:addParamRow()">添加参数</a></th>
    </tr>
</thead>
<tbody>
</tbody>`
						);
				}
			});
	});

function addParamRow() {
	const newRow = $("<tr></tr>");

	const paramNameCell = $("<td></td>");
	const paramNameInput = $("<input>")
		.attr({
			"class": "mdui-text-field",
			"type": "text",
			"name": `paramName-${paramIndex}`,
			"placeholder": "参数名"
		});
	paramNameCell.append(paramNameInput);

	const paramValueCell = $("<td></td>");
	const paramValueInput = $("<input>")
		.attr({
			"class": "mdui-text-field",
			"type": "text",
			"name": `paramValue-${paramIndex}`,
			"placeholder": "值"
		});
	paramValueCell.append(paramValueInput);

	const actionCell = $("<td></td>");
	const deleteButton = $("<mdui-button-icon></mdui-button-icon>")
		.attr({
			"style": "color: red"
		})
		.attr("icon", "delete")
		.click(function() {
			newRow.remove();
		});
	actionCell.append(deleteButton);

	newRow.append(paramNameCell, paramValueCell, actionCell);
	$("#paramsTable")
		.append(newRow);

	paramIndex++;
}

function sendRequest() {
	const methodSelect = $("#methodSelect");

	if (methodSelect.val() !== "PUT") {
		const params = {};
		$("#paramsTable tr")
			.each(function(index, row) {
				const paramName = $(row)
					.find("td:eq(0) input")
					.val();
				const paramValue = $(row)
					.find("td:eq(1) input")
					.val();
				if (paramName && paramValue) {
					params[paramName] = paramValue;
				}
			});

		$.ajax({
			url: `${window.location.pathname.match(/\/docs(.*)\//)[1].startsWith("/api") ? "" : "/api"}${window.location.pathname.match(/\/docs(.*)\//)[1]}`,
			method: methodSelect.val(),
			data: params,
			success: function(data, status, jqxhr) {
				var contentType = jqxhr.getResponseHeader("content-type");
				if (contentType.startsWith("image/")) {
					var container = $("#responseTEXT");
					container.html("暂不支持查看图片");
				} else {
					renderResponseCard(data);
				}
			},
			error: function(xhr) {
				const response = xhr.responseText || "请求失败";
				try {
					const jsonResponse = JSON.parse(response);
					renderResponseCard(jsonResponse);
				} catch (e) {
					renderResponseCard(response);
				}
			}
		});
	} else {
		$.ajax({
			url: `${window.location.pathname.match(/\/docs(.*)\//)[1].startsWith("/api") ? "" : "/api"}${window.location.pathname.match(/\/docs(.*)\//)[1]}`,
			method: "PUT",
			data: $('#file')[0].files[0],
			processData: false,
			contentType: false,
			success: function(data, status, jqxhr) {
				var contentType = jqxhr.getResponseHeader("content-type");
				if (contentType.startsWith("image/")) {
					var container = $("#responseTEXT");
					container.html("暂不支持查看图片");
				} else {
					renderResponseCard(data);
				}
			},
			error: function(xhr) {
				const response = xhr.responseText || "请求失败";
				try {
					const jsonResponse = JSON.parse(response);
					renderResponseCard(jsonResponse);
				} catch (e) {
					renderResponseCard(response);
				}
			}
		});
	}
}

function syntaxHighlight(json) {
	json = json.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
		var cls = "number";
		if (/^"/.test(match)) {
			if (/:$/.test(match)) {
				cls = "key";
			} else {
				cls = "string";
			}
		} else if (/true|false/.test(match)) {
			cls = "boolean";
		} else if (/null/.test(match)) {
			cls = "null";
		}
		return "<span class='" + cls + "'>" + match + "</span>";
	});
}

function renderResponseCard(response) {
	$("#responseTEXT")
		.html(syntaxHighlight(JSON.stringify(response, undefined, 4)));
}

function getKeys(obj, prefix = '') {
	let keys = [];

	if (Array.isArray(obj)) {
		obj.forEach((item, index) => {
			keys = keys.concat(getKeys(item, `${prefix}[${index}]`));
		});
	} else if (typeof obj === 'object') {
		Object.keys(obj)
			.forEach(key => {
				const fullKey = prefix ? `${prefix}.${key}` : key;
				if (!Array.isArray(obj[key]) && typeof obj[key] !== 'object') {
					keys.push(fullKey);
				}
				keys = keys.concat(getKeys(obj[key], fullKey));
			});
	}
	return keys;
}

function getValue(key) {
	let obj = JSON.parse($('#responseTEXT')
		.text());
	const keys = key.split('.');
	let value = obj;
	for (let i = 0; i < keys.length; i++) {
		const currentKey = keys[i];

		if (currentKey.includes('[') && currentKey.includes(']')) {
			const startIndex = currentKey.indexOf('[') + 1;
			const endIndex = currentKey.indexOf(']');
			const index = parseInt(currentKey.substring(startIndex, endIndex));
			value = value[currentKey.substring(0, startIndex - 1)][index];
		} else {
			value = value[currentKey];
		}
		if (value === undefined) {
			break;
		}
	}
	return value;
}

function preview() {
	let data = JSON.parse($('#responseTEXT')
		.text() || '{}');
	let chips = "";
	getKeys(data)
		.forEach((chip, index) => {
			chips += `<mdui-chip variant="filter" onclick="getData(getValue('${chip}'))">${chip}</mdui-chip>`;
		});
	popups.dialog(chips || "ㄟ( ▔, ▔ )ㄏ 什么也没有", "选取要查看的项");
}

function getData(data) {
	let preElement = document.createElement('pre');
	preElement.textContent = data;
	document.body.appendChild(preElement);
	preElement.style.whiteSpace = 'pre-wrap';
	preElement.style.display = 'inline-block';
	domtoimage.toSvg(preElement)
		.then(function(dataUrl) {
			preElement.remove();
			openLightbox(dataUrl);
		})
}


function span_code(language) {
	const url = $("#urlInput")
		.val();
	const method = $("#methodSelect")
		.val();
	if (method !== "PUT") {
		var params = {};
		$("#paramsTable tr")
			.each(function(index, row) {
				const paramName = $(row)
					.find("td:eq(0) input")
					.val();
				const paramValue = $(row)
					.find("td:eq(1) input")
					.val();
				if (paramName && paramValue) {
					params[paramName] = paramValue;
				}
			});

	} else {
		return "No data."
	}
	let code = "";

	switch (language.toLowerCase()) {
		case "python":
			const paramString = JSON.stringify(params);
			code = `import requests\n\nurl = ${url}\nparams = ${paramString}\n\nresponse = requests.${method.toLowerCase()}(url, params=params)\n`;
			code += `if response.headers['Content-Type'] == 'application/json':\n`;
			code += `    data = response.json()\n`;
			code += `else:\n`;
			code += `    data = response.text\n`;
			code += `print(data)\n`;
			break;

		case "javascript":
			code = `const url = "${url}";\nconst params = ${JSON.stringify(params)};\n\nfetch(url, {\n  method: "${method}",\n  body: params,\n})\n`;
			code += `  .then(response => {\n`;
			code += `    if (response.headers.get('content-type') === 'application/json')\n`;
			code += `      return response.json();\n`;
			code += `    else\n`;
			code += `      return response.text();\n`;
			code += `  })\n`;
			code += `  .then(data => console.log(data))\n`;
			code += `  .catch(error => console.error("请求失败"));\n`;
			break;

		case "php":
			const param = Object.keys(params)
                .map((key) => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
                .join("&");
            code = `<?php\n\n$url = "${url}";\n$params="${param}";\n$response = file_get_contents($url . "?" . $params);\nif ($response === false) {\n  echo "请求失败";\n} else {\n  $contentType = $http_response_header[0];\n  if (strpos($contentType, 'application/json') !== false) {\n    $data = json_decode($response);\n  } else {\n    $data = $response;\n  }\n  print_r($data);\n}\n`;
            break;

		case "java":
            const paramStringJava = Object.keys(params)
                .map((key) => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
                .join("&");
            code = `import java.io.BufferedReader;\nimport java.io.InputStreamReader;\nimport java.net.HttpURLConnection;\nimport java.net.URL;\n\npublic class HttpRequestExample {\n\n  public static void main(String[] args) throws Exception {\n    String url = "${url}";\n    String params = "${paramStringJava}";\n\n    URL obj = new URL(url + "?" + params);\n    HttpURLConnection con = (HttpURLConnection) obj.openConnection();\n    con.setRequestMethod("${method}");\n\n    BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));\n    String inputLine;\n    StringBuffer response = new StringBuffer();\n    while ((inputLine = in.readLine()) != null) {\n      response.append(inputLine);\n    }\n    in.close();\n\n    String contentType = con.getHeaderField("Content-Type");\n    if (contentType != null && contentType.contains("application/json")) {\n      String data = response.toString();\n      // parse JSON data\n      System.out.println(data);\n    } else {\n      System.out.println(response.toString());\n    }\n  }\n}`;
            break;

		case "powershell":
			const paramStringPs = Object.keys(params)
				.map((key) => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
				.join("&");
			code = `$url = "${url}"\n$params = "${paramStringPs}"\n\n$wc = New-Object System.Net.WebClient\n$response = $wc.DownloadString("$url?$params")\n\n$contentType = $wc.ResponseHeaders["Content-Type"]\nif ($contentType -ne $null -and $contentType.Contains("application/json")) {\n  $data = ConvertFrom-Json $response\n} else {\n  $data = $response\n}\nWrite-Output $data\n`;
			break;

		default:
			code = "No data.";
			break;
	}

	return code;
}