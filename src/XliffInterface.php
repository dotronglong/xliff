<?php namespace Xliff;

interface XliffInterface
{
    /**
     * Return xlf version, e.g, 1.2, 2
     * @return string
     */
    public function getVersion();

    /**
     * Set xlf version
     * @param string $version
     * @return void
     */
    public function setVersion($version);

    /**
     * Get source language
     * @return string
     */
    public function getSourceLanguage();

    /**
     * Set source language
     * @param string $language
     * @return void
     */
    public function setSourceLanguage($language);

    /**
     * Get target language
     * @return string
     */
    public function getTargetLanguage();

    /**
     * Set target language
     * @param string $language
     * @return void
     */
    public function setTargetLanguage($language);
}