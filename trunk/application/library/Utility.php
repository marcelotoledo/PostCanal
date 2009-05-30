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
}
