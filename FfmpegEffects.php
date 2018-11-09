<?php
/**
 *
 *
 * This class is the wrapper for ffmpeg ( http://ffmpeg.org )
 * and have several function for effects, like
 * transitions, mix audio, etc
 * @author korolev-ia [at] yandex.ru
 * @version 3.0.7
 */

class FfmpegEffects
{

    private $ffmpegSettings = array();
    private $error; # last error

    public function __construct()
    {

# GENERAL settinds
        $this->ffmpegSettings['general'] = array();
        $this->ffmpegSettings['general']['showCommand'] = true;
        $this->ffmpegSettings['general']['ffmpegLogLevel'] = 'info'; # info warning error fatal panic verbose debug trace
        $this->ffmpegSettings['general']['ffmpeg'] = "ffmpeg";
        $this->ffmpegSettings['general']['ffprobe'] = "ffprobe";

# AUDIO settinds
        $this->ffmpegSettings['audio'] = array();

        ################# direct audio settings #################
        # you can use 'direct audio settings' string for audio settings,
        # in this case all other audio settings will be ignored
        //$this->ffmpegSettings['audio']['direct']=" -c:a aac -b:a 160k -ac 1 ";
        ################# end of direct audio settings #################

        ################# copy settings #################
        // $this->ffmpegSettings['audio']['codec']="copy" ;        # copy existing audio cahnnels to output file, without transcoding ( -c:a copy )
        ################# end of copy settings #################

        $this->ffmpegSettings['audio']['channels'] = 2; # stereo # https://trac.ffmpeg.org/wiki/AudioChannelManipulation
        ################# aac settings #################
        $this->ffmpegSettings['audio']['codec'] = "aac"; # https://trac.ffmpeg.org/wiki/Encode/AAC                                                            # used native encoder/decoder
        $this->ffmpegSettings['audio']['bitrate_mode'] = "cbr"; # Constant Bit Rate (CBR) mode
        $this->ffmpegSettings['audio']['bitrate'] = "160k"; # hi quality ( -c:a aac -b:a 484k )
        ################# end of aac settings #################

        ################# mp3 settings #################
        //$this->ffmpegSettings['audio']['codec']="mp3";        # https://trac.ffmpeg.org/wiki/Encode/MP3
        //$this->ffmpegSettings['audio']['bitrate_mode']="cbr";    # Constant Bit Rate (CBR) mode
        //$this->ffmpegSettings['audio']['bitrate']="320k";        # hi quality ( -c:a mp3 -b:a 320k )
        // please select cbr or vbr mode
        ////$this->ffmpegSettings['audio']['bitrate_mode']="vbr";# Variable Bit Rate (VBR) mode
        ////$this->ffmpegSettings['audio']['qscale']="1";        # hi quality ( -c:a mp3 -q:a 1 )
        ################# end of mp3 settings #################

# VIDEO settinds
        $this->ffmpegSettings['video'] = array();
        ################# direct video settings #################
        # you can use 'direct video settings' string for video settings,
        # in this case all other video settings will be ignored
        //$this->ffmpegSettings['video']['direct']=" -c:v libx264 -pix_fmt yuv420p -f mp4 ";
        ################# end of direct video settings #################

        ################# copy settings #################
        //$this->ffmpegSettings['video']['codec']="copy";        # copy video stream to output withou transcoding ( -c:v copy )
        ################# end of copy settings #################

        $this->ffmpegSettings['video']['framerate'] = 25;
        $this->ffmpegSettings['video']['format'] = "mp4";
        $this->ffmpegSettings['video']['pix_fmt'] = "yuv420p";
        $this->ffmpegSettings['video']['faststart'] = true; # -movflags +faststart
        ################# libx264 settings #################
        $this->ffmpegSettings['video']['codec'] = "libx264"; # https://trac.ffmpeg.org/wiki/Encode/H.264
        $this->ffmpegSettings['video']['preset'] = "veryfast"; # Speed of processing: ultrafast,superfast, veryfast, faster, fast, medium, slow, slower, veryslow, placebo
        $this->ffmpegSettings['video']['crf'] = "23"; # Constant Rate Factor: 0-51: where 0 is lossless, 23 is default, and 51 is worst possible.
        //$this->ffmpegSettings['video']['profile']="main";        # limit the output to a specific H.264 profile: baseline, main, high, high10, high422, high444 ( for old devices set to:  'baseline -level 3.0' )
        ################# end of libx264 settings #################
        $this->error = null;
    }

/**
 * getAudioOutSettingsString
 * return the string for audio out settings for ffmpeg
 *
 * @return    string
 */
    private function getAudioOutSettingsString()
    {
        if (isset($this->ffmpegSettings['audio']['direct'])) {
            return ($this->ffmpegSettings['audio']['direct']);
        }
        $str = '';
        if (isset($this->ffmpegSettings['audio']['codec'])) {
            $str .= " -strict -2 -c:a " . $this->ffmpegSettings['audio']['codec'];
        }
        if (isset($this->ffmpegSettings['audio']['bitrate_mode']) && $this->ffmpegSettings['audio']['bitrate_mode'] == 'cbr') {
            if ($this->ffmpegSettings['audio']['bitrate']) {
                $str .= " -b:a " . $this->ffmpegSettings['audio']['bitrate'];
            }
        }
        if (isset($this->ffmpegSettings['audio']['bitrate_mode']) && $this->ffmpegSettings['audio']['bitrate_mode'] == 'vbr') {
            if ($this->ffmpegSettings['audio']['qscale']) {
                $str .= " -q:a " . $this->ffmpegSettings['audio']['qscale'];
            }
        }
        if (isset($this->ffmpegSettings['audio']['channels'])) {
            $str .= " -ac " . $this->ffmpegSettings['audio']['channels'];
        }

        return ($str);
    }

/**
 * getVideoOutSettingsString
 * return the string for video out settings for ffmpeg
 *
 * @return    string
 */
    private function getVideoOutSettingsString()
    {
        if (isset($this->ffmpegSettings['video']['direct'])) {
            return ($this->ffmpegSettings['video']['direct']);
        }
        $str = '';
        if (isset($this->ffmpegSettings['video']['codec'])) {
            $str .= " -c:v " . $this->ffmpegSettings['video']['codec'];
        }
        if (isset($this->ffmpegSettings['video']['preset'])) {
            $str .= " -preset " . $this->ffmpegSettings['video']['preset'];
        }
        if (isset($this->ffmpegSettings['video']['crf'])) {
            $str .= " -crf " . $this->ffmpegSettings['video']['crf'];
        }
        if (isset($this->ffmpegSettings['video']['profile'])) {
            $str .= " -profile:v " . $this->ffmpegSettings['video']['profile'];
        }

        if (isset($this->ffmpegSettings['video']['pix_fmt'])) {
            $str .= " -pix_fmt " . $this->ffmpegSettings['video']['pix_fmt'];
        }
        if (isset($this->ffmpegSettings['video']['faststart'])) {
            $str .= " -movflags +faststart";
        }
        if (isset($this->ffmpegSettings['video']['format'])) {
            $str .= " -f " . $this->ffmpegSettings['video']['format'];
        }
        return ($str);
    }

/**
 * getLastError
 * return last error description
 *
 * @return    string
 */
    public function getLastError()
    {
        return ($this->error);
    }

/**
 * setLastError
 * set last error description
 *
 * @param    string  $err
 * @return    string
 */
    private function setLastError($err)
    {
        $this->error = $err;
        return (true);
    }

/**
 * getFfmpegSettings
 * return the current value of ffmpeg settings
 *
 * @param    string  $section ( 'general' ,'audio' or 'video' )
 * @param    string  $key
 * @return    string
 */
    public function getFfmpegSettings($section, $key)
    {
        $value = isset($this->ffmpegSettings[$section][$key]) ? $this->ffmpegSettings[$section][$key] : null;
        return $value;
    }

/**
 * setFfmpegSettings
 * set new value to ffmpeg output settings
 *
 * @param    string  $section ( 'general' ,'audio' or 'video' )
 * @param    string  $key
 * @param    string  $value
 * @return    true
 */
    public function setFfmpegSettings($section, $key, $value)
    {
        $this->ffmpegSettings[$section][$key] = $value;
        return (true);
    }

/**
 * setGeneralSettings
 * return the current value of general ffmpeg settings
 *
 * @param    array  with key=>value of audio settings
 * @param    string  $value
 * @return    true
 */
    public function setGeneralSettings($arr)
    {
        $this->ffmpegSettings['general'] = array_replace($this->ffmpegSettings['general'], $arr);
        return (true);
    }

/**
 * getGeneralSettings
 * return the current value of general ffmpeg settings
 *
 * @param    array  with key=>value of audio settings
 * @return    true
 */
    public function getGeneralSettings()
    {
        return ($this->ffmpegSettings['general']);
    }

/**
 * setAudioOutputSettings
 * return the current value of ffmpeg settings
 *
 * @param    array  with key=>value of audio settings
 * @return    true
 */
    public function setAudioOutputSettings($arr)
    {
        $this->ffmpegSettings['audio'] = array_replace($this->ffmpegSettings['audio'], $arr);
        return (true);
    }

/**
 * setVideoOutputSettings
 * return the current value of ffmpeg settings
 *
 * @param    array  with key=>value of video settings
 * @return    true
 */
    public function setVideoOutputSettings($arr)
    {
        $this->ffmpegSettings['video'] = array_replace($this->ffmpegSettings['video'], $arr);
        return (true);
    }

/**
 * getAudioOutputSettings
 * return the current value output audio ffmpeg settings
 *
 * @return array with key=>value of audio settings
 */
    public function getAudioOutputSettings()
    {
        return ($this->ffmpegSettings['audio']);
    }

/**
 * getVideoOutputSettings
 * return the current value output video ffmpeg settings
 *
 * @return array with key=>value of audio settings
 */
    public function getVideoOutputSettings()
    {
        return ($this->ffmpegSettings['video']);
    }

/**
 * formatTime
 * return time in hour:minute:
 *
 * @param    integer $t
 * @param    string  $f
 * @return    string
 */
    private function formatTime($t, $f = ':') // t = seconds, f = separator

