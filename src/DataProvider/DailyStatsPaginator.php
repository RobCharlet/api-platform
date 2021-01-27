<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Service\StatsHelper;
use Exception;
use Traversable;

class DailyStatsPaginator implements PaginatorInterface, \IteratorAggregate
{
    private $dailyStatsIterator;
    private $statsHelper;
    private $currentPage;
    private $maxResults;
    /**
     * @var \DateTimeInterface|null
     */
    private $fromDate;

    public function __construct(StatsHelper $statsHelper, int $currentPage, int $maxResults)
    {
        $this->statsHelper = $statsHelper;
        $this->currentPage = $currentPage;
        $this->maxResults = $maxResults;
    }

    public function count()
    {
        return $this->getItemsPerPage();
    }

    public function getLastPage(): float
    {
        return ceil($this->getTotalItems() / $this->getItemsPerPage()) ?: 1.;
    }

    public function getTotalItems(): float
    {
        // TODO: Return true count if "from" filter is used
        return $this->statsHelper->count();
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): float
    {
        return $this->maxResults;
    }

    public function getIterator()
    {
        if ($this->dailyStatsIterator === null) {
            //Nb of items we wants to skip before selecting DailyStats
            $offset = (($this->getCurrentPage() - 1) * $this->getItemsPerPage());

            $criteria = [];
            if ($this->fromDate) {
                $criteria['from'] = $this->fromDate;
            }

            $this->dailyStatsIterator = new \ArrayIterator(
                $this->statsHelper->fetchMany(
                    $this->getItemsPerPage(),
                    $offset,
                    $criteria
                )
            );
        }

        return $this->dailyStatsIterator;
    }

    public function setFromDate(?\DateTimeInterface $fromDate): void
    {
        $this->fromDate = $fromDate;
    }

}