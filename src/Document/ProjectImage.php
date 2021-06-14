<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class ProjectImage
{
    /** @MongoDB\Id */
    private $id;

    /** @MongoDB\File */
    private $file;

    /** @MongoDB\Field(type="string") */
    private $filename;

    /** @MongoDB\Field(type="string") */
    private $mimeType;

    /** @MongoDB\Field(type="date") */
    private $uploadDate;

    /** @MongoDB\Field(type="int") */
    private $length;

    /** @MongoDB\Field(type="int") */
    private $chunkSize;

    /** @MongoDB\Field(type="string") */
    private $md5;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getMd5()
    {
        return $this->md5;
    }

    public function getUploadDate()
    {
        return $this->uploadDate;
    }
}

