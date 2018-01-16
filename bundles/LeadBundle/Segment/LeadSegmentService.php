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

use Doctrine\DBAL\Query\QueryBuilder;
use Mautic\LeadBundle\Entity\LeadList;
use Mautic\LeadBundle\Entity\LeadListSegmentRepository;
use Mautic\LeadBundle\Services\LeadSegmentFilterQueryBuilderTrait;
use Mautic\LeadBundle\Services\LeadSegmentQueryBuilder;

class LeadSegmentService
{
    use LeadSegmentFilterQueryBuilderTrait;

    /**
     * @var LeadListSegmentRepository
     */
    private $leadListSegmentRepository;

    /**
     * @var LeadSegmentFilterFactory
     */
    private $leadSegmentFilterFactory;

    /**
     * @var LeadSegmentQueryBuilder
     */
    private $queryBuilder;

    public function __construct(
        LeadSegmentFilterFactory $leadSegmentFilterFactory,
        LeadListSegmentRepository $leadListSegmentRepository,
        LeadSegmentQueryBuilder $queryBuilder)
    {
        $this->leadListSegmentRepository = $leadListSegmentRepository;
        $this->leadSegmentFilterFactory  = $leadSegmentFilterFactory;
        $this->queryBuilder              = $queryBuilder;
    }

    public function getDqlWithParams(Doctrine_Query $query)
    {
        $vals = $query->getFlattenedParams();
        $sql  = $query->getDql();
        $sql  = str_replace('?', '%s', $sql);

        return vsprintf($sql, $vals);
    }

    public function getNewLeadsByListCount(LeadList $entity, array $batchLimiters)
    {
        $segmentFilters = $this->leadSegmentFilterFactory->getLeadListFilters($entity);

        /** @var QueryBuilder $qb */
        $qb = $this->queryBuilder->getLeadsQueryBuilder($entity->getId(), $segmentFilters, $batchLimiters);

        $qb = $this->addNewLeadsRestrictions($qb, $entity->getId(), $batchLimiters);

//        $qb->andWhere('l.sssss=1');
        dump($qb->getQueryParts());
        $sql = $qb->getSQL();

        foreach ($qb->getParameters() as $k=>$v) {
            $sql = str_replace(":$k", "'$v'", $sql);
        }

        echo '<hr/>';
        echo $sql;
        try {
            $stmt    = $qb->execute();
            $results = $stmt->fetchAll();
            dump($results);
            foreach ($results as $result) {
                dump($result);
            }
        } catch (\Exception $e) {
            dump('Query exception: '.$e->getMessage());
        }
        echo '<hr/>';

        return $this->leadListSegmentRepository->getNewLeadsByListCount($entity->getId(), $segmentFilters, $batchLimiters);
    }
}