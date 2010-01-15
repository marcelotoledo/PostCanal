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
     * Access to registry data
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __call($name, $arguments)
    {
        return B_Registry::get($name . '/object');
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
        /* render layout */

        if(strlen($this->layout) > 0)
        {
            if(B_Registry::get('view/cache')=='true' && strlen($this->template) > 0)
            {
                $this->includeCache();
            }
            else
            {
                $this->includeLayout($this->layout . '.php');
            }
        }

        /* render view data as xml */

        else
        {
            echo $this->__toXML();
        }
    }

    /**
     * include cache file
     */
    public function includeCache()
    {
        $path = APPLICATION_PATH . '/view/cache/cache-' . $this->layout . '-' .
                strtolower(str_replace('/', '-', $this->template)) . '.php';

        if(file_exists($path))
        {
            include $path;
        }
        else
        {
            $_m = 'cache not found in path (' . $path . ')';
            throw new B_Exception($_m, E_ERROR);
        }
    }

    /**
     * include layout file
     *
     * @param   string  $name
     */
    public function includeLayout($name)
    {
        if(file_exists(($path = APPLICATION_PATH . '/view/layout/' . $name))) include $path;
    }

    /**
     * include template file
     *
     * @param   string  $type
     */
    public function includeTemplate($type)
    {
        if(file_exists(($path = APPLICATION_PATH . '/view/template/' . $this->template  . '.' . $type))) include $path;
    }
}
