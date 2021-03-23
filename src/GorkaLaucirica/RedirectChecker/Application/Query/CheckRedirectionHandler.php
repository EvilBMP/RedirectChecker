<?php

namespace GorkaLaucirica\RedirectChecker\Application\Query;

use GorkaLaucirica\RedirectChecker\Domain\Redirection;
use GorkaLaucirica\RedirectChecker\Domain\RedirectionTraceItem;
use GorkaLaucirica\RedirectChecker\Domain\RedirectTraceProvider;
use GorkaLaucirica\RedirectChecker\Domain\Uri;

/**
 * Class CheckRedirectionHandler
 *
 * @package GorkaLaucirica\RedirectChecker\Application\Query
 */
class CheckRedirectionHandler
{

    /**
     * @var RedirectTraceProvider
     */
    private $redirectTraceProvider;

    /**
     * CheckRedirectionHandler constructor.
     *
     * @param RedirectTraceProvider $redirectTraceProvider
     */
    public function __construct(RedirectTraceProvider $redirectTraceProvider)
    {
        $this->redirectTraceProvider = $redirectTraceProvider;
    }

    /**
     * @param CheckRedirectionQuery $query
     * @return array
     */
    public function __invoke(CheckRedirectionQuery $query)
    {
        $redirection = new Redirection(
            new Uri($query->origin()),
            new Uri($query->destination())
        );

        $redirectionTrace = $this->redirectTraceProvider->getRedirectionTrace($redirection);

        return [
            'isValid' => $redirection->isValid($redirectionTrace),
            'trace' => array_map(function (RedirectionTraceItem $redirectionTraceItem) {
                return [
                    'uri' => $redirectionTraceItem->uri()->__toString(),
                    'statusCode' => $redirectionTraceItem->statusCode()->statusCode()
                ];
            }, $redirectionTrace)
        ];
    }
}
