<?php

/**
 * Default helper class
 *
 * @category    Autoblog
 * @package     View
 */
class DefaultHelper extends AB_Helper
{
    public function getSessionLabel()
    {
        $id = SessionController::getSessionIdentification();
        $label = null;

        if(is_array($id))
            if(array_key_exists('label', $id))
                $label = $id['label'];

        return $label;
    }
}
