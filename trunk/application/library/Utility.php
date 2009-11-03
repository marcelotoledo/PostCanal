<?php

/**
 * Application utility class
 * 
 * @category    PostCanal
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class L_Utility
{
    public static $literal_time_dictionary = array
    (
        '-y' => '%d years ago',
        '-m' => '%d months ago',
        '-d' => '%d days ago',
        '-h' => '%d hours ago',
        '-i' => '%d minutes ago',
        '-s' => '%d seconds ago',
        '+0' => 'now',
        '+y' => 'in %d years',
        '+m' => 'in %d months',
        '+d' => 'in %d days',
        '+h' => 'in %d hours',
        '+i' => 'in %d minutes',
        '+s' => 'in %d seconds'
    );

    /**
     * Fix URL
     * 
     * @param   string      $url
     */
    public static function fixURL(&$url)
    {
        $p = "#^(.*?//)*([\w\.\d]*)(:(\d+))*(/*)(.*)$#";
        $m = array();
        preg_match($p, $url, $m);

        $protocol = empty($m[1]) ? "http://" : $m[1];
        $address  = empty($m[2]) ? ""        : $m[2];
        $port     = empty($m[3]) ? ""        : $m[3];
        $resource = empty($m[6]) ? ""        : $m[5] . $m[6];

        $url = $protocol . $address . $port . $resource;
    }

    /**
     * Title from URL
     * 
     * @param   string      $url
     * @return  string
     */
    public static function titleFromURL($url)
    {
        $m = array();
        preg_match("#^(.*?//)*([\w\.\d]*)(:(\d+))*(/*)(.*)$#", $url, $m);
        return ucwords(trim(str_replace('.', ' ', $m[2] . '.' . $m[6])));
    }

    /**
     * Compact HTML
     * 
     * @param   string  $html
     * @return  void
     */
    public static function compactHTML(&$html)
    {
        $html = preg_replace("/[\r\n]+/", "", $html); // new lines and tabs
        $html = preg_replace("/[[:space:]]+/", " ", $html); // extra spaces
        $html = preg_replace("/<!(--([^\-]|-[^\-])*--)>/", "", $html); // comments
    }

    /**
     * Generate random string
     *
     * @param   integer $length
     * @return  string
     */
    public static function randomString($length=8)
    {
        $d="123456789BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz";
        $s="";
        for($i=0;$i<$length;$i++) $s.=$d[mt_rand(0,50)];
        return $s;
    }

    /**
     * Array to Object
     *
     * @param   array   $in
     * @return  object
     */
    public static function array2Object($in)
    {
        $r = new stdClass();

        foreach($in as $k => $v)
        {
            $r->{$k} = is_array($v) ? self::array2Object($v) : $v;
        }

        return $r;
    }

    /**
     * Get default culture list
     */
    public static function getDefaultCulture()
    {
        return array
        (
            "en_US"
        );
    }

    /**
     * Keywords from string or hypertext
     */
    public static function keywords(&$s)
    {
        $s = strip_tags($s);
        $s = preg_replace("/[\t\r\n]+/", "", $s); // new lines and tabs
        $s = strtolower($s);

        // $s = htmlentities($s, ENT_NOQUOTES, 'UTF-8', false); // PHP 5.3
        $s = htmlentities($s, ENT_NOQUOTES, 'UTF-8'); // PHP 5.2
        $s = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $s);
        $s = preg_replace("/&(.)([\w]+);/", " ", $s);
        $s = preg_replace("/[^\w]+/", " ", $s);

        $s = preg_replace("/[^\w][\w]{1,2}[^\w]/", " ", $s);
        $s = preg_replace("/[^\w][\w]{1,2}[^\w]/", " ", $s);
        $s = preg_replace("/^[\w]{1,2}[^\w]/", " ", $s);
        $s = preg_replace("/[^\w][\w]{1,2}$/", " ", $s);

        $s = trim($s);
        $s = preg_replace("/[[:space:]]+/", " ", $s); // extra spaces

        $s = implode(" ", array_unique(explode(" ", $s)));
        $s = strtolower($s);
    }

    /**
     * Literal time
     */
    public static function literalTime($t, $d=array())
    {
        if(count($d)==0) $d = self::$literal_time_dictionary;

        $k = $t >= 0 ? '+' : '-';
        $j = abs($t);
        $x = '0';

        if($j >  1           ) {                     $x = 's'; }
        if($j > 60 && $x=='s') { $j = ceil($j / 60); $x = 'i'; }
        if($j > 60 && $x=='i') { $j = ceil($j / 60); $x = 'h'; }
        if($j > 24 && $x=='h') { $j = ceil($j / 24); $x = 'd'; }
        if($j > 30 && $x=='d') { $j = ceil($j / 30); $x = 'm'; }
        if($j > 12 && $x=='m') { $j = ceil($j / 12); $x = 'y'; }

        return (array_key_exists(($k . $x), $d)) ? sprintf($d[($k . $x)], $j) : '';
    }

    /**
     * Browser info
     */
    public static function browserInfo ($agent=null)
    {
        $known = array('msie'  , 'firefox'  , 'safari'    , 'webkit', 
                       'opera' , 'netscape' , 'konqueror' , 'gecko');
        $agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);

        $pattern = '#(?<browser>' . 
            join('|', $known) . 
            ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';

        if (!preg_match_all($pattern, $agent, $matches)) return '';

        $i = count($matches['browser'])-1;
        return preg_replace('/[^\w]/', '',  $matches['version'][$i] . ' ' . 
                                 strtolower($matches['browser'][$i]));
    }
}
