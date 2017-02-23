<?php namespace Xliff;

use Sabre\Xml\Writer as XmlWriter;

class Writer extends Xliff implements WriterInterface
{
    /**
     * @var array
     */
    private $phrases = [];

    /**
     * @return array
     */
    public function getPhrases()
    {
        return $this->phrases;
    }

    /**
     * @param array $phrases
     */
    public function setPhrases($phrases)
    {
        $this->phrases = $phrases;
    }

    public function toString()
    {
        $writer = new XmlWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument($this->getVersion());

        switch ($this->getVersion()) {
            case '1.2':
                $this->toVersionOne($writer);
                break;

            case '2':
            default:
                $this->toVersionTwo($writer);
                break;
        }

        return $writer->outputMemory();
    }

    public function toFile($file)
    {
        file_put_contents($file, $this->toString());
    }

    private function toVersionOne(XmlWriter $writer)
    {
        $writer->startElement('xliff'); // <xliff>
        $writer->writeAttributes([
            'version' => $this->getVersion()
        ]);

        $writer->startElement('file'); // <file>
        $writer->writeAttributes([
            'source-language' => $this->getSourceLanguage(),
            'target-language' => $this->getTargetLanguage()
        ]);

        $writer->startElement('body'); // <body>
        $i = 0;
        foreach ($this->phrases as $source => $target) {
            $i++;
            $writer->startElement('trans-unit'); // <trans-unit>
            $writer->writeAttribute('id', $i);
            $writer->startElement('source'); // <source>
            $writer->writeAttribute('xml:lang', $this->getSourceLanguage());
            $writer->write($source);
            $writer->endElement(); // </source>
            $writer->startElement('target'); // <target>
            $writer->writeAttribute('xml:lang', $this->getTargetLanguage());
            $writer->write($target);
            $writer->endElement(); // </target>
            $writer->endElement(); // </trans-unit>
        }
        $writer->endElement(); // </body>
        $writer->endElement(); // </file>
        $writer->endElement(); // </xliff>
    }

    private function toVersionTwo(XmlWriter $writer)
    {
        $namespace = 'urn:oasis:names:tc:xliff:document:2.0';
        $writer->namespaceMap = [
            $namespace => ''
        ];

        $writer->startElement('xliff'); // <xliff>
        $writer->writeAttributes([
            'version' => $this->getVersion(),
            'srcLang' => $this->getSourceLanguage(),
            'trgLang' => $this->getTargetLanguage()
        ]);

        $writer->startElement('file'); // <file>
        $writer->writeAttribute('id', "{$this->getTargetLanguage()}.xlf");
        $i = 0;
        foreach ($this->phrases as $source => $target) {
            $i++;
            $writer->startElement('unit'); // <unit>
            $writer->writeAttribute('id', $i);
            $writer->startElement('segment'); // <segment>
            $writer->startElement('source'); // <source>
            $writer->write($source);
            $writer->endElement(); // </source>
            $writer->startElement('target'); // <target>
            $writer->write($target);
            $writer->endElement(); // </target>
            $writer->endElement(); // </segment>
            $writer->endElement(); // </unit>
        }
        $writer->endElement(); // </file>
        $writer->endElement(); // </xliff>
    }
}