<?php

# This script stitch video parts to entire video
# korolev-ia@yandex.ru
#

require_once "./FfmpegEffects.php";
##########################
# Set enviroment FONTCONFIG_FILE . This require for addTextAss effect
$basedir = dirname(__FILE__);


/*
$options = getopt('j:o:');

$json_file = isset($options['j']) ? $options['j'] : false;
$output = isset($options['o']) ? $options['o'] : false;

if (!$json_file) {
    help("Need parameter json file ( -j file.json ) ");
}
if (!$output) {
  help("Need parameter output file ( -o output_file.mp4 ) ");
}


if (!file_exists($json_file)) {
    help("File $json_file do not exist");
}

$string = file_get_contents($json_file);
$params = json_decode($string, true);
if (!$params) {
    help("Cannot decode json from $json_file");
}
*/

# new instance for FfmpegEffects
$effect = new FfmpegEffects();

$effect->setGeneralSettings(
    array(
        'ffmpegLogLevel' => 'info',
        'showCommand' => false
    )
);
#echo "General settings:";
#echo var_dump($effect->getGeneralSettings());


# set ffmpeg new audio output settings
$effect->setAudioOutputSettings(
    array(
        'direct' => ' -c:a copy ',
    )
);
#echo "New settings for output audio ffmpeg:";
#echo var_dump($effect->getAudioOutputSettings());



# we must split with slow speed and hi quality
$effect->setVideoOutputSettings(
    array(
      'direct' => ' -c:v copy -f mp4 ',
    )
);

echo "Settings for output audio ffmpeg:";
echo var_dump($effect->getAudioOutputSettings());

echo "Settings for output video ffmpeg:";
echo var_dump($effect->getVideoOutputSettings());



$cmd = $effect->scrollingText (
	$input,
	$inputAudio,
	$output
) ;


if (!$cmd) {
  echo $effect->getLastError();
  exit(1);
}
if (!$effect->doExec($cmd)) {
  $effect->writeToLog("Someting wrong: $cmd");
  exit(1);
}

exit(0);

function help($msg)
{
    fwrite(STDERR,
        "$msg
	Usage:$0 -i bg.png -o output_file.mp4
	\n");
    exit(-1);
}
