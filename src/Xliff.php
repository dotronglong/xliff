<?php namespace Xliff;

abstract class Xliff
{
    const XML_VERSION = '1.0';

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $sourceLanguage;

    /**
     * @var string
     */
    private $targetLanguage;

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getSourceLanguage()
    {
        return $this->sourceLanguage;
    }

    public function setSourceLanguage($language)
    {
        $this->sourceLanguage = $language;
    }

    public function getTargetLanguage()
    {
        return $this->targetLanguage;
    }

    public function setTargetLanguage($language)
    {
        $this->targetLanguage = $language;
    }

    /**
     * Scan and fix xml string
     * @param string $xml
     * @return string
     */
    protected function scanAndFix($xml)
    {
        switch ($this->version) {
            case '1.2':
                return strip_tags($xml, '<xml><xliff><file><body><trans-unit><source><target>');

            case '2':
            default:
                return $xml;
        }
    }
}