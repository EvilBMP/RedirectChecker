<?php

namespace GorkaLaucirica\RedirectChecker\Infrastructure\RedirectTraceProvider;

use GorkaLaucirica\RedirectChecker\Domain\Redirection;
use GorkaLaucirica\RedirectChecker\Domain\RedirectionTraceItem;
use GorkaLaucirica\RedirectChecker\Domain\RedirectTraceProvider;
use GorkaLaucirica\RedirectChecker\Domain\StatusCode;
use GorkaLaucirica\RedirectChecker\Domain\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RedirectMiddleware;

/**
 * Class Guzzle
 *
 * @package GorkaLaucirica\RedirectChecker\Infrastructure\RedirectTraceProvider
 */
final class Guzzle implements RedirectTraceProvider
{

    /**
     * @var Client
     */
    private $client;

    /**
     * Guzzle constructor.
     */
    public function __construct()
    {
        $this->client = new Client(['allow_redirects' => ['track_redirects' => true]]);
    }

    /**
     * @param Redirection $redirection
     * @return array
     * @throws GuzzleException
     */
    public function getRedirectionTrace(Redirection $redirection): array
    {
        $request = new Request('GET', $redirection->origin());

        try {
            $response = $this->client->send($request);

            return $this->generateRedirectionTrace($response);
        } catch (ClientException $e) {
            $redirectionTrace = $this->generateRedirectionTrace($e->getResponse());
            return $this->replaceLastItemsStatusCode($redirectionTrace, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @param Response $response
     * @return array
     */
    public function generateRedirectionTrace(Response $response): array
    {
        $statusHistory = $response->getHeader(RedirectMiddleware::STATUS_HISTORY_HEADER);
        $cntStatusHistory = count($statusHistory);
        $urlHistory = $response->getHeader(RedirectMiddleware::HISTORY_HEADER);
        $cntUrlHistory = count($urlHistory);

        if ($cntStatusHistory !== $cntUrlHistory) {
            return [];
        }

        $redirectionTrace = [];

        for ($i = 0; $i < $cntStatusHistory; $i++) {
            $redirectionTrace[] = new RedirectionTraceItem(
                new Uri($urlHistory[$i]),
                new StatusCode($statusHistory[$i])
            );
        }

        return $redirectionTrace;
    }

    /**
     * @param array $redirectionTrace
     * @param int $statusCode
     * @return array
     */
    private function replaceLastItemsStatusCode(array $redirectionTrace, int $statusCode): array
    {
        if (count($redirectionTrace) === 0) {
            return [];
        }
        $redirectionTrace[count($redirectionTrace) - 1] = new RedirectionTraceItem(
            $redirectionTrace[count($redirectionTrace) - 1]->uri(),
            new StatusCode($statusCode)
        );

        return $redirectionTrace;
    }
}
