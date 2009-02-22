<?php

/**
 * View
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AB_View
{
    /**
     * View helpers
     *
     * @var array
     */
    private $helpers = array();

    /**
     * View data
     *
     * @var mixed
     */
    private $data;

    /**
     * View layout
     *
     * @var string
     */
    private $layout = 'default';

    /**
     * View template
     *
     * @var string
     */
    private $template;


    /**
     * View constructor
     *
     * @param   AB_Request  $request
     * @return  void
     */
    public function __construct($request)
    {
        $this->template = $request->getController() . "/" . 
                          $request->getAction();
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        $value = null;

        if(is_array($this->data))
        {
            if(array_key_exists($name, $this->data))
            {
                $value = $this->data[$name];
            }
        }

        return $value;
    }

    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set($name, $value)
    {
        if(is_array($this->data) == false)
        {
            $this->data = array();
        }

        $this->data[$name] = $value;
    }

    /**
     * Call helper class 
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  string
     */
    public function __call($name, $args)
    {
        if(!array_key_exists($name, $this->helpers))
        {
            $this->helpers[$name] = new $name($this);
        }

        return $this->helpers[$name];
    }

    /**
     * Set view data
     *
     * @param   mixed   $data
     * @return  void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Set layout
     *
     * @param   string  $layout
     * @return  void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Set template
     *
     * @param   string  $template
     * @return  void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Get template
     *
     * @return  string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * View render
     *
     * @throws  AB_Exception
     * @return  void
     */
    public function render()
    {
        if(empty($this->layout))
        {
            if(empty($this->template))
            {
                /* render text */

                if(is_string($this->data))
                {
                    echo $this->data;
                }
            }

            /* render view template */

            else
            {
                $this->renderTemplate();
            }
        }

        /* render layout */

        else
        {
            if(($path = self::getLayoutPath($this->layout)))
            {
                include $path;
            }
            else
            {
                $_m = "layout (" . $this->layout . ") not found";
                $_d = array('method' => __METHOD__);
                throw new AB_Exception($_m, E_USER_ERROR, $_d);
            }
        }
    }

    /**
     * View template render
     *
     * @throws  AB_Exception
     * @return  void
     */
    private function renderTemplate()
    {
        if(($path = self::getTemplatePath($this->template)))
        {
            include $path;
        }
        else
        {
            $_m = "template (" . $this->template . ") not found";
            $_d = array('method' => __METHOD__);
            throw new AB_Exception($_m, E_USER_ERROR, $_d);
        }
    }

    /**
     * get layout path
     *
     * @param   string  $layout
     * @return  boolean
     */
    public static function getLayoutPath($layout)
    {
        $path = APPLICATION_PATH . "/view/layout/" . $layout . ".php";
        return file_exists($path) ? $path : null;
    }

    /**
     * get template path
     *
     * @param   string  $template
     * @return  boolean
     */
    public static function getTemplatePath($template)
    {
        $path = APPLICATION_PATH . "/view/template/" . $template . ".php";
        return file_exists($path) ? $path : null;
    }
}
