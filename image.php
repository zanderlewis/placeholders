<?php

header('Content-Type: image/png');

if (!isset($_GET['type'])) {
    $TYPE = 'retro';
} else {
    $TYPE = $_GET['type'];
}

if ($TYPE === 'retro') {
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
    $symmetry = isset($_GET['symmetry']) ? $_GET['symmetry'] : 'vertical';

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
} elseif ($TYPE === 'random') {
    // Get canvas size from URL parameter (default to 8x8)
    $size = isset($_GET['canvas_size']) ? intval($_GET['canvas_size']) : 8;

    // Create a blank canvas image
    $im = imagecreatetruecolor($size, $size);

    // Set the background color
    $bg = imagecolorallocate($im, 255, 255, 255);  // white
    imagefill($im, 0, 0, $bg);

    // Choose a random color
    // Favoring darker shades
    $color = imagecolorallocate($im, rand(0, 127), rand(0, 127), rand(0, 127));

    // Fill the canvas with random pixels
    for ($x = 0; $x < $size; $x++) {
        for ($y = 0; $y < $size; $y++) {
            if (rand(0, 1) == 1) {
                imagesetpixel($im, $x, $y, $color);
            }
        }
    }

    // Get output size from URL parameter (default to 512x512)
    $output_size = isset($_GET['size']) ? intval($_GET['size']) : 512;

    // Upscale the image to the specified size
    $im2 = imagecreatetruecolor($output_size, $output_size);
    imagecopyresampled($im2, $im, 0, 0, 0, 0, $output_size, $output_size, $size, $size);
    $im = $im2;

    // Output the image
    imagepng($im);

    // Free up memory
    imagedestroy($im);
    imagedestroy($im2);
} elseif ($TYPE === 'maze') {
    // Get canvas size from URL parameter (default to 256x256)
    $canvas_size = isset($_GET['canvas_size']) ? intval($_GET['canvas_size']) : 256;

    // Create a blank canvas image
    $im = imagecreatetruecolor($canvas_size, $canvas_size);

    // Set the background color
    $bg = imagecolorallocate($im, 255, 255, 255);  // white
    imagefill($im, 0, 0, $bg);

    // Set the wall color
    $wall_color = imagecolorallocate($im, 0, 0, 0);  // black

    // Create a maze using the recursive backtracking algorithm
    $maze = array_fill(0, $canvas_size, array_fill(0, $canvas_size, 1));
    $stack = array();
    $x = rand(0, $canvas_size - 1);
    $y = rand(0, $canvas_size - 1);
    $maze[$x][$y] = 0;
    array_push($stack, array($x, $y));
    
    while (!empty($stack)) {
        $neighbors = array();
        if ($x > 1 && $maze[$x - 2][$y] == 1) {
            array_push($neighbors, array($x - 2, $y));
        }
        if ($x < $canvas_size - 2 && $maze[$x + 2][$y] == 1) {
            array_push($neighbors, array($x + 2, $y));
        }
        if ($y > 1 && $maze[$x][$y - 2] == 1) {
            array_push($neighbors, array($x, $y - 2));
        }
        if ($y < $canvas_size - 2 && $maze[$x][$y + 2] == 1) {
            array_push($neighbors, array($x, $y + 2));
        }
        
        if (!empty($neighbors)) {
            array_push($stack, array($x, $y));
            $neighbor = $neighbors[array_rand($neighbors)];
            $maze[($x + $neighbor[0]) / 2][($y + $neighbor[1]) / 2] = 0;
            $maze[$neighbor[0]][$neighbor[1]] = 0;
            $x = $neighbor[0];
            $y = $neighbor[1];
        } else {
            $cell = array_pop($stack);
            $x = $cell[0];
            $y = $cell[1];
        }
    }

    // Draw the maze on the canvas
    for ($x = 0; $x < $canvas_size; $x++) {
        for ($y = 0; $y < $canvas_size; $y++) {
            if ($maze[$x][$y] == 1) {
                imagesetpixel($im, $x, $y, $wall_color);
            }
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
}