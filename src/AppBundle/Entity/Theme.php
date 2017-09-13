<?php

namespace AppBundle\Entity;

/**
 * Class Theme
 * @package AppBundle\Entity
 */
class Theme
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $zipUrl;

    /**
     * @var string
     */
    private $gitUrl;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipUrl(): string
    {
        return $this->zipUrl;
    }

    /**
     * @param string $zipUrl
     * @return $this
     */
    public function setZipUrl(string $zipUrl)
    {
        $this->zipUrl = $zipUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getGitUrl(): string
    {
        return $this->gitUrl;
    }

    /**
     * @param string $gitUrl
     * @return $this
     */
    public function setGitUrl(string $gitUrl)
    {
        $this->gitUrl = $gitUrl;

        return $this;
    }
}