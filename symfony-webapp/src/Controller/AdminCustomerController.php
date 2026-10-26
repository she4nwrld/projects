<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminCustomerController extends AbstractController
{
    private const VIEWS = ['all', 'active', 'quiet', 'disabled'];

    private const LABELS = [
        'all' => 'All',
        'active' => 'Has booked',
        'quiet' => 'Never booked',
        'disabled' => 'Disabled',
    ];

    #[Route('/admin/customers', name: 'admin_customer')]
    public function index(
        #[MapQueryParameter] string $view = 'all',
        #[MapQueryParameter] string $q = '',
    ): Response {
        $filter = \in_array($view, self::VIEWS, true) ? $view : 'all';
        $term = trim($q);

        $customers = array_values(array_filter(
            $this->sampleCustomers(),
            static function (array $user) use ($filter, $term): bool {
                if ('' !== $term) {
                    $haystack = $user['full_name'].' '.$user['email'].' '.$user['phone'];
                    if (!str_contains(mb_strtolower($haystack), mb_strtolower($term))) {
                        return false;
                    }
                }

                return match ($filter) {
                    'active' => $user['bookings'] > 0,
                    'quiet' => 0 === $user['bookings'],
                    'disabled' => 0 === $user['is_active'],
                    default => true,
                };
            }
        ));

        return $this->render('admin_customer/index.html.twig', [
            'views' => self::VIEWS,
            'labels' => self::LABELS,
            'filter' => $filter,
            'term' => $term,
            'stats' => [
                'total' => 5,
                'fresh' => 1,
                'buyers' => 3,
                'disabled' => 1,
                'revenue' => 51_720,
            ],
            'customers' => $customers,
        ]);
    }

    /**
     * Shaped like the legacy customers query: users with booking rollups attached.
     *
     * @return list<array<string, mixed>>
     */
    private function sampleCustomers(): array
    {
        return [
            [
                'id' => 11,
                'full_name' => 'Maria Dela Cruz',
                'email' => 'maria.delacruz@example.com',
                'phone' => '0917 555 0142',
                'is_active' => 1,
                'created_at' => '2025-11-04 10:12:00',
                'bookings' => 6,
                'live' => 1,
                'done' => 5,
                'spent' => 28_400,
                'last_booked' => '2026-08-30 09:14:00',
                'reviews' => 2,
            ],
            [
                'id' => 12,
                'full_name' => 'Aileen Ramos',
                'email' => 'aileen.ramos@example.com',
                'phone' => '0995 108 2310',
                'is_active' => 1,
                'created_at' => '2026-02-19 14:40:00',
                'bookings' => 3,
                'live' => 1,
                'done' => 2,
                'spent' => 14_520,
                'last_booked' => '2026-09-20 11:05:00',
                'reviews' => 1,
            ],
            [
                'id' => 13,
                'full_name' => 'Edgar Villanueva',
                'email' => 'edgar.villanueva@example.com',
                'phone' => '0922 744 9901',
                'is_active' => 1,
                'created_at' => '2026-05-08 08:02:00',
                'bookings' => 2,
                'live' => 0,
                'done' => 2,
                'spent' => 8_800,
                'last_booked' => '2026-07-28 08:31:00',
                'reviews' => 0,
            ],
            [
                'id' => 14,
                'full_name' => 'Jomar Alcantara',
                'email' => 'jomar.alcantara@example.com',
                'phone' => '0918 221 7744',
                'is_active' => 1,
                'created_at' => '2026-09-21 17:26:00',
                'bookings' => 0,
                'live' => 0,
                'done' => 0,
                'spent' => 0,
                'last_booked' => null,
                'reviews' => 0,
            ],
            [
                'id' => 15,
                'full_name' => 'Rhea Sablan',
                'email' => 'rhea.sablan@example.com',
                'phone' => '0906 332 1188',
                'is_active' => 0,
                'created_at' => '2026-01-30 12:55:00',
                'bookings' => 0,
                'live' => 0,
                'done' => 0,
                'spent' => 0,
                'last_booked' => null,
                'reviews' => 0,
            ],
        ];
    }
}
