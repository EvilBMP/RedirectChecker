<?php

namespace GorkaLaucirica\RedirectChecker\Domain;

/**
 * Interface RedirectTraceProvider
 *
 * @package GorkaLaucirica\RedirectChecker\Domain
 */
interface RedirectTraceProvider
{

    /**
     * @param Redirection $redirection
     * @return array of Uri's
     */
    public function getRedirectionTrace(Redirection $redirection): array;
}
