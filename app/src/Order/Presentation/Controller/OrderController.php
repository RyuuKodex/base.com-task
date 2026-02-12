<?php

declare(strict_types=1);

namespace App\Order\Presentation\Controller;

use App\Order\Application\Query\GetOrdersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/api/orders', name: 'api_orders_list', methods: ['GET'])]
    public function list(Request $request, MessageBusInterface $queryBus): JsonResponse
    {
        $marketplace = $request->query->get('marketplace');
        $limit = $request->query->getInt('limit', 10);
        $offset = $request->query->getInt('offset', 0);

        $envelope = $queryBus->dispatch(new GetOrdersQuery($marketplace, $limit, $offset));

        /** @var null|HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        if (null === $handledStamp) {
            return new JsonResponse([]);
        }

        $orders = $handledStamp->getResult();

        return new JsonResponse($orders);
    }
}
