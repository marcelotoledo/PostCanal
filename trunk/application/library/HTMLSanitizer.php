<?php

/**
 * HTML Sanitizer
 * 
 * @category    PostCanal
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class L_HTMLSanitizer
{
    private $cmtype;


    const CMTYPE_WORDPRESS = 'wordpress';
    const CMTYPE_TWITTER   = 'twitter';

    public static $known_cmtypes = array
    (
        self::CMTYPE_WORDPRESS,
        self::CMTYPE_TWITTER
    );

    public static $video_sources = array
    (
        'youtube',
        'video.google',
        'dailymotion'
    );


    public function __construct($cmtype=null)
    {
        $this->cmtype = in_array($cmtype, self::$known_cmtypes) ? $cmtype : null;
    }

    public function sanitize(&$html)
    {
        $this->sanitizeAll($html);
        if($this->cmtype==self::CMTYPE_WORDPRESS) $this->sanitizeWordPress($html);
        if($this->cmtype==self::CMTYPE_TWITTER)   $this->sanitizeTwitter($html);
    }

    protected function findTags(&$html, $tag)
    {
        $j=0;
        $t=strlen($html);
        $a=Array();
        $tag_s = '<'  . $tag;
        $tag_e = '</' . $tag;
        $tag_e_t = strlen($tag_e);

        while(($s=strpos($html, $tag_s, $j))!==false)
        {
            $e=strpos($html, $tag_e, $s);

            if($e>$s) 
            {
                $a[] = substr($html, $s, ($e-$s+$tag_e_t+1));
                $j=$e;
            }
            else
            {
                $j=$t;
            }
        }

        return $a;
    }

    protected function getAttributes(&$html)
    {
        $j=0;
        $t=strlen($html);
        $a=array();
        
        while(($s=strpos($html, '=', $j))!==false)
        {
            $r=$s-1;
            while($r>0 && substr($html, $r, 1)!=' ') $r--;

            $k=substr($html, ($r+1), ($s-$r-1));

            $s++;
            $d=substr($html, $s, 1);
            if(in_array($d, array('\'','"'))==false) $d=' ';
            $e=strpos($html, $d, $s+1);

            if($e>$s)
            {
                if(array_key_exists($k, $a)==false) $a[$k] = substr($html, ($s+1), ($e-$s-1));
                $j=$e;
            }
            else
            {
                $j=$t;
            }
        }

        return $a;
    }

    protected function sanitizeFlashVideo(&$html)
    {
        foreach($this->findTags($html, 'object') AS $t)
        {
            $attr = $this->getAttributes($t);
            $sub = null;

            $vs_allow=false;
            if(array_key_exists('src', $attr))
            {
                foreach(self::$video_sources AS $_vs)
                {
                    if(strpos($attr['src'], $_vs)!==false) $vs_allow=true;
                }
            }

            if($vs_allow)
            {
                $sub = "<embed src=\"" . $attr['src'] . "\" ";
                $sub.= "type=\"application/x-shockwave-flash\"></embed>";
            }

            if($sub) $html = str_replace($t, $sub, $html);
        }

        foreach($this->findTags($html, 'embed') AS $t)
        {
            $attr = $this->getAttributes($t);
            $sub = null;

            $vs_allow=false;
            if(array_key_exists('src', $attr))
            {
                foreach(self::$video_sources AS $_vs)
                {
                    if(strpos($attr['src'], $_vs)!==false) $vs_allow=true;
                }
            }

            if($vs_allow)
            {
                $sub = "<embed";
                $sub.= " src=\"" . $attr['src'] . "\"";
                $sub.= " type=\"application/x-shockwave-flash\"";
                $sub.= "></embed>";
            }

            if($sub) $html = str_replace($t, $sub, $html);
        }
    }

    protected function sanitizeAll(&$html)
    {
    }

    protected function sanitizeWordPress(&$html)
    {
        $this->sanitizeFlashVideo($html);
    }

    protected function sanitizeTwitter(&$html)
    {
    }
}
