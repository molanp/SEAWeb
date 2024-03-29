<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
if ($DATABASE->query("SELECT value FROM setting WHERE item = '维护模式'")->fetchColumn() == "true") {
    die(include_once("maintenance.html"));
};
$sql = "SELECT name, profile FROM api WHERE url_path = :urlPath";
$statement = $DATABASE->prepare($sql);
$statement->execute([":urlPath" => addSlashIfNeeded($_GET["__"])]);
$data = $statement->fetch(PDO::FETCH_ASSOC);
if ($data == null) {
    die(include_once($_SERVER["DOCUMENT_ROOT"] . "/404.php"));
}
$web = new Data();
$web = $web->get("web");
req_log();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"
		/>
		<meta name="renderer" content="webkit" />
		<meta name="force-rendering" content="webkit" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="keywords" content="<?=$web["keywords"]?>">
		<meta name="description" content="<?=str_replace("\n", "", strip_tags($data["profile"]))?>">
		<link rel="Shortcut Icon" href="/favicon.ico">
		<link rel="bookmark" href="/favicon.ico" type="image/x-icon" />
		<link href="https://registry.npmmirror.com/mdui/2.0.3/files/mdui.css" rel="stylesheet">
		<script src="https://registry.npmmirror.com/interactjs/1.10.26/files/dist/interact.min.js"></script>
		<script src="https://registry.npmmirror.com/dom-to-image/2.6.0/files/src/dom-to-image.js"></script>
		<script src="/assets/js/marked.min.js"></script>
		<script src="/assets/js/jquery-3.7.1.min.js"></script>
		<script src="/assets/js/bar.js"></script>
		<script src="/assets/js/cookie.js"></script>
		<script src="/assets/js/api.js"></script>
		<title><?=$data[ "name"] . " - " . $web[ "index_title"] ?></title>
	</head>
	<body>
		<mdui-top-app-bar scroll-behavior="elevate" id="bar">
		</mdui-top-app-bar>
		<noscript>
			<div style="text-align: center;margin-top: 10%;">
				<h4>
					Sorry, the web page requires a Javascript runtime environment, please allow you to run scripts or use a new version of the modern browser.
				</h4>
				<p>
					It is recommended to use
					<a href="https://www.microsoft.com/edge/download">
						Edge
					</a>
					modern browser.
				</p>
			</div>
		</noscript>
		<div style="text-align:center;">
			<br />
			<h3 id="api_name">
				<mdui-circular-progress>
				</mdui-circular-progress>
			</h3>
			<mdui-tooltip content="API Version">
				<mdui-chip icon="info_outline">
					<span id="api_version">
						<mdui-circular-progress>
						</mdui-circular-progress>
					</span>
				</mdui-chip>
			</mdui-tooltip>
			<mdui-tooltip content="API Author">
				<mdui-chip icon="account_circle">
					<span id="author">
						<mdui-circular-progress>
						</mdui-circular-progress>
					</span>
				</mdui-chip>
			</mdui-tooltip>
			<mdui-tooltip content="API Count">
				<mdui-chip icon="equalizer">
					<span id="api_count">
						<mdui-circular-progress>
						</mdui-circular-progress>
					</span>
					&nbsp;times
				</mdui-chip>
			</mdui-tooltip>
		</div>
		<div class="grid">
			<mdui-card class="item" variant="outlined">
				<h3>
					<mdui-icon name="language">
					</mdui-icon>
					简介
				</h3>
				<span id="api_profile" class="mdui-prose">
				</span>
			</mdui-card>
			<mdui-card class="item" variant="outlined">
				<h3>
					<mdui-icon name="view_compact">
					</mdui-icon>
					API 地址
				</h3>
				<div class="mdui-table">
					<table id="api_address">
					</table>
				</div>
			</mdui-card>
			<mdui-card class="item" variant="outlined">
				<h3>
					<mdui-icon name="vpn_key">
					</mdui-icon>
					参数列表 (
					<code>*</code>
					是必填项)
				</h3>
				<div class="mdui-table">
					<table id="request">
					</table>
				</div>
			</mdui-card>
			<mdui-card class="item" variant="outlined">
				<h3>
					<mdui-icon name="reply">
					</mdui-icon>
					返回的数据
				</h3>
				<div class="mdui-table">
					<table id="response">
					</table>
				</div>
			</mdui-card>
			<mdui-card class="item" variant="outlined">
				<mdui-tabs value="test_online" full-width>
					<mdui-tab value="test_online" icon="build">
						在线测试
					</mdui-tab>
					<mdui-tab value="code_online" icon="code">
						查看代码
					</mdui-tab>
					<mdui-tab-panel slot="panel" value="test_online">
						<div id="requestForm">
							<mdui-text-field readonly label="URL" id="urlInput">
							</mdui-text-field>
							<mdui-select id="methodSelect" value="GET" label="Method">
								<mdui-menu-item value="GET">GET</mdui-menu-item>
								<mdui-menu-item value="POST">POST</mdui-menu-item>
								<mdui-menu-item value="PUT">PUT</mdui-menu-item>
								<mdui-menu-item value="DELETE">DELETE</mdui-menu-item>
								<mdui-menu-item value="OPTIONS">OPTIONS</mdui-menu-item>
								<mdui-menu-item value="PATCH">PATCH</mdui-menu-item>
							</mdui-select>
						</div>
						<div class="mdui-table">
							<table id="paramsTable">
								<thead>
									<tr>
										<th>
											参数名
										</th>
										<th>
											值
										</th>
										<th>
											<a href="javascript:addParamRow()">
												添加参数
											</a>
										</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<a href="javascript:$('#responseTEXT').html('')">
							清空输出
						</a>
						<a href="javascript:preview()">
							预览内容
						</a>
						<pre class="language-json" id="responseTEXT" style="text-align: left;max-height: 50vh;overflow: auto;">
						</pre>
						<mdui-button onclick="sendRequest()">
							发送请求
						</mdui-button>
            		</mdui-tab-panel>
            		<mdui-tab-panel slot="panel" value="code_online">
            		    <mdui-tabs value="main" full-width>
            				<mdui-tab value="main" onclick="$('#code').text(span_code('javascript'))">
            					JavaScript
            				</mdui-tab>
            				<mdui-tab value="main" onclick="$('#code').text(span_code('php'))">
            					PHP
            				</mdui-tab>
            				<mdui-tab value="main" onclick="$('#code').text(span_code('java'))">
            					Java
            				</mdui-tab>
            				<mdui-tab value="main" onclick="$('#code').text(span_code('python'))">
            					Python
            				</mdui-tab>
            				<mdui-tab value="main" onclick="$('#code').text(span_code('powershell'))">
            					Powershell
            				</mdui-tab>
            				<mdui-tab-panel slot="panel" value="main">
            				    <pre id="code" style="text-align: left;max-height: 50vh;overflow: auto;"></pre>
            				</mdui-tab-panel>
            		</mdui-tab-panel>
        		</mdui-tabs>
    		</mdui-card>
		</div>
		<footer style="text-align: center;margin-top: 10%;">
			<span id="record">
			</span>
			<span id="copyright">
			</span>
			<p>
				本站内容由网友上传(或整理自网络)，原作者已无法考证，版权归原作者所有。仅供学习参考，其观点不代表本站立场，网站接口数据均收集互联网。
			</p>
		</footer>
		<script src="https://registry.npmmirror.com/mdui/2.0.3/files/mdui.global.js">
		</script>
	</body>

</html>