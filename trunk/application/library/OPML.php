<?php

/**
 * OPML parser
 *
 * @category    Blotomate
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 *
 * based on http://www.mt-soft.com.ar/2007/12/21/opml-parser-php-class/
 */

class A_OPML
{
    public $parser = null;
    public $data   = "";
    public $index  = 0;

	public $opml_map_vars = array('URL' => 'link_url', 'HTMLURL' => 'link_url', 'TEXT' => 'link_name', 'TITLE' => 'link_name', 'TARGET' => 'link_target','DESCRIPTION' => 'link_description', 'XMLURL' => 'link_rss', "CREATED"=>'created', 'TYPE'=>'type');


    public function ParseElementStart($parser, $tagName, $attrs)
    {
        $map = $this->opml_map_vars;
        if ($tagName == 'OUTLINE')
        {
              foreach (array_keys($this->opml_map_vars) as $key)
            {
                   if (isset($attrs[$key]))
                {
                        $$map[$key] = $attrs[$key];
                   }
              }
              // save the data away.
              @$this->data[$this->index]['names'] = $link_name;
              @$this->data[$this->index]['urls'] = $link_url;
              @$this->data[$this->index]['targets'] = $link_target;
              @$this->data[$this->index]['feeds'] = $link_rss;
              @$this->data[$this->index]['descriptions'] = $link_description;
              @$this->data[$this->index]['created'] = $created;
              @$this->data[$this->index]['type'] = $type;
              $this->index++;
         } // end if outline
    }

    public function ParseElementEnd($parser, $name)
    {
         // nothing to do.
    }

    public function ParseElementCharData($parser, $name)
    {
         // nothing to do.
    }

    public function Parse($XMLdata)
    {
          $this->parser = xml_parser_create();
          xml_set_object($this->parser, $this);

          xml_set_element_handler($this->parser,
               array(&$this, 'ParseElementStart'),
               array(&$this, 'ParseElementEnd'));

        xml_set_character_data_handler($this->parser,
            array(&$this, 'ParseElementCharData'));

          xml_parse($this->parser, $XMLdata);

          xml_parser_free($this->parser);

    }
}