    {
        return sprintf("%01d%s%02d%s%02.2f", floor($t / 3600), $f, ($t / 60) % 60, $f, $t % 60);
    }

/**
 * writeToLog
 * function print messages to console
 *
 * @param    string $message
 * @return    string
 */
    public function writeToLog($message)
    {
        #echo "$message\n";
        fwrite(STDERR, "$message\n");
    }

/**
 * getStreamInfo
 * function get info about video or audio stream in the file
 *
 * @param    string $fileName
 * @param    string $streamType    must be  'audio' or 'video'
 * @param    array &$data          return data
 * @return    integer 1 for success, 0 for any error
 */
    public function getStreamInfo($fileName, $streamType, &$data)
    {
        # parameter - 'audio' or 'video'
        $ffprobe = $this->getFfmpegSettings('general', 'ffprobe');

        if (!$probeJson = json_decode(`"$ffprobe" $fileName -v quiet -hide_banner -show_streams -of json`, true)) {
            $this->writeToLog("Cannot get info about file $fileName");
            return 0;
        }
        if (empty($probeJson["streams"])) {
            $this->writeToLog("Cannot get info about streams in file $fileName");
            return 0;
        }
        foreach ($probeJson["streams"] as $stream) {
            if ($stream["codec_type"] == $streamType) {
                $data = $stream;
                break;
            }
        }

        if (empty($data)) {
            $this->writeToLog("File $fileName :  stream not found");
            return 0;
        }
        if ('video' == $streamType) {
            if (empty($data["height"]) || !intval($data["height"]) || empty($data["width"]) || !intval($data["width"])) {
                $this->writeToLog("File $fileName : invalid or corrupt dimensions");
                return 0;
            }
        }

        return 1;
    }

/**
 * time2float
 * this function translate time in format 00:00:00.00 to seconds
 *
 * @param    string $t
 * @return    float
 */

    public function time2float($t)
    {
        $matches = preg_split("/:/", $t, 3);
        if (array_key_exists(2, $matches)) {
            list($h, $m, $s) = $matches;
            return ($s + 60 * $m + 3600 * $h);
        }
        $h = 0;
        list($m, $s) = $matches;
        return ($s + 60 * $m);
    }

/**
 * float2time
 * this function translate time from seconds to format 00:00:00.00
 *
 * @param    float $i
 * @return    string
 */
    public function float2time($i)
    {
        $h = intval($i / 3600);
        $m = intval(($i - 3600 * $h) / 60);
        $s = $i - 60 * floatval($m) - 3600 * floatval($h);
        return sprintf("%01d:%02d:%05.2f", $h, $m, $s);
    }

/**
 * doExec
 * @param    string    $Command
 * @return integer 0-error, 1-success
 */

    public function doExec($Command)
    {
        $outputArray = array();
        exec($Command, $outputArray, $execResult);
        if ($execResult) {
            $this->writeToLog(join("\n", $outputArray));
            return 0;
        }
        return 1;
    }

/**
 * scrollingText
 *
 * @param    string    $bgImage
 * @param    integer   $textBoxWidth
 * @param    integer   $textBoxHeight
 * @param    integer   $x
 * @param    integer   $y
 * @param    string    $text
 * @param    integer   $duration
 * @param    integer   $scrollingDelay
 * @param    string    $audioFile
 * @param    string    $output
 * @param    string    $temporaryAssFile
 * @param    integer   $width - video width
 * @param    integer   $height - video height
 * @param    string    $font - default Arial
 * @param    integer   $fontSize - default 35
 * @param    string    $fontColor - default white &H00FFFFFF
 * @param    integer   $styleBold - ( default disable - 0, enable - 1 )
 * @param    integer   $styleItalic - ( default disable - 0, enable - 1 )
 * @param    integer   $showLines - how many line will be shown in the scrolling window, default - 3 
 * @return string  Command ffmpeg
 */

    public function scrollingText(
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
        $width = 1280,
        $height = 720,
        $font = "Arial",
        $fontSize = 35,
        $fontColor = "&H00FFFFFF",
        $styleBold = 0,
        $styleItalic = 0,
        $showLines = 3
    ) {

        $this->setLastError('');
        if (!$this->prepareSubtitles(
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
            "",
            "Default",
            $font,
            $fontSize,
            $fontColor,
            $styleBold,
            $styleItalic,
            3
        )) {
            return ("");
        }

        $ffmpeg = $this->getFfmpegSettings('general', 'ffmpeg');
        $ffmpegLogLevel = $this->getFfmpegSettings('general', 'ffmpegLogLevel');
        $videoOutSettingsString = $this->getVideoOutSettingsString();
        $audioOutSettingsString = $this->getAudioOutSettingsString();
        $data = null;
        if (!file_exists($bgImage)) {
            $this->setLastError("File $bgImage do not exists");
            return '';
        }
        if (!$this->getStreamInfo($bgImage, 'video', $data)) {
            $this->setLastError("Cannot get info about video stream in file $bgImage");
            return '';
        }
        if (!$this->getStreamInfo($audioFile, 'audio', $data)) {
            $this->setLastError("Cannot get info about audio stream in file $audioFile");
            return '';
        }

        $cmd = join(" ", [
            "$ffmpeg -loglevel $ffmpegLogLevel  -y  ",
            " -i $audioFile -ss 0 -t $duration ",
            " -loop 1 -i $bgImage -ss 0 -t $duration ",
            " -filter_complex \" ",
            " [0:a] apad [a];  ",
            " [1:v] scale=w=min(iw*${height}/ih\,${width}):h=min(${height}\,ih*${width}/iw),  ",
            " pad=w=${width}:h=${height}:x=(${width}-iw)/2:y=(${height}-ih)/2 , ",
            " ass='$temporaryAssFile' ",
            " [v]\" ",
            " -map \"[v]\" -map \"[a]\" $audioOutSettingsString $videoOutSettingsString $output",
        ]
        );
        if ($this->getFfmpegSettings('general', 'showCommand')) {
            echo "$cmd\n";
        }

        return $cmd;
    }

/**
 * prepareSubtitles
 * prepare ASS subtitles file
 *
 * @param    integer   $textBoxWidth
 * @param    integer   $textBoxHeight
 * @param    integer   $x
 * @param    integer   $y
 * @param    string    $text
 * @param    integer   $duration
 * @param    integer   $scrollingDelay
 * @param    integer   $width - video width
 * @param    integer   $height - video height
 * @param    string    $temporaryAssFile
 * @param    string    $additionalStyles eg 'myStyle0: My,Arial,20,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,0,0,0,0,100,100,0,6,1,0,0,2,10,25,35,1')
 * @param    string    $useStyle  eg myStyle0
 * @param    string    $font
 * @param    integer   $fontSize
 * @param    integer   $fontColor
 * @param    string    $fontColor
 * @param    integer   $styleBold ( disable - 0, enable - 1 )
 * @param    integer   $styleItalic ( disable - 0, enable - 1 )
 * @param    integer   $showLines - how many line will be shown in the scrolling window
 *
 * @return   boolean
 */
    public function prepareSubtitles(
        $textBoxWidth,
        $textBoxHeight,
        $x,
        $y,
        $text,
        $duration,
        $scrollingDelay,
        $temporaryAssFile,
        $width = 1280,
        $height = 720,
        $additionalStyles = "",
        $useStyle = "Default",
        $font = "Arial",
        $fontSize = 35,
        $fontColor = "&H00FFFFFF",
        $styleBold = 0,
        $styleItalic = 0,
        $showLines = 3
    ) {
        $this->setLastError('');
        $dialogEnd = $this->float2time($duration);
        $lines = substr_count($text, "\n");
        $text = preg_replace('/\s*$/', '', $text); // remove \n and spaces in the end of text
        $fixedText = preg_replace('/\s*\n\s*/', '\N', $text);

        $clipX0 = $x;
        $clipX1 = $x + $textBoxWidth;
        $clipY0 = $y;
        $clipY1 = $y + $textBoxHeight;
        $styleMarginL = $x;
        $styleMarginR = $width - $textBoxWidth - $x;

        $moveT0 = $scrollingDelay * 1000;
        $moveT1 = $duration * 1000;
        $oneLineHeight = $textBoxHeight / $showLines;
        $moveY1 = $y - (1 + $lines - $showLines) * $oneLineHeight;
        $styleBold = $styleBold ? -1 : 0;
        $styleItalic = $styleItalic ? -1 : 0;
        $styles = "Style: $useStyle,$font,$fontSize,$fontColor,&H000000FF,&H00050506,&H00919198,$styleBold,$styleItalic,0,0,100,100,0,0,1,1,0.1,7,$styleMarginL,$styleMarginR,10,1";

        $content = "[Script Info]
; Aegisub 3.2.2
; http://www.aegisub.org/
; FfmpegEffects php lib
; korolev-ia [at] yandex.ru
ScriptType: v4.00+
PlayResX: $width
PlayResY: $height
WrapStyle: 2
YCbCr Matrix: TV.601


[V4+ Styles]
Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic, Underline, StrikeOut, ScaleX, ScaleY, Spacing, Angle, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, Encoding
$styles
$additionalStyles

[Events]
Format: Layer, Start, End, Style, Name, MarginL, MarginR, MarginV, Effect, Text
Dialogue: 0,0:00:00.00,$dialogEnd,Default,,0,0,0,,{\clip($clipX0,$clipY0,$clipX1,$clipY1)} {\move($clipX0,$clipY0,$clipX0,$moveY1,$moveT0,$moveT1)}$fixedText
";

        if (!file_put_contents($temporaryAssFile, $content)) {
            $this->writeToLog("Cannot save temporary subtitles file '$temporaryAssFile'");
            return (false);
        }
        return (true);

    }

}
