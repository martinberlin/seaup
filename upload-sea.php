<?php 
require 'vendor/autoload.php';

$dateFormat = 'Y-m-d';
$token = isset($_GET['token']) ? $_GET['token'] : null;
$repositoryId = isset($_GET['repository_id']) ? $_GET['repository_id'] : null;

if (is_null($token) && is_null($repositoryId)) {
    if (file_exists('sea-config.json')) {
        $config = json_decode(file_get_contents('sea-config.json'));
        if (is_null($config)) exit('sea-config JSON cannot be parsed');

        $token = $config->token;
        $repositoryId = $config->repository_id;

    } else {
        exit('token & repository_id not provided by GET parameters / sea-config.json not found');
    }
}

$client = new \Seafile\Client\Http\Client(
    [
        'base_uri' => $config->seafile_host,
        'debug' => false,
        'headers' => [
            'Authorization' => 'Token ' . $token
        ]
    ]
);

#LISTS Libraries
if (isset($_GET['list'])) {
    $libs = $libraryResource->getAll();
    foreach ($libs as $lib) {
        printf("Name: %s, ID: %s, is encrypted: %s\n", $lib->name, $lib->id, $lib->encrypted ? 'YES' : 'NO');
    }
    exit();
}

//UPLOAD
$parentDir = '';
$libraryResource = new \Seafile\Client\Resource\Library($client);
$library = $libraryResource->getById($repositoryId);
$directoryResource = new \Seafile\Client\Resource\Directory($client);
$directory = date($dateFormat);

if ($directoryResource->exists($library, $directory) === false) {
    //  directory item does not exist, create it
    $recursive = false; // recursive will create parentDir if not already existing
    $success = $directoryResource->create($library, $directory, $parentDir, $recursive);
}


$fileResource = new \Seafile\Client\Resource\File($client);
$fileToUpload = 'composer.json';
$response = $fileResource->upload($library, $fileToUpload, $directory);
$uploadedFileId = json_decode((string)$response->getBody());
echo $uploadedFileId;
