<?php
/**
 * Robots txt wrapper for multi domains
 *
 * For Apache htaccess file:
 * ```
 * RewriteEngine On
 * RewriteRule ^robots.txt$ /components/com_mycityselector/robots.txt.php [QSA,L]
 * ```
 *
 * For Nginx config:
 * ```
 * server {
 *
 *      ... base instructions ..
 *
 *      location = /robots.txt {
 *          rewrite ^(.*)$ /components/com_mycityselector/robots.txt.php last;
 *      }
 * }
 * ```
 */

$host = $_SERVER['HTTP_HOST'];
$robotsTxt = __DIR__ . '/../../robots.txt';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
if (is_file($robotsTxt)) {
    $handle = fopen($robotsTxt, 'r');
    if ($handle) {
        $hostIsSent = false;
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (strtolower(substr($line, 0, 7)) == 'sitemap') {
                $defaultSiteMap = "Sitemap: {$protocol}{$host}/sitemap.xml\r\n";
                if (preg_match('/Sitemap:\s{1,4}(.+)$/i', $line, $result)) {
                    $info = parse_url($result[1]);
                    if (!empty($info['path'])) {
                        $query = empty($info['query']) ? '' : '?' . $info['query'];
                        echo 'Sitemap: ' . $protocol . $host . $info['path'] . $query . "\r\n";
                    } else {
                        echo $defaultSiteMap;
                    }
                } else {
                    echo $defaultSiteMap;
                }
            } else if (strtolower(substr($line, 0, 4)) == 'host') {
                echo "Host: {$protocol}{$host}\r\n";
                $hostIsSent = true;
            } else {
                if (substr($line, 0, 1) != '#' && !empty($line)) { // ignore comments and empty lines
                    echo $line . "\r\n";
                }
            }
        }
        fclose($handle);
        if (!$hostIsSent) {
            echo "Host: {$host}\r\n";
        }
        exit;
    }
}

echo "User-Agent: *\r\nHost: {$protocol}{$host}\r\n";
