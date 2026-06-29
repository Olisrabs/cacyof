<?php
header('Content-Type: text/plain');

$zipFile = '../cacyof.zip';
if (!file_exists($zipFile)) {
    echo "Zip file not found at " . realpath($zipFile) . "\n";
    exit;
}

$zip = new ZipArchive();
if ($zip->open($zipFile) !== TRUE) {
    echo "Failed to open zip file\n";
    exit;
}

echo "Searching for files with 'images/' in name:\n";
for ($i = 0; $i < $zip->numFiles; $i++) {
    $name = $zip->getNameIndex($i);
    if (strpos($name, 'images/') !== false) {
        echo "$name\n";
    }
}
$zip->close();
?>
