<?php
header('Content-Type: text/plain');

$zipFile = '../cacyof.zip';
if (!file_exists($zipFile)) {
    echo "Zip file not found\n";
    exit;
}

$zip = new ZipArchive();
if ($zip->open($zipFile) !== TRUE) {
    echo "Failed to open zip file\n";
    exit;
}

$extracted = 0;
for ($i = 0; $i < $zip->numFiles; $i++) {
    $name = $zip->getNameIndex($i);
    // If it contains cacyof/images/
    if (strpos($name, 'cacyof/images/') === 0 || strpos($name, 'images/') === 0) {
        $target = './';
        if (strpos($name, 'cacyof/') === 0) {
            $target = '../';
        }
        if ($zip->extractTo($target, $name)) {
            $extracted++;
        }
    }
}
echo "Extracted $extracted images.\n";
$zip->close();
?>
