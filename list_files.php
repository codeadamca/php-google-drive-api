<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Drive Files</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .file-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            width: 18rem;
            margin: 20px;
        }

        .card-img-top {
            width: 100%;
            height: auto;
        }

        .card-body {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class='file-container'>
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
                } else {
                    header('Location: index.php');
                    exit();
                }

                return $client;
            }

            function getFolderIdByName($service, $folderName)
            {
                $escapedFolderName = addslashes($folderName);
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
                    return $results->getFiles()[0]->getId();
                }
            }

            function listFilesInFolder($service, $folderId, $indentation = 0)
            {
                $optParams = array(
                    'q' => "'$folderId' in parents",
                    'pageSize' => 100,
                    'fields' => 'nextPageToken, files(id, name, mimeType, createdTime, thumbnailLink, webViewLink)',
                );

                $results = $service->files->listFiles($optParams);

                if (count($results->getFiles()) == 0) {
                    return;
                }

                // Generate the card layout
                foreach ($results->getFiles() as $file) {
                    echo generateFileCard($file, $indentation);

                    // If the file is a folder, recurse into it
                    if ($file->getMimeType() === 'application/vnd.google-apps.folder') {
                        listFilesInFolder($service, $file->getId(), $indentation + 1);
                    }
                }
            }

            function generateFileCard($file)
            {
                $name = $file->getName();
                $createdTime = date('F j, Y', strtotime($file->getCreatedTime()));
                $moreInfoLink = $file->getWebViewLink();

                return "
    <div class='card'>
        <div class='card-body'>
            <h5 class='card-title'>$name</h5>
            <p class='card-text'>Added on: $createdTime</p>
            <a href='$moreInfoLink' target='_blank' class='btn btn-primary'>More Info</a>
        </div>
    </div>
    ";
            }

            // Entry point: List all files starting from the root folder
            $client = getClient();
            $service = new Drive($client);
            $rootFolderId = getFolderIdByName($service, 'IB question bank');

            if ($rootFolderId !== null) {
                listFilesInFolder($service, $rootFolderId);
            } else {
                echo "Root folder not found.";
            } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>