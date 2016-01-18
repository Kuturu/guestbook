<?php 
 
$capletters = 'ABCDEFGKIJKLMNOPQRSTUVWXYZ123456789';  
$captlen = 4;  
$capwidth = 95; $capheight = 40;  
$capfont = 'font/font.ttf';  
$capfontsize = 20; 
header('Content-type: image/png');  
$capim = imagecreatetruecolor($capwidth, $capheight);  
imagesavealpha($capim, true);  
$capbg = imagecolorallocatealpha($capim, 0, 0, 0, 127); 
imagefill($capim, 0, 0, $capbg);  
$capcha = ''; 
for ($i = 0; $i < $captlen; $i++){ 
    $capcha .= $capletters[rand(0, strlen($capletters)-1) ];  
    $x = ($capwidth - 15) / $captlen * $i + 7;  
    $x = rand($x, $x+7);
    $y = $capheight - ( ($capheight - $capfontsize) / 2 );  
    $capcolor = imagecolorallocate($capim, rand(0, 100), rand(0, 100), rand(0, 100) );  
    $capangle = rand(-25, 25);  
    imagettftext($capim, $capfontsize, $capangle, $x, $y, $capcolor, $capfont, $capcha[$i]); 
}
if(!isset($_SESSION)){ 
    session_start(); 
} 
$_SESSION['c'] = $capcha; 
imagepng($capim);
imagedestroy($capim);