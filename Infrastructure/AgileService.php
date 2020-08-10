<?php

namespace Infra\InfraBot\Infrastructure;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\JiraException;
use Psr\Log\LoggerInterface;

class AgileService extends \JiraRestApi\JiraClient
{

    private $uri = '/backlog';

    /**
     * AgileService constructor.
     * @param ConfigurationInterface|null $configuration
     * @param LoggerInterface|null $logger
     * @param string $path
     * @throws JiraException
     */
    public function __construct(
        ConfigurationInterface $configuration = null,
        LoggerInterface $logger = null,
        $path = './'
    ) {
        parent::__construct($configuration, $logger, $path);
        $this->setAPIUri('/rest/agile/1.0');
    }

    /**
     * @param string $issuesId
     * @throws JiraException
     */
    public function moveToBacklog(string $issuesId): void
    {
        $this->exec($this->uri, [
            'issues' => [$issuesId],
        ]);
    }
}
