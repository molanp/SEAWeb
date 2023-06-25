<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$path = $_SERVER['DOCUMENT_ROOT'].'/sitemap.xml';
header('Content-Type: application/xml');
// 检查缓存是否存在
if (file_exists($path) && filemtime($path) > strtotime('-1 day')) {
    // 如果缓存存在且在一天内更新过，则直接输出缓存内容
    readfile($path);
    exit;
}

// 创建XML文档对象
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

// 创建根节点
$urlset = $xml->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->setAttribute('xmlns:mobile','http://www.sitemaps.org/schemas/sitemap-mobile/1');

$xml->appendChild($urlset);

// 获取网站内容，例如从数据库或文件中读取
$pages = [];
$data = curl_get('http://'.$_SERVER['HTTP_HOST'].'/v2/info');
foreach($data['data'] as $type){
    foreach($type as $info){
        $pages[] = ['url'=>'http://'.$_SERVER['HTTP_HOST'].'/'.$info['path'], 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily'];
    }
}

// 遍历每个页面生成URL节点
foreach ($pages as $page) {
    $url = $xml->createElement('url');

    $loc = $xml->createElement('loc', $page['url']);
    $url->appendChild($loc);

    $lastmod = $xml->createElement('lastmod', $page['lastmod']);
    $url->appendChild($lastmod);

    $changefreq = $xml->createElement('changefreq', $page['changefreq']);
    $url->appendChild($changefreq);

    $urlset->appendChild($url);
}
// 保存XML文件
$xml->save($path);
readfile($path);
exit;