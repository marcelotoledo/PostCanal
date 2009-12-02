<?php

/**
 * OPML Parser
 * 
 * @category    PostCanal
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 * @description Based on IAM OPML Parser http://freshmeat.net/projects/opml-parser-class
 */

class L_OPMLParser
{
    private $parser;
    private $data;
    private $index = 0;

    public function __construct()
    {
        $this->parser = null;
        $this->data = Array();
    }

    protected function ParseElementStart($parser, $tagName, $attrs)
    {
        if ($tagName == 'OUTLINE')
		{
            $this->data[$this->index] = $attrs;
            $this->index++;
        } // end if outline
    }

    protected function ParseElementEnd($parser, $name)
    {
        // nothing to do.
    }

    protected function ParseElementCharData($parser, $name)
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

        return $this->data;
    }
}
