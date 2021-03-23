<?php

namespace GorkaLaucirica\RedirectChecker\Infrastructure\Ui\Cli\Symfony;

use GorkaLaucirica\RedirectChecker\Application\Query\CheckRedirectionHandler;
use GorkaLaucirica\RedirectChecker\Application\Query\CheckRedirectionQuery;
use GorkaLaucirica\RedirectChecker\Infrastructure\Loader\Yaml;
use GorkaLaucirica\RedirectChecker\Infrastructure\RedirectTraceProvider\Guzzle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckRedirectionFromYamlCommand
 *
 * @package GorkaLaucirica\RedirectChecker\Infrastructure\Ui\Cli\Symfony
 */
final class CheckRedirectionFromYamlCommand extends Command
{

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('yaml')
             ->setDescription('Executes the URL redirects tests in the given YAML file')
             ->addArgument('filepath', InputArgument::REQUIRED, 'The path to the YAML file.')
             ->addOption('base-url', null, InputOption::VALUE_OPTIONAL, 'The base URL for non-absolute redirects.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $checkRedirectionHandler = new CheckRedirectionHandler(new Guzzle());

        $fails = 0;
        $success = 0;

        $redirections = (new Yaml())->load($input->getArgument('filepath'));

        try {
            foreach ($redirections as $origin => $destination) {
                if ($input->hasOption('base-url')) {
                    $baseUrl = rtrim($input->getOption('base-url'), '/');
                    if (strpos($origin, 'http') === false) {
                        $origin = $baseUrl . $origin;
                    }
                    if (strpos($destination, 'http') === false) {
                        $destination = $baseUrl . $destination;
                    }
                }

                $redirection = $checkRedirectionHandler->__invoke(new CheckRedirectionQuery($origin, $destination));
                $output->writeln($this->renderResultLine($redirection, $origin, $destination));
                $redirection['isValid'] ? $success++ : $fails++;

                if ($input->getOption('verbose')) {
                    $output->writeln(sprintf('├── %s', $origin));

                    foreach ($redirection['trace'] as $traceItem) {
                        $output->writeln($this->renderTraceItemLine($traceItem));
                    }
                }
            }

            $output->writeln('');
            $output->writeln(
                sprintf(
                    '%s tests run, <fg=green>%s success</>, <fg=red>%s failed</>',
                    count($redirections),
                    $success,
                    $fails
                )
            );
        } catch (\Exception $e) {
            $output->writeln(
                '<error>' . $e->getMessage() . '</error>'
            );
        }

        return $fails > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @param array $redirection
     * @param string $origin
     * @param string $destination
     * @return string
     */
    private function renderResultLine(array $redirection, string $origin, string $destination): string
    {
        $isValid = $redirection['isValid'];

        $redirectionTraceLength = count($redirection['trace']);

        $statusCode = $redirectionTraceLength === 0
            ? 404
            : $redirection['trace'][$redirectionTraceLength - 1]['statusCode'];

        return sprintf(
            '<fg=%1$s>%2$s</> [%3$d] %4$s -> <fg=%1$s> %5$s </>',
            $isValid ? 'green' : 'red',
            $isValid ? '✓' : '✗',
            $statusCode,
            $origin,
            $destination
        );
    }

    /**
     * @param array $traceItem
     * @return string
     */
    private function renderTraceItemLine(array $traceItem): string
    {
        return sprintf('├── [%d] %s', $traceItem['statusCode'], $traceItem['uri']);
    }
}
