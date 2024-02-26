<?php
/**
 * Sitemap.xml wrapper for multi domains
 *
 * For Apache htaccess file:
 * ```
 * RewriteEngine On
 * RewriteRule ^robots.txt$ /components/com_mycityselector/robots.txt.php [QSA,L]
 * RewriteRule ^sitemap(.*).xml$ /components/com_mycityselector/sitemap.xml.php [QSA,L]
 * ```
 *
 * For Nginx config:
 * ```
 * server {
 *
 *      ... base instructions ..
 *
 *      location ~ ^(.*)sitemap(.*)\.xml$ {
 *        rewrite ^(.*)$ /components/com_mycityselector/sitemap.xml.php last;
 *      }
 * }
 * ```
 */

$host = $_SERVER['HTTP_HOST'];
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
$fileName = $_SERVER['REQUEST_URI'];
$sitemapXml = realpath(__DIR__ . '/../..' . $fileName);

if (is_file($sitemapXml)) {
    $xml = simplexml_load_file($sitemapXml);
    foreach($xml->url as $values) {
        $_url = parse_url($values->loc);
        $values->loc = $_url['scheme'] . '://' . $host . $_url['path'];
    }
    header('Content-Type: text/xml');
    echo $xml->asXML();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "File not found.";
}
exit;
