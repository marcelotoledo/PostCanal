<?php

/**
 * WordPress CMS type abstract class
 * 
 * @category    Blotomate
 * @package     CMSType
 */
abstract class CMSTypeWordPressAbstract
    extends CMSTypeAbstract
    implements CMSTypeInterface
{
    /**
     * WordPress CMS type abstract class constructor
     *
     * @return  void
     */
    public function __construct()
    {
    }

    /**
     * Get admin URL
     *
     * @return  string
     */
    public function getAdminURL()
    {
        return $this->base_url . "/wp-admin";
    }

    /**
     * Get admin auth URL
     *
     * @return  string
     */
    public function getAdminAuthURL()
    {
        return $this->base_url . "/wp-login.php?action=auth";
    }
}
