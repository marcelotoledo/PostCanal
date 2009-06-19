<?php

/**
 * Application utility class
 * 
 * @category    PostCanal
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class A_Utility
{
    /**
     * Fix URL
     * 
     * @param   string      $url
     * @return  string
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

        $s = htmlentities($s, ENT_NOQUOTES, 'UTF-8', false);
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
    }
}
