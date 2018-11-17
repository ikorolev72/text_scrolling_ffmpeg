<?php

# This script demo for scrolling text video
# korolev-ia@yandex.ru
#
/* 
This demo show how to use prdefined styles 
You can use editor Aegisub (  http://www.aegisub.org/ )
for definition of styles
*/ 



require_once "./FfmpegEffects.php";
##########################
# Set enviroment FONTCONFIG_FILE . This require for addTextAss effect
$basedir = dirname(__FILE__);
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    putenv("FONTCONFIG_FILE=$basedir\\fonts.conf"); # for windows
}

# new instance for FfmpegEffects
$effect = new FfmpegEffects();

echo "Settings for output video ffmpeg:";
echo var_dump($effect->getVideoOutputSettings());

$tempDir = ".";

// please remove next temporary file after ffmpeg processing ( eg @unlink( $temporaryAssFile  );
$temporaryAssFile = "$tempDir/" . time() . rand(10000, 99999) . ".ass";

$bgImage = "bg.jpg";

// WARNING
// Please be carefull with $fontSize, $textBoxHeight and $showLines
// those variables are relative and define speed of scrolling.
// Unfortunately, several fonts may have another height in Linux , but in most cases you can set simple $textBoxHeight=$showLines*$fontSize;
//
// for example $fontSize=35, we need 3 ( $showLines=3 )lines to will be shown, then
// set $textBoxHeight to 3*35=105
//


$showLines = 6; // how many line will be shown in the scrolling window
$textBoxHeight = 150; // usual it will be equivalent of lines * fontSize, for example lines=3, fontSize=35, textBoxHeight=3*35=105
$textBoxWidth = 700;
$x = 555;
$y = 470;

$text = "I saw your ffmpeg / php work on github and you're probably perfect for a project. I'm looking for someone to make a php class with a function to create a video with an animated text box (scroll down to up).
The parameters for input should be:
    - image file to be used as background
    - text box dimensions (height / width in px)
    - text box position of top left corner ( x / y in pixels)
    - text to be used (left indented)
    - total length of the movie in seconds
    - delay scrolling at the begining ( ex. 5 seconds)
    - file name for the new video";

$text = wordwrap($text, 60); // please wrap very long lines in your text
$duration = 20;
$scrollingDelay = 5;
$audioFile = "15sec.mp3";
$output = "output.mp4";
$width = 1280;
$height = 720;


// Style 'Default' will be set with parameters in function prepareSubtitles
// $additionalStyles predefined styles you can use each of them  with variable $useStyle
// $useStyle="Verdana_bold_35";
// if you use predefined styles, next parameters do not used $font, $fontColor, $fontSize, $styleBold, $styleItalic, $outLine
$additionalStyles =
    "Style: Arial_bold_35,Arial,25,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,-1,0,0,0,100,100,0,0,1,0,0,7,10,10,10,1
Style: OpenSans_25,Open Sans,25,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,0,0,0,0,100,100,0,0,1,0,0,7,10,10,10,1
Style: OpenSans_bold_25,Open Sans,25,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,-1,0,0,0,100,100,0,0,1,0,0,7,10,10,10,1
Style: Verdana_35 ,Verdana,35,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,0,0,0,0,100,100,0,0,1,0,0,7,10,10,10,1
Style: Arial_35,Arial,25,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,0,0,0,0,100,100,0,0,1,0,0,7,10,10,10,1
Style: Verdana_bold_35,Verdana,35,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,-1,0,0,0,100,100,0,0,1,0,0,7,10,10,10,1
";
$useStyle = "OpenSans_bold_25";

// prepare SSA/ASS file
if (!$effect->prepareSubtitles(
    $textBoxWidth,
    $textBoxHeight,
    $x,
    $y,
    $text,
    $duration,
    $scrollingDelay,
    $temporaryAssFile,
    $width,
    $height,
    $additionalStyles,
    $useStyle,
    $font,
    $fontSize,
    $fontColor,
    $styleBold,
    $styleItalic,
    $showLines,
    $outLine)
) {
    echo $effect->getLastError();
    @unlink($temporaryAssFile);
    exit(1);
}

$cmd = $effect->scrollingText(
    $bgImage,
    $audioFile,
    $temporaryAssFile,
    $output,
    $duration,
    $width,
    $height
);

if (!$cmd) {
    echo $effect->getLastError();
    @unlink($temporaryAssFile);
    exit(1);
}
if (!$effect->doExec($cmd)) {
    $effect->writeToLog("Someting wrong: $cmd");
    exit(1);
}
@unlink($temporaryAssFile);
exit(0);
