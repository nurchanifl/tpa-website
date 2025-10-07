<?php
// Path to the source image
$sourceImage = 'uploads/DSC09220.JPG';

// Check if source image exists
if (!file_exists($sourceImage)) {
    die("Source image not found.");
}

// Create directory if not exists
$iconDir = 'assets/icons/';
if (!is_dir($iconDir)) {
    mkdir($iconDir, 0755, true);
}

// Function to resize and save image
function resizeImage($source, $destination, $width, $height) {
    $imageInfo = getimagesize($source);
    $mime = $imageInfo['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    $resizedImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

    imagepng($resizedImage, $destination);
    imagedestroy($image);
    imagedestroy($resizedImage);
    return true;
}

// Generate icons
resizeImage($sourceImage, $iconDir . 'icon-192.png', 192, 192);
resizeImage($sourceImage, $iconDir . 'icon-512.png', 512, 512);

echo "Icons generated successfully.";
?>
