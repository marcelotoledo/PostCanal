<?php

/**
 * View
 * 
 * @category    Autoblog
 * @package     AB
 */
class AB_View
{
    /**
     * Request
     *
     * @var AB_Request
     */
    private $request;

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
     * View constructor
     *
     * @param   AB_Request  $request
     * @return  void
     */
    public function __construct($request)
    {
        $this->request = $request;
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
     * Get request
     *
     * @return AB_Request
     */
    public function getRequest()
    {
        return $this->request;
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
     * Get view data
     *
     * @return  mixed
     */
    public function getData()
    {
        return $this->data;
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
     * Get layout
     *
     * @return  string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * View render
     *
     * @throws  Exception
     * @return  void
     */
    public function render()
    {
        if(empty($this->layout))
        {
            /* render text */

            if(is_string($this->data))
            {
                echo $this->data;
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
            $path = APPLICATION_PATH . "/view/layout/" . $this->layout . ".php";

            if(file_exists($path))
            {
                include $path;
            }
            else
            {
                throw new Exception("layout " . $this->layout . " not found");
            }
        }
    }

    /**
     * View template render
     *
     * @throws  Exception
     * @return  void
     */
    private function renderTemplate()
    {
        $template = $this->request->getController() . "/" . 
                    $this->request->getAction();

        $path = APPLICATION_PATH . "/view/template/" . $template . ".php";

        if(file_exists($path) == true)
        {
            include $path;
        }
        else
        {
            throw new Exception("template " . $template . " not found");
        }
    }
}
