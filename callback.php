<?php
require 'vendor/autoload.php';


use Google\Client;

session_start();

function getClient()
{
    $client = new Client();
    $client->setApplicationName('Google Drive API PHP Quickstart');
    $client->setScopes(Google\Service\Drive::DRIVE_METADATA_READONLY);
    $client->setAuthConfig('env.json');
    $client->setAccessType('offline');
    $client->setRedirectUri('http://localhost:8888/callback.php');

    return $client;
}

$client = getClient();


if (!isset($_GET['code'])) {
    header('Location: index.php');
    exit();
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (array_key_exists('error', $token)) {
    throw new Exception(join(', ', $token));
}

$_SESSION['access_token'] = $token;
header('Location: list_files.php');
