<?php
$source = 'public/logo.png';
$dest192 = 'public/icons/icon-192x192.png';
$dest512 = 'public/icons/icon-512x512.png';

// Create image from png
$img = imagecreatefrompng($source);

// Get dimensions
$width = imagesx($img);
$height = imagesy($img);

// Create 192x192
$img192 = imagecreatetruecolor(192, 192);
imagealphablending($img192, false);
imagesavealpha($img192, true);
$transparent = imagecolorallocatealpha($img192, 255, 255, 255, 127);
imagefilledrectangle($img192, 0, 0, 192, 192, $transparent);
imagecopyresampled($img192, $img, 0, 0, 0, 0, 192, 192, $width, $height);
imagepng($img192, $dest192);

// Create 512x512
$img512 = imagecreatetruecolor(512, 512);
imagealphablending($img512, false);
imagesavealpha($img512, true);
$transparent512 = imagecolorallocatealpha($img512, 255, 255, 255, 127);
imagefilledrectangle($img512, 0, 0, 512, 512, $transparent512);
imagecopyresampled($img512, $img, 0, 0, 0, 0, 512, 512, $width, $height);
imagepng($img512, $dest512);

echo "Icons generated!\n";
