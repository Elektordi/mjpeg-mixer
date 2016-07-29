<?php

$dir = "/home/photos-videos/2016/Low Quality";

# Used to separate multipart
$boundary = "Ba4oTvQMY8ew04N8dcnM";

# We start with the standard headers. PHP allows us this much
header("Cache-Control: no-cache");
header("Cache-Control: private");
header("Pragma: no-cache");
header("Content-type: multipart/x-mixed-replace; boundary=$boundary");

# From here out, we no longer expect to be able to use the header() function
print "--$boundary\n";

# Set this so PHP doesn't timeout during a long stream
set_time_limit(0);

# Disable Apache and PHP's compression of output to the client
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);

# Set implicit flush, and flush all current buffers
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++)
    ob_end_flush();
ob_implicit_flush(1);

# The loop, producing one jpeg frame per iteration
while (true) {
    # Your function to get one jpeg image
    $images = glob($dir . '/*.{jpg,jpeg}', GLOB_BRACE);
    $randomImage = $images[array_rand($images)];
    $img = imagecreatefromjpeg($randomImage);
    $w = imagesx($img);
    $h = imagesy($img);
    if($w<$h) {
        imagedestroy($img);
        continue;
    }
    $img2 = imagecreatetruecolor(1024,768);    
    imagecopyresampled($img2, $img, 0, 0, 0, 0, 1024, 768, $w, $h);

    # Per-image header, note the two new-lines
    print "Content-type: image/jpeg\n\n";
    print imagejpeg($img2);
    print "--$boundary\n";

    imagedestroy($img);
    imagedestroy($img2);

    sleep(2);
}
?>

