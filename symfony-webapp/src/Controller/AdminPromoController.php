<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminPromoController extends AbstractController
{
    private const VIEWS = ['all', 'live', 'hidden', 'expired'];

    #[Route('/admin/promos', name: 'admin_promo')]
    public function index(#[MapQueryParameter] string $view = 'all'): Response
    {
        $filter = \in_array($view, self::VIEWS, true) ? $view : 'all';
        $today = date('Y-m-d');

        $promos = array_values(array_filter(
            $this->samplePromos(),
            static function (array $promo) use ($filter, $today): bool {
                $expired = null !== $promo['expires_at'] && $promo['expires_at'] < $today;

                return match ($filter) {
                    'live' => 1 === $promo['active'] && !$expired,
                    'hidden' => 0 === $promo['active'],
                    'expired' => $expired,
                    default => true,
                };
            }
        ));

        return $this->render('admin_promo/index.html.twig', [
            'views' => self::VIEWS,
            'filter' => $filter,
            'today' => $today,
            'stats' => [
                'total' => 5,
                'live' => 3,
                'off' => 1,
                'gone' => 1,
            ],
            'promos' => $promos,
        ]);
    }

    /**
     * Shaped like the legacy promos query, with the usage count attached.
     *
     * @return list<array<string, mixed>>
     */
    private function samplePromos(): array
    {
        return [
            [
                'id' => 1,
                'code' => 'EARLY10',
                'tag' => 'Early Bird',
                'title' => 'Book 7 days ahead, save 10%',
                'blurb' => 'Plan the trip early and take ten percent off the daily rate. Applies to every vehicle in the fleet.',
                'percent' => 10,
                'free_days' => 0,
                'min_days' => 0,
                'min_advance_days' => 7,
                'stackable' => 0,
                'active' => 1,
                'expires_at' => null,
                'sort_order' => 1,
                'uses' => 14,
            ],
            [
                'id' => 2,
                'code' => 'WEEKFREE',
                'tag' => 'Long Stay',
                'title' => 'Seventh day on us',
                'blurb' => 'Hire for a full week and the last day is free. Great for island hopping around Negros Oriental.',
                'percent' => 0,
                'free_days' => 1,
                'min_days' => 7,
                'min_advance_days' => 0,
                'stackable' => 1,
                'active' => 1,
                'expires_at' => null,
                'sort_order' => 2,
                'uses' => 6,
            ],
            [
                'id' => 3,
                'code' => null,
                'tag' => 'Always On',
                'title' => 'Free delivery within Dumaguete',
                'blurb' => 'We bring the car to your hotel anywhere in the city centre. No code needed, just ask at booking.',
                'percent' => 0,
                'free_days' => 0,
                'min_days' => 0,
                'min_advance_days' => 0,
                'stackable' => 1,
                'active' => 1,
                'expires_at' => null,
                'sort_order' => 3,
                'uses' => 0,
            ],
            [
                'id' => 4,
                'code' => 'FIESTA25',
                'tag' => 'Seasonal',
                'title' => 'Sandurot Festival, 25% off',
                'blurb' => 'Festival week special on all hatchbacks and sedans. Limited to bookings of three days or more.',
                'percent' => 25,
                'free_days' => 0,
                'min_days' => 3,
                'min_advance_days' => 0,
                'stackable' => 0,
                'active' => 0,
                'expires_at' => '2026-11-30',
                'sort_order' => 4,
                'uses' => 2,
            ],
            [
                'id' => 5,
                'code' => 'SUMMER20',
                'tag' => 'Expired',
                'title' => 'Summer getaway, 20% off',
                'blurb' => 'Ran through the dry season for trips to Apo Island and the waterfalls at Valencia.',
                'percent' => 20,
                'free_days' => 0,
                'min_days' => 2,
                'min_advance_days' => 0,
                'stackable' => 0,
                'active' => 1,
                'expires_at' => '2026-06-30',
                'sort_order' => 5,
                'uses' => 31,
            ],
        ];
    }
}
