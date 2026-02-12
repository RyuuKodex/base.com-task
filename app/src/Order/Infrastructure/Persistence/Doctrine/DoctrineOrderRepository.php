<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Persistence\Doctrine;

use App\Order\Domain\Model\Order;
use App\Order\Domain\Repository\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    /** @var EntityRepository<Order> */
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->repository = $entityManager->getRepository(Order::class);
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function existsByExternalId(int $externalId): bool
    {
        return $this->repository->count(['externalId' => $externalId]) > 0;
    }

    public function findByExternalId(int $externalId): ?Order
    {
        return $this->repository->findOneBy(['externalId' => $externalId]);
    }

    /**
     * @return array<int, Order>
     */
    public function findByFilters(?string $marketplace, int $limit, int $offset): array
    {
        $criteria = [];
        if (null !== $marketplace) {
            $criteria['marketplace'] = $marketplace;
        }

        return $this->repository->findBy($criteria, ['createdAt' => 'DESC'], $limit, $offset);
    }
}
