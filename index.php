<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

session_start();

function getClient()
{
    $client = new Client();
    $client->setApplicationName('Google Drive API PHP Quickstart');
    $client->setScopes(Drive::DRIVE_METADATA_READONLY);
    $client->setAuthConfig('env.json');
    $client->setAccessType('offline');
    $client->setRedirectUri('http://localhost:8080/callback.php');

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
    }

    return $client;
}

if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] == '') {
    $client = getClient();
    $authUrl = $client->createAuthUrl();
    echo '<a href="' . htmlspecialchars($authUrl) . '"><button>Log in with Google</button></a>';
} else {
    header('Location: list_files.php');
    exit();
}
