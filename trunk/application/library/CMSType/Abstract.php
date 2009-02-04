<?php

/**
 * CMS type abstract class
 * 
 * @category    Blotomate
 * @package     CMSType
 */
abstract class CMSTypeAbstract
{
    /**
     * Base URL
     *
     * @var string
     */
    protected $base_url = "";


    /**
     * CMS type abstract class constructor
     *
     * @return  void
     */
    public function __construct()
    {
    }

    /**
     * Set CMS Base URL
     *
     * @param   string  $url
     * @return  void
     */
    public function setBaseURL($url)
    {
        $this->base_url = $url;
    }
}
