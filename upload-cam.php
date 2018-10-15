<?php
namespace App;
use App\UploadException;

require 'vendor/autoload.php';
$directoryBase = "./";
$uploadBase = "uploads/seafile/";
if ($_FILES["upload"]["error"] > 0)
{
    throw new UploadException($_FILES['upload']['error']);
} else {
    $fileName = $_FILES["upload"]["name"];
    $explodeFile = explode(".", $fileName);
    $extension = end($explodeFile);
    $directoryDate = $uploadBase . date('Y-m-d') . "/";
    $uploadedName = date('l-H-i-s').".".$extension;
// Check if directory exists if not create it
    if (!is_dir($directoryDate)) {
        mkdir($directoryDate);
    }
    $uploaded = move_uploaded_file($_FILES["upload"]["tmp_name"], $directoryDate.$uploadedName);
    if ($uploaded) {
        $imageLink = "http://".$_SERVER['HTTP_HOST']."/".$directoryBase.$directoryDate.$uploadedName;
    } else {
        $imageLink = "http://".$_SERVER['HTTP_HOST']."/".$directoryBase."gallery/assets/error-uploading.png";
    }
    echo $imageLink;
}

if (!file_exists($directoryDate.$uploadedName)) {
    exit("File could not be uploaded in ".$directoryDate.$uploadedName);
}
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


$libraryResource = new \Seafile\Client\Resource\Library($client);

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
$library = $libraryResource->getById($repositoryId);
$directoryResource = new \Seafile\Client\Resource\Directory($client);
$directory = date($dateFormat);

if ($directoryResource->exists($library, $directory) === false) {
    //  directory item does not exist, create it
    $recursive = false; // recursive will create parentDir if not already existing
    $success = $directoryResource->create($library, $directory, $parentDir, $recursive);
}


$fileResource = new \Seafile\Client\Resource\File($client);
$response = $fileResource->upload($library, $directoryDate.$uploadedName, $directory);
$uploadedFileId = json_decode((string)$response->getBody());
echo $uploadedFileId;
