<?php

/**
 * Application utility class
 * 
 * @category    Blotomate
 * @package     Application library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class ApplicationUtility
{
    /**
     * PREG
     * 
     * @param   string      $subject
     * @param   array       $replace    preg_replace parameters
     * @param   array       $match      preg_match parameters
     * @return  integer
     */
    public static function preg(&$subject, $replace, $match)
    {
        foreach($replace as $r)
        {
            $p = array_merge($r, array($subject));
            $subject = call_user_func_array('preg_replace', $p);
        }

        $i = 0;
    
        foreach($match as $m)
        {
            $p = array_merge($m, array($subject));
            if(call_user_func_array('preg_match', $p) > 0) $i++;
        }

        return $i;
    }

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
}
