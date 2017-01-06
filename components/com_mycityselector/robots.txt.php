<?php
/**
 * Robots txt wrapper for multi domains
 *
 * For Apache htaccess file:
 * ```
 * RewriteEngine On
 * RewriteRule ^robots.txt$ /components/com_mycityselector/robots.txt.php
 * ```
 *
 * For Nginx config:
 * ```
 * server {
 *
 *      ... base instructions ..
 *
 *      location /robots.txt {
 *          rewrite "^.*+$" /components/com_mycityselector/robots.txt.php;
 *      }
 * }
 * ```
 */

$robotsTxt = __DIR__ . '/../../robots.txt';
if (is_file($robotsTxt)) {
    $handle = fopen($robotsTxt, 'r');
    if ($handle) {
        $hostIsSent = false;
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (strtolower(substr($line, 0, 4)) == 'host') {
                echo "Host: {$_SERVER['HTTP_HOST']}\n";
                $hostIsSent = true;
            } else {
                if (substr($line, 0, 1) != '#' && !empty($line)) { // ignore comments and empty lines
                    echo $line . "\n";
                }
            }
        }
        fclose($handle);
        if (!$hostIsSent) {
            echo "Host: {$_SERVER['HTTP_HOST']}\n";
        }
        exit;
    }
}
// else
echo "User-Agent: *\nHost: {$_SERVER['HTTP_HOST']}\n";

