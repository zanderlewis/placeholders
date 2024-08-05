<?php

header('Content-Type: image/png');

// Get canvas size from URL parameter (default to 8x8)
$canvas_size = isset($_GET['canvas_size']) ? intval($_GET['canvas_size']) : 8;

// Create a blank canvas image
$im = imagecreatetruecolor($canvas_size, $canvas_size);

// Set the background color
$bg = imagecolorallocate($im, 255, 255, 255);  // white
imagefill($im, 0, 0, $bg);

// Choose a random color with a preference for darker shades
$color = imagecolorallocate($im, rand(0, 127), rand(0, 127), rand(0, 127));

// Get symmetry type from URL parameter (default to vertical)
$symmetry = isset($_GET['type']) ? $_GET['type'] : 'vertical';

// Create an image with the specified symmetry
for ($i = 0; $i < ($canvas_size * $canvas_size) / 4; $i++) {
    $x = rand(0, ($canvas_size / 2) - 1);
    $y = rand(0, $canvas_size - 1);
    imagesetpixel($im, $x, $y, $color);

    if ($symmetry == 'vertical') {
        imagesetpixel($im, $canvas_size - 1 - $x, $y, $color); // Mirror to the right half
    } elseif ($symmetry == 'horizontal') {
        imagesetpixel($im, $x, $canvas_size - 1 - $y, $color); // Mirror to the bottom half
    } elseif ($symmetry == 'rotational') {
        imagesetpixel($im, $canvas_size - 1 - $x, $y, $color); // Mirror to the right half
        imagesetpixel($im, $x, $canvas_size - 1 - $y, $color); // Mirror to the bottom half
        imagesetpixel($im, $canvas_size - 1 - $x, $canvas_size - 1 - $y, $color); // Mirror to the bottom-right quarter
    }
}

// Get output size from URL parameter (default to 512x512)
$size = isset($_GET['size']) ? intval($_GET['size']) : 512;

// Upscale the image to the specified size
$im2 = imagecreatetruecolor($size, $size);
imagecopyresampled($im2, $im, 0, 0, 0, 0, $size, $size, $canvas_size, $canvas_size);
$im = $im2;

// Output the image
imagepng($im);

// Free up memory
imagedestroy($im);
imagedestroy($im2);
?>