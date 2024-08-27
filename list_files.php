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
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setRedirectUri('http://localhost:8888/callback.php');

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
    } else {
        header('Location: index.php');
        exit();
    }

    return $client;
}


function getFolderIdByName($service, $folderName)
{
    // Escape single quotes in the folder name
    $escapedFolderName = addslashes($folderName);

    // Correctly quote the string
    $query = sprintf("name = '%s' and mimeType = 'application/vnd.google-apps.folder'", $escapedFolderName);

    $optParams = array(
        'q' => $query,
        'fields' => 'files(id, name)',
        'spaces' => 'drive',
    );

    $results = $service->files->listFiles($optParams);

    if (count($results->getFiles()) == 0) {
        return null;
    } else {
        // Assuming folder names are unique, return the ID of the first match
        return $results->getFiles()[0]->getId();
    }
}



function listFilesInFolder($folderName)
{
    $client = getClient();
    $service = new Drive($client);

    // Get the folder ID by name
    $folderId = getFolderIdByName($service, $folderName);

    if ($folderId === null) {
        print "Folder '$folderName' not found.\n";
        return;
    }

    $optParams = array(
        'q' => "'$folderId' in parents",
        'pageSize' => 100,
        'fields' => 'nextPageToken, files(id, name, mimeType)',
    );

    $results = $service->files->listFiles($optParams);

    if (count($results->getFiles()) == 0) {
        print "No files found in the folder '$folderName'.\n";
    } else {
        print "Files in folder '$folderName':<br>";
        foreach ($results->getFiles() as $file) {
            printf("%s (%s) - %s<br>", $file->getName(), $file->getId(), $file->getMimeType());
        }
    }
}

// Call the function to list files in a specific folder
listFilesInFolder('BrickMMO');
