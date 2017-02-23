<?php namespace Xliff;

interface ReaderInterface extends XliffInterface
{
    /**
     * Parse xlf file and return phrases
     *
     * @return array
     */
    public function parse();

    /**
     * Load xlf from URI
     * @param string $uri
     * @param null   $encoding
     * @param int    $options
     * @return void
     */
    public function fromUri($uri, $encoding = null, $options = 0);

    /**
     * Load xlf from string
     * @param string $xml
     * @param null   $encoding
     * @param int    $options
     * @return void
     */
    public function fromString($xml, $encoding = null, $options = 0);
}