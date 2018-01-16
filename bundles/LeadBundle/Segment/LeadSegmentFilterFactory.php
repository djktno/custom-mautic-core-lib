<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Segment;

use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Entity\LeadList;
use Mautic\LeadBundle\Segment\Decorator\BaseDecorator;
use Mautic\LeadBundle\Segment\Decorator\FilterDecoratorInterface;
use Mautic\LeadBundle\Segment\FilterQueryBuilder\BaseFilterQueryBuilder;
use Symfony\Component\DependencyInjection\Container;

class LeadSegmentFilterFactory
{
    /**
     * @var LeadSegmentFilterDate
     */
    private $leadSegmentFilterDate;

    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private $entityManager;

    /**
     * @var BaseDecorator
     */
    private $baseDecorator;

    /**
     * @var Container
     */
    private $container;

    public function __construct(
        LeadSegmentFilterDate $leadSegmentFilterDate,
        EntityManager $entityManager,
        BaseDecorator $baseDecorator,
        Container $container
    ) {
        $this->leadSegmentFilterDate = $leadSegmentFilterDate;
        $this->entityManager         = $entityManager;
        $this->baseDecorator         = $baseDecorator;
        $this->container             = $container;
    }

    /**
     * @param LeadList $leadList
     *
     * @return LeadSegmentFilters
     */
    public function getLeadListFilters(LeadList $leadList)
    {
        $leadSegmentFilters = new LeadSegmentFilters();

        $filters = $leadList->getFilters();
        foreach ($filters as $filter) {
            // LeadSegmentFilterCrate is for accessing $filter as an object
            $leadSegmentFilterCrate = new LeadSegmentFilterCrate($filter);

            $decorator = $this->getDecoratorForFilter($leadSegmentFilterCrate);

            $leadSegmentFilter = new LeadSegmentFilter($leadSegmentFilterCrate, $decorator, $this->entityManager);
            //$this->leadSegmentFilterDate->fixDateOptions($leadSegmentFilter);
            $leadSegmentFilter->setFilterQueryBuilder($this->getQueryBuilderForFilter($leadSegmentFilter));

            //@todo replaced in query builder
            $leadSegmentFilters->addLeadSegmentFilter($leadSegmentFilter);
        }

        return $leadSegmentFilters;
    }

    /**
     * @param LeadSegmentFilter $filter
     *
     * @return BaseFilterQueryBuilder
     */
    protected function getQueryBuilderForFilter(LeadSegmentFilter $filter)
    {
        $qbServiceId = $filter->getQueryType();

        return $this->container->get($qbServiceId);
    }

    /**
     * @param LeadSegmentFilterCrate $leadSegmentFilterCrate
     *
     * @return FilterDecoratorInterface
     */
    protected function getDecoratorForFilter(LeadSegmentFilterCrate $leadSegmentFilterCrate)
    {
        return $this->baseDecorator;
    }
}