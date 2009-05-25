<?php

/**
 * Base View
 * 
 * @category    PostCanal
 * @package     Base Library
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
        if($name == "registry") return $this->registry;
        else                    return $this->registry->{$name}()->object;
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
    public function __toXML()
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
     * Auxiliar method for __toXML
     */
    private static function __xml_recursive($a, &$xml)
    {
        foreach($a as $k => $v)
        {
            $element = is_integer($k) ? "item" : $k;

            if(is_object($v)) $v = ((array) $v);

            if(is_array($v))
            {
                $xml->startElement($element);
                if(is_integer($k)) $xml->writeAttribute("key", $k);
                self::__xml_recursive($v, $xml);
                $xml->endElement();
            }
            elseif(is_bool($v))
            {
                $xml->writeElement($element, ($v == true) ? "true" : "false");
            }
            else
            {
                $xml->writeElement($element, $v);
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
        $this->renderLayout();
    }

    /**
     * Layout render
     *
     * @param   string  $type
     * @param   string  $required
     * @throws  B_Exception
     * @return  void
     */
    public function renderLayout($type='php', $required=true)
    {
        if(strlen($this->layout) == 0)
        {
            if(strlen($this->template) == 0)
            {
                echo $this->__toXML(); /* render view data as xml */
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
            if(($path = self::getLayoutPath($this->layout, $type)))
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
                    $_m = "layout (" . $this->layout . "." . $type . ") not found";
                    $_d = array('method' => __METHOD__);
                    throw new B_Exception($_m, E_USER_ERROR, $_d);
                }
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
     * @param   string  $type
     * @return  boolean
     */
    public static function getLayoutPath($layout, $type='php')
    {
        $path = APPLICATION_PATH . "/view/layout/" . $layout . "." . $type;
        return file_exists($path) ? $path : null;
    }

    /**
     * get template path
     *
     * @param   string  $template
     * @param   string  $type
     * @return  boolean
     */
    public static function getTemplatePath($template, $type='php')
    {
        $path = APPLICATION_PATH . "/view/template/" . $template . "." . $type;
        return file_exists($path) ? $path : null;
    }
}
