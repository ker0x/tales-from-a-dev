<?php

declare(strict_types=1);

namespace App\Domain\Project\Repository;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function queryAllByType(ProjectType $type): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p');

        return $queryBuilder
            ->where(
                $queryBuilder->expr()->eq('p.type', ':type')
            )
            ->setParameter('type', $type)
        ;
    }

    public function findOneByGithubId(string $id): ?Project
    {
        $rsm = $this->createResultSetMappingBuilder('p');

        $query = <<<SQL
            SELECT %s
            FROM project AS p
            WHERE p.type = :type
            AND p.metadata::jsonb->>'id' = :id
        SQL;
        $rawQuery = sprintf($query, $rsm->generateSelectClause());

        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameters([
            'type' => ProjectType::GitHub->value,
            'id' => $id,
        ]);

        return $query->getOneOrNullResult();
    }
}
