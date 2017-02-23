<?php namespace Xliff;

interface WriterInterface extends XliffInterface
{
    /**
     * Return xlf content as string
     * @return string
     */
    public function toString();

    /**
     * Output xlf content to file
     * @param string $file
     * @return void
     */
    public function toFile($file);


}