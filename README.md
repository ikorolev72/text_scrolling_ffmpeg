#						Scrolling text video effect

##  What is it?
##  -----------
Php class prepare ffmpeg command for text scrolling effect.
The parameters:
- image file to be used as background
- text box dimensions (height / width in px)
- text box position of top left corner ( x / y in pixels)
- text to be used (left indented)
- total length of the movie in seconds
- delay scrolling at the begining ( ex. 5 seconds)
- file name for the new video
- audio file


##  The Latest Version

	version 1.1 2018.11.08


##  Whats new
	version 1.1 2018.11.08
  + Added scrollingText function and demo

	version 1.0 2018.11.06
  + Initial version


##  How to install


## How to run
```
git clone https://github.com/ikorolev72/text_scrolling_ffmpeg.git
cd text_scrolling_ffmpeg
php demo.php
```


## How to use
Mostly this will be looks like this
```
require_once "./FfmpegEffects.php";
$effect = new FfmpegEffects();
$cmd = $effect->scrollingText(
    $bgImage,
    $textBoxWidth,
    $textBoxHeight,
    $x,
    $y,
    $text,
    $duration,
    $scrollingDelay,
    $audioFile,
    $output,
    $width,
    $height,
    $temporaryAssFile,
    $font,
    $fontSize
);
$effect->doExec($cmd)
```
Additional settings and parameters you can see in `demo.php` file


##  Bugs
##  ------------
In Windows, text rendering require enviroment variable FONTCONFIG_FILE . Please set this variable before run.
eg
```
c:> set FONTCONFIG_FILE=d:\tools\ffmpeg\conf\fonts.conf
```


##  Licensing
  ---------
	GNU

  Contacts
  --------

     o korolev-ia [at] yandex.ru
     o http://www.unixpin.com
