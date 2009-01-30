<?php

/**
 * Default helper class
 *
 * @category    Blotomate
 * @package     View
 */
class DefaultHelper extends AB_Helper
{
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
