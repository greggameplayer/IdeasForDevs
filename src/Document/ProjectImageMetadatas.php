<?php


namespace App\Document;


use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;

/** @EmbeddedDocument */
class ProjectImageMetadatas
{
    /** @Field(type="string") */
    private $contentType;

    public function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}