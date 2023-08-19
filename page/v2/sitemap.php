<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$path = $_SERVER['DOCUMENT_ROOT'].'/sitemap.xml';
header('Content-Type: application/xml');
if (file_exists($path) && filemtime($path) > strtotime('-1 day')) {
    readfile($path);
    exit;
}
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;
$urlset = $xml->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->setAttribute('xmlns:mobile','http://www.sitemaps.org/schemas/sitemap-mobile/1');
$xml->appendChild($urlset);
$pages = [];
$data = requests->get('http://'.$_SERVER['HTTP_HOST'].'/v2/info')->json();
foreach($data['data'] as $type){
    foreach($type as $info){
        $pages[] = ['url'=>'http://'.$_SERVER['HTTP_HOST'].$info['path'], 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily'];
    }
}
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
$xml->save($path);
