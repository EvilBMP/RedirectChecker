<?php

namespace GorkaLaucirica\RedirectChecker\Domain;

/**
 * Class StatusCode
 *
 * @package GorkaLaucirica\RedirectChecker\Domain
 */
class StatusCode
{

    /**
     * @var int
     */
    private $statusCode;

    /**
     * StatusCode constructor.
     *
     * @param int $statusCode
     */
    public function __construct(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 399;
    }
}
