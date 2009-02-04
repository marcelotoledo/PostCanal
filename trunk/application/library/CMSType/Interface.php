<?php

/**
 * CMS type class interface
 * 
 * @category    Blotomate
 * @package     CMSType
 */
interface CMSTypeInterface
{
    /**
     * Set CMS Base URL
     *
     * @param   string  $url
     * @return  void
     */
    public function setBaseURL($url);

    /**
     * Get admin URL
     *
     * @return  string
     */
    public function getAdminURL();

    /**
     * Get admin auth URL
     *
     * @return  string
     */
    public function getAdminAuthURL();
}
