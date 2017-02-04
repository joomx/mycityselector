<?php
/*
 * CURL helper
 */

namespace adamasantares\jexter;

if (!defined('JEXTER_DIR')) {
    define('JEXTER_DIR', realpath(__DIR__ . '/../'));
}

function curl_request($url, $get = [], $post = [])
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, JEXTER_DIR . '/tmp/cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEJAR, JEXTER_DIR . '/tmp/cookie.txt');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    if (!empty($post)) {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    } else {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        if (!empty($get)) {
            $url .= '?' . http_build_query($get);
        }
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    $out = curl_exec($curl);
    $code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return ['response' => $out, 'code' => $code];
}


function curl_githubGetLastReleases($repo)
{
    $json = [];
    $url = "https://api.github.com/repos/{$repo}/releases/latest";
    $data = curl_request($url);
    if ($data['code'] == 200) {
        $json = json_decode($data['response'], true);
        if ($json === null) {
            $json = [];
        }
    }
    return $json;
}