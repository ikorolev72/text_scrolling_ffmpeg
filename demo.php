<?php

# This script demo for scrolling text video
# korolev-ia@yandex.ru
#

require_once "./FfmpegEffects.php";
##########################
# Set enviroment FONTCONFIG_FILE . This require for addTextAss effect
$basedir = dirname(__FILE__);
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    putenv("FONTCONFIG_FILE=$basedir\\fonts.conf"); # for windows
}

# new instance for FfmpegEffects
$effect = new FfmpegEffects();

$effect->setGeneralSettings(
    array(
        //'ffmpeg' => '/usr/bin/ffmpeg',
        'ffmpegLogLevel' => 'info',
        'showCommand' => false,
    )
);
echo "General settings:";
echo var_dump($effect->getGeneralSettings());

# set ffmpeg new audio output settings
$effect->setAudioOutputSettings(
    array(
        'codec' => 'aac',
        'bitrate' => '128k',
    )
);

echo "New settings for output audio ffmpeg:";
echo var_dump($effect->getAudioOutputSettings());

$effect->setVideoOutputSettings(
    array(
        'format' => 'mp4',
        'crf' => 20,
        'framerate' => 30,
        'preset' => 'veryfast',
    )
);

echo "Settings for output video ffmpeg:";
echo var_dump($effect->getVideoOutputSettings());

#$tempDir = sys_get_temp_dir();
#$tempDir = "/tmp";
$tempDir = ".";

// please remove next temporary file after ffmpeg processing ( eg @unlink( $temporaryAssFile  );
$temporaryAssFile = "$tempDir/" . time() . rand(10000, 99999) . ".ass";

$bgImage = "bg.jpg";
$textBoxWidth = 1000;

// WARNING
// Please be carefull with $fontSize, $textBoxHeight and $showLines
// those variables are relative and define speed of scrolling.
// Unfortunately, several fonts may have another height in Linux , but in most cases you can set simple  $textBoxHeight=$showLines*$fontSize;
//
// for example $fontSize=35, we need 3 ( $showLines=3 )lines to will be shown, then
// set $textBoxHeight to 3*35=105
//
$showLines = 3; // how many line will be shown in the scrolling window
$textBoxWidth = 700;
$textBoxHeight = 120; // usual it will be equivalent of lines * fontSize, for example lines=3, fontSize=35, textBoxHeight=3*35=105
$x = 50;
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
$duration = 25;
$scrollingDelay = 5;
$audioFile = "15sec.mp3";
$output = "output.mp4";
$width = 1280;
$height = 720;
$font = "Open Sans";
$fontSize = 40;
$fontColor = "&H00FFFFFF"; // please use KML format. For converting from rgb to KML you can use http://www.netdelight.be/kml/index.php
$styleBold = 1;
$styleItalic = 0;
$outLine = 0.4;
$scrollingPostDelay = 2;

$additionalStyles = "";
$useStyle = "Default";

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
    $outLine,
    $scrollingPostDelay)
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
