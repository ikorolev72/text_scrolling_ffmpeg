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

	version 1.2 2018.11.09


##  Whats new
	version 1.2 2018.11.09
  + Added parameters FontColor, styleBold, styleItalic, showLines in function
  + Fixed bug with used font
  + Removed 'hard spaces' in begining of text


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
    $temporaryAssFile,
    $width, 
    $height,
    $font,
    $fontSize,
    $fontColor,
    $styleBold,
    $styleItalic,
    $showLines        
);
$effect->doExec($cmd)
```
Additional settings and parameters you can see in `demo.php` file


### How to set font color 
There used AARRGGBB color format with alpha channel ( eg `$fontColor = "&HD1CEE7"`) and you can use simple converter like http://www.netdelight.be/kml/index.php for converting from HTML to required color format.


### How to install and use new font ( Linux )
Font files that are placed in the hidden .fonts directory of your home folder will automatically be available.
Eg
```
cd ~
mkdir .fonfs
wget https://github.com/google/fonts/raw/master/apache/opensans/OpenSans-Regular.ttf
fc-list | grep -i OpenSans
```
You need use system name of font in your script, for example
```
$ fc-list |grep -i open
/home/ubuntu/.fonts/OpenSans-Regular.ttf: Open Sans:style=Regular
$ fc-list |grep -i roboto
/usr/share/fonts/truetype/roboto/hinted/RobotoCondensed-Regular.ttf: Roboto Condensed:style=Regular
```
In your script you need use second field of `fc-info` output. Eg `$font='Open Sans';` or `$font='Roboto Condensed';`



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
