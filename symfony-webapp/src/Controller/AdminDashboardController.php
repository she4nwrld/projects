<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractController
{
    /** Status tabs, plus overdue which is a confirmed booking past its return date. */
    private const STATUSES = ['all', 'pending', 'confirmed', 'overdue', 'completed', 'cancelled'];

    private const PICKUP_POINTS = [
        'Rizal Boulevard, Dumaguete',
        'Sibulan Airport',
        'Valencia',
        'Bacong',
        'Dauin',
    ];

    private const AGE_BRACKETS = ['21-24', '25-29', '30-64', '65+'];

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(#[MapQueryParameter] string $status = 'all'): Response
    {
        $filter = \in_array($status, self::STATUSES, true) ? $status : 'all';
        $today = date('Y-m-d');

        $bookings = array_values(array_filter(
            $this->sampleBookings(),
            static function (array $booking) use ($filter, $today): bool {
                if ('all' === $filter) {
                    return true;
                }

                if ('overdue' === $filter) {
                    return 'confirmed' === $booking['status'] && $booking['return_date'] < $today;
                }

                return $filter === $booking['status'];
            }
        ));

        return $this->render('admin_dashboard/index.html.twig', [
            'statuses' => self::STATUSES,
            'filter' => $filter,
            'today' => $today,
            'pickups' => self::PICKUP_POINTS,
            'ages' => self::AGE_BRACKETS,
            'stats' => [
                'active' => 4,
                'cancelled' => 1,
                'pending' => 1,
                'overdue' => 2,
                'confirmed' => 2,
                'completed' => 1,
                'revenue' => 42_920,
            ],
            'bookings' => $bookings,
        ]);
    }

    /**
     * Shaped like the legacy dashboard query: bookings joined onto cars and users.
     *
     * @return list<array<string, mixed>>
     */
    private function sampleBookings(): array
    {
        return [
            [
                'id' => 1042,
                'status' => 'confirmed',
                'car_name' => 'Toyota Avanza',
                'car_img' => 'images/toyota-avanza.png',
                'full_name' => 'Maria Dela Cruz',
                'email' => 'maria.delacruz@example.com',
                'phone' => '0917 555 0142',
                'pickup_date' => '2026-09-18',
                'return_date' => '2026-09-25',
                'pickup_location' => 'Sibulan Airport',
                'return_location' => 'Sibulan Airport',
                'driver_age' => '30-64',
                'days' => 7,
                'delivery' => 1,
                'subtotal' => 16_800,
                'discount_code' => 'EARLY10',
                'discount' => 1_680,
                'total' => 15_620,
                'created_at' => '2026-08-30 09:14:00',
            ],
            [
                'id' => 1041,
                'status' => 'pending',
                'car_name' => 'Honda City',
                'car_img' => 'images/honda-city.png',
                'full_name' => 'Jomar Alcantara',
                'email' => 'jomar.alcantara@example.com',
                'phone' => '0918 221 7744',
                'pickup_date' => '2026-10-12',
                'return_date' => '2026-10-15',
                'pickup_location' => 'Rizal Boulevard, Dumaguete',
                'return_location' => 'Valencia',
                'driver_age' => '25-29',
                'days' => 3,
                'delivery' => 0,
                'subtotal' => 7_200,
                'discount_code' => null,
                'discount' => 0,
                'total' => 7_200,
                'created_at' => '2026-10-01 16:42:00',
            ],
            [
                'id' => 1038,
                'status' => 'confirmed',
                'car_name' => 'Mitsubishi Xpander',
                'car_img' => 'images/mitsubishi-xpander.png',
                'full_name' => 'Aileen Ramos',
                'email' => 'aileen.ramos@example.com',
                'phone' => '0995 108 2310',
                'pickup_date' => '2026-09-26',
                'return_date' => '2026-10-02',
                'pickup_location' => 'Dauin',
                'return_location' => 'Dauin',
                'driver_age' => '30-64',
                'days' => 6,
                'delivery' => 1,
                'subtotal' => 18_000,
                'discount_code' => null,
                'discount' => 0,
                'total' => 18_500,
                'created_at' => '2026-09-20 11:05:00',
            ],
            [
                'id' => 1030,
                'status' => 'completed',
                'car_name' => 'Suzuki Swift',
                'car_img' => 'images/suzuki-swift.png',
                'full_name' => 'Edgar Villanueva',
                'email' => 'edgar.villanueva@example.com',
                'phone' => '0922 744 9901',
                'pickup_date' => '2026-08-02',
                'return_date' => '2026-08-06',
                'pickup_location' => 'Bacong',
                'return_location' => 'Rizal Boulevard, Dumaguete',
                'driver_age' => '21-24',
                'days' => 4,
                'delivery' => 0,
                'subtotal' => 8_800,
                'discount_code' => null,
                'discount' => 0,
                'total' => 8_800,
                'created_at' => '2026-07-28 08:31:00',
            ],
            [
                'id' => 1025,
                'status' => 'cancelled',
                'car_name' => 'Kia Picanto',
                'car_img' => 'images/kia-picanto.png',
                'full_name' => 'Rhea Sablan',
                'email' => 'rhea.sablan@example.com',
                'phone' => '0906 332 1188',
                'pickup_date' => '2026-07-19',
                'return_date' => '2026-07-21',
                'pickup_location' => 'Valencia',
                'return_location' => 'Valencia',
                'driver_age' => '25-29',
                'days' => 2,
                'delivery' => 0,
                'subtotal' => 3_800,
                'discount_code' => null,
                'discount' => 0,
                'total' => 3_800,
                'created_at' => '2026-07-14 19:58:00',
            ],
        ];
    }
}
