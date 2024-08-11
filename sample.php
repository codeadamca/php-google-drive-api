<?php

session_start();

// Get .env Variables
$env = file(__DIR__.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);


$url = 'https://www.googleapis.com/drive/v3/about';

echo $url.'<br>';

// $headers[] = 'Content-type: application/json';
// $headers[] = 'Authorization: Bearer '.GITHUB_ACCESS_TOKEN;
// $headers[] = 'User-Agent: Awesome-Octocat-App';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

echo '<pre>';
print_r($result);
echo '</pre>';

curl_close($ch);