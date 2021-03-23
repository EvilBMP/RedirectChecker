<?php

namespace GorkaLaucirica\RedirectChecker\Domain;

/**
 * Class Redirection
 *
 * @package GorkaLaucirica\RedirectChecker\Domain
 */
class Redirection
{

    /**
     * @var Uri
     */
    private $origin;

    /**
     * @var Uri
     */
    private $destination;

    /**
     * Redirection constructor.
     *
     * @param Uri $origin
     * @param Uri $destination
     */
    public function __construct(Uri $origin, Uri $destination)
    {
        $this->origin = $origin;
        $this->destination = $destination;
    }

    /**
     * @param array $uriTrace
     * @return bool
     */
    public function isValid(array $uriTrace): bool
    {
        if (count($uriTrace) === 0) {
            return false;
        }

        $lastUri = $uriTrace[count($uriTrace) - 1];

        if (!$lastUri instanceof RedirectionTraceItem) {
            throw new \InvalidArgumentException('Each element of trace must be instance of RedirectionTraceItem');
        }

        return $lastUri->uri()->__toString() === $this->destination->__toString()
            && $lastUri->statusCode()->isSuccessful();
    }

    /**
     * @return Uri
     */
    public function origin(): Uri
    {
        return $this->origin;
    }

    /**
     * @return Uri
     */
    public function destination(): Uri
    {
        return $this->destination;
    }
}
