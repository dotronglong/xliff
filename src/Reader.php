<?php namespace Xliff;

use Sabre\Xml\Reader as XmlReader;
use Xliff\Exception\FileNotFoundException;

class Reader extends Xliff implements ReaderInterface
{
    /**
     * @var XmlReader
     */
    private $reader;

    public function parse()
    {
        if ($this->reader === null) {
            return [];
        }

        switch ($this->getVersion()) {
            case '1.2':
                return $this->parseVersionOne();

            case '2':
            default:
                return $this->parseVersionTwo();
        }
    }

    public function fromUri($uri, $encoding = null, $options = 0)
    {
        if (!file_exists($uri)) {
            throw new FileNotFoundException("File $uri could not be found.");
        }

        $this->reader = new XmlReader();
        $this->fromString(file_get_contents($uri), $encoding, $options);
    }

    public function fromString($xml, $encoding = null, $options = 0)
    {
        $this->reader = new XmlReader();
        $this->reader->XML($this->scanAndFix($xml), $encoding, $options);
    }

    /**
     * Parse xlf followed by version 1.2
     * @return array
     */
    private function parseVersionOne()
    {
        $this->reader->elementMap = [
            'xliff'      => function (XmlReader $reader) {
                return \Sabre\Xml\Deserializer\keyValue($reader, 'file');
            },
            'file'       => function (XmlReader $reader) {
                $attributes = $reader->parseAttributes();
                if (!$this->getSourceLanguage() && isset($attributes['source-language'])) {
                    $this->setSourceLanguage(str_replace('-', '_', $attributes['source-language']));
                }
                if (!$this->getTargetLanguage() && isset($attributes['target-language'])) {
                    $this->setTargetLanguage(str_replace('-', '_', $attributes['target-language']));
                }

                return \Sabre\Xml\Deserializer\keyValue($reader, 'body');
            },
            'body'       => function (XmlReader $reader) {
                return \Sabre\Xml\Deserializer\repeatingElements($reader, 'trans-unit');
            },
            'trans-unit' => function (XmlReader $reader) {
                return \Sabre\Xml\Deserializer\valueObject($reader, TransUnit::class, '');
            }
        ];

        $content = $this->reader->parse();
        if (isset($content['name']) && $content['name'] === '{}xliff') {
            $content = $content['value'];
        }
        if (isset($content['{}file'])) {
            $content = $content['{}file'];
        }
        if (isset($content['{}body'])) {
            $content = $content['{}body'];
        }

        $return = [];
        foreach ($content as $transUnit) {
            if ($transUnit instanceof TransUnit) {
                $return[$transUnit->source] = $transUnit->target;
            }
        }

        return $return;
    }

    /**
     * Parse xlf followed by version 2.0
     *
     * @return array
     */
    private function parseVersionTwo()
    {
        $this->reader->elementMap = [
            '{urn:oasis:names:tc:xliff:document:2.0}xliff'   => function (XmlReader $reader) {
                return \Sabre\Xml\Deserializer\keyValue($reader, '{urn:oasis:names:tc:xliff:document:2.0}file');
            },
            '{urn:oasis:names:tc:xliff:document:2.0}file'    => function (XmlReader $reader) {
                return \Sabre\Xml\Deserializer\repeatingElements($reader, '{urn:oasis:names:tc:xliff:document:2.0}unit');
            },
            '{urn:oasis:names:tc:xliff:document:2.0}unit'    => function (XmlReader $reader) {
                return \Sabre\Xml\Deserializer\keyValue($reader, '{urn:oasis:names:tc:xliff:document:2.0}segment');
            },
            '{urn:oasis:names:tc:xliff:document:2.0}segment' => function (XmlReader $reader) {
                return \Sabre\Xml\Deserializer\keyValue($reader, '{urn:oasis:names:tc:xliff:document:2.0}source');
            }
        ];

        $content = $this->reader->parse();
        if (isset($content['attributes']['version'])) {
            $this->setVersion($content['attributes']['version']);
        }
        if (isset($content['attributes']['srcLang'])) {
            $this->setSourceLanguage($content['attributes']['srcLang']);
        }
        if (isset($content['attributes']['trgLang'])) {
            $this->setTargetLanguage($content['attributes']['trgLang']);
        }

        if (isset($content['name']) && $content['name'] === '{urn:oasis:names:tc:xliff:document:2.0}xliff') {
            $content = $content['value'];
        }
        if (isset($content['{urn:oasis:names:tc:xliff:document:2.0}file'])) {
            $content = $content['{urn:oasis:names:tc:xliff:document:2.0}file'];
        }

        $return = [];
        foreach ($content as $segment) {
            if (isset($segment['{urn:oasis:names:tc:xliff:document:2.0}segment'])) {
                $segment                                                           = $segment['{urn:oasis:names:tc:xliff:document:2.0}segment'];
                $return[$segment['{urn:oasis:names:tc:xliff:document:2.0}source']] = $segment['{urn:oasis:names:tc:xliff:document:2.0}target'];
            }
        }

        return $return;
    }
}

class TransUnit
{
    public $source;
    public $target;
}