<?php

/**
 * Default helper class
 *
 * @category    Blotomate
 * @package     View
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class DefaultHelper extends AB_Helper
{
    /**
     * print session attribute value
     *
     * @param   string  $attribute
     * @return  void
     */
    public function sessionAttribute($attribute)
    {
        $session = AbstractController::recoverSession();
        $value = null;

        if(is_object($session))
        {
            $value = $session->{$attribute};
        }

        echo $value;
    }
}
