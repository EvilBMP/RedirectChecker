<?php

namespace GorkaLaucirica\RedirectChecker\Domain;

/**
 * Class RedirectionTraceItem
 *
 * @package GorkaLaucirica\RedirectChecker\Domain
 */
class RedirectionTraceItem
{

    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var StatusCode
     */
    private $statusCode;

    /**
     * RedirectionTraceItem constructor.
     *
     * @param Uri $uri
     * @param StatusCode $statusCode
     */
    public function __construct(Uri $uri, StatusCode $statusCode)
    {
        $this->uri = $uri;
        $this->statusCode = $statusCode;
    }

    /**
     * @return Uri
     */
    public function uri(): Uri
    {
        return $this->uri;
    }

    /**
     * @return StatusCode
     */
    public function statusCode(): StatusCode
    {
        return $this->statusCode;
    }
}


