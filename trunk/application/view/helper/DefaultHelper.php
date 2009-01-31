<?php

/**
 * Default helper class
 *
 * @category    Blotomate
 * @package     View
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
        $session = SessionController::recoverSession();
        $value = null;

        if(is_object($session))
        {
            $value = $session->{$attribute};
        }

        echo $value;
    }
}
