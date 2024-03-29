<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

req_log();
$path = $_SERVER["DOCUMENT_ROOT"] . "/sitemap.xml";
header("Content-Type: application/xml");
if (file_exists($path) && filemtime($path) > strtotime("-1 day")) {
    exit;
}
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
$stmt = $DATABASE->query("SELECT url_path FROM api");
$urls = $stmt->fetchAll(PDO::FETCH_COLUMN);
if ($urls) {
    $xml = new DOMDocument("1.0", "UTF-8");
    $xml->formatOutput = true;
    $urlset = $xml->createElement("urlset");
    $urlset->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
    $urlset->setAttribute("xmlns:mobile", "http://www.sitemaps.org/schemas/sitemap-mobile/1");
    $xml->appendChild($urlset);
    $pages = [];
    foreach ($urls as $url) {
        $pages[] = ["url" => "http://" . $_SERVER["HTTP_HOST"] . "/docs" . $url, "lastmod" => date("Y-m-d"), "changefreq" => "daily"];
    }
    foreach ($pages as $page) {
        $url = $xml->createElement("url");
        $loc = $xml->createElement("loc", $page["url"]);
        $url->appendChild($loc);
        $lastmod = $xml->createElement("lastmod", $page["lastmod"]);
        $url->appendChild($lastmod);
        $changefreq = $xml->createElement("changefreq", $page["changefreq"]);
        $url->appendChild($changefreq);
        $urlset->appendChild($url);
    }
    $xml->save($path);
}
