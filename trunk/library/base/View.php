<?php

/**
 * View
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_View
{
    /**
     * View data
     *
     * @var mixed
     */
    private $data = array();

    /**
     * View layout
     *
     * @var string
     */
    private $layout;

    /**
     * View template
     *
     * @var string
     */
    private $template;

    /**
     * Registry
     *
     * @var B_Registry
     */
    public $registry;


    /**
     * Access to registry data
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __call($name, $arguments)
    {
        return ($name == "registry") ? 
            $this->registry :
            $this->registry->{$name}()->object;
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        $result = null;

        if(array_key_exists($name, $this->data))
        {
            $result = $this->data[$name];
        }

        return $result;
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
        $this->data[$name] = $value;
    }

    /**
     * To (string) XML
     *
     * @return  string
     */
    public function __toString()
    {
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->startElement("data");
        self::__xml_recursive($this->data, $xml);
        $xml->endElement();
        return $xml->outputMemory();
    }

    /**
     * Deep recursion in array to write xml
     * Auxiliar method for __toString
     */
    private static function __xml_recursive($a, &$xml)
    {
        foreach($a as $k => $v)
        {
            if(is_array($v))
            {
                $xml->startElement($k);
                self::__xml_recursive($v, $xml);
                $xml->endElement();
            }
            else
            {
                if(is_bool($v)) $v = ($v == true) ? "true" : "false";
                $xml->writeElement($k, $v);
            }
        }
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
     * Get template
     *
     * @return  string
     */
    public function getTemplate()
    {
        return $this->template;
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
     * View render
     *
     * @throws  B_Exception
     * @return  void
     */
    public function render()
    {
        if(strlen($this->layout) == 0)
        {
            if(strlen($this->template) == 0)
            {
                echo ((string) $this); /* render view data as xml */
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
                throw new B_Exception($_m, E_USER_ERROR, $_d);
            }
        }
    }

    /**
     * Template render
     *
     * @param   string          $type
     * @param   string          $required
     * @throws  B_Exception
     * @return  void
     */
    private function renderTemplate($type='php', $required=true)
    {
        if(($path = self::getTemplatePath($this->template, $type)))
        {
            if($type == 'js')  echo "<script type=\"text/javascript\">\n";
            if($type == 'css') echo "<style type=\"text/css\">\n";

            include $path;

            if($type == 'js')  echo "</script>\n";
            if($type == 'css') echo "</style>\n";
        }
        else
        {
            if($required == true)
            {
                $_m = "template (" . $this->template . "." . $type . ") not found";
                $_d = array('method' => __METHOD__);
                throw new B_Exception($_m, E_USER_ERROR, $_d);
            }
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
    public static function getTemplatePath($template, $type='php')
    {
        $path = APPLICATION_PATH . "/view/template/" . $template . "." . $type;
        return file_exists($path) ? $path : null;
    }
}
