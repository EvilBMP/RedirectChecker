<?php

namespace GorkaLaucirica\RedirectChecker\Application\Query;

/**
 * Class CheckRedirectionQuery
 *
 * @package GorkaLaucirica\RedirectChecker\Application\Query
 */
class CheckRedirectionQuery
{

    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $destination;

    /**
     * CheckRedirectionQuery constructor.
     *
     * @param string $origin
     * @param string $destination
     */
    public function __construct(string $origin, string $destination)
    {
        $this->origin = $origin;
        $this->destination = $destination;
    }

    /**
     * @return string
     */
    public function origin(): string
    {
        return $this->origin;
    }

    /**
     * @return string
     */
    public function destination(): string
    {
        return $this->destination;
    }
}
