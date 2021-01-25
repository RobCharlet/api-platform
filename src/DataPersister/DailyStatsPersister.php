<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\DailyStats;
use Psr\Log\LoggerInterface;

class DailyStatsPersister implements ContextAwareDataPersisterInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof DailyStats;
    }

    /**
     * @param DailyStats $data
     */
    public function persist($data, array $context = [])
    {
        $this->logger->info(sprintf('Update the visitors to "%d"', $data->totalVisitors));
    }

    public function remove($data, array $context = [])
    {
        throw new \Exception('not supported');
    }
}