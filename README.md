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

	version 1.5 2018.12.02


##  Whats new

	version 1.5 2018.12.02
  + Added parameter $scrollingPostDelay for delay in the end of scrolling effect

	version 1.4 2018.11.28
  + Added delay in the end of scrolling effect


	version 1.3 2018.11.17
  + Added parameter outLine in function prepareSubtitles
  + Fixed bug with used font
  + Split function prepareSubtitles and scrollingText
  + Added demo with predefined styles demo_predefined_styles.php


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
$effect->prepareSubtitles(
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
    $scrollingPostDelay  );

$cmd = $effect->scrollingText(
    $bgImage,
    $audioFile,
    $temporaryAssFile,
    $output,
    $duration,
    $width,
    $height
);
$effect->doExec($cmd)
```
Additional settings and parameters you can see in `demo.php` and `demo_predefined_styles.php` file

### How to set right textBoxHeight and showLines value
Please be carefull with $fontSize, $textBoxHeight and $showLines. 
Those variables are relative and define speed of scrolling.
Unfortunately, several fonts may have another height in Linux, but in most cases you can set simple `$textBoxHeight=$showLines*$fontSize;`

for example `$fontSize=35`, we need 3 ( `$showLines=3` ) lines to will be shown, then
set `$textBoxHeight` to `3*35=105`


### How to set font color 
There used AARRGGBB color format with alpha channel ( eg `$fontColor = "&HD1CEE7"`) and you can use simple converter like http://www.netdelight.be/kml/index.php for converting from HTML to required color format.

### How to define additional styles
You can define own text styles ( font, fontSize, fontColor, bold, italic, etc ) with editor Aegisub (  http://www.aegisub.org/ )
Create new subtitles file, define own styles ( menu 'Subtitles'->'Style manager' ). Save subtitles, then cut styles
in any editor and use those lines for variable $additionalStyles.

### How to install and use new font ( Linux )
Font files that are placed in the hidden .fonts directory of your home folder will automatically be available.
Eg
```
cd ~
mkdir .fonfs
cd .fonfs
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
