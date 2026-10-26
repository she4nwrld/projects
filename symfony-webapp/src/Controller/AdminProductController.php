<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminProductController extends AbstractController
{
    private const VIEWS = ['all', 'available', 'hidden'];

    /** The public filter categories, minus the "All" pill. */
    private const TYPES = ['Hatchback', 'Sedan', 'SUV', 'MPV'];

    #[Route('/admin/products', name: 'admin_product')]
    public function index(#[MapQueryParameter] string $view = 'all'): Response
    {
        $filter = \in_array($view, self::VIEWS, true) ? $view : 'all';

        $cars = array_values(array_filter(
            $this->sampleCars(),
            static function (array $car) use ($filter): bool {
                return match ($filter) {
                    'available' => 1 === $car['available'],
                    'hidden' => 0 === $car['available'],
                    default => true,
                };
            }
        ));

        return $this->render('admin_product/index.html.twig', [
            'views' => self::VIEWS,
            'filter' => $filter,
            'types' => self::TYPES,
            'stats' => [
                'total' => 6,
                'live' => 5,
                'off' => 1,
                'avg_price' => 2_567,
            ],
            'cars' => $cars,
        ]);
    }

    /**
     * Shaped like the legacy fleet query: cars with booking counts attached.
     *
     * @return list<array<string, mixed>>
     */
    private function sampleCars(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Toyota Avanza',
                'type' => 'MPV',
                'price' => 2_400,
                'gear' => 'Manual',
                'seats' => 7,
                'doors' => 5,
                'bag_large' => 2,
                'bag_small' => 2,
                'kids' => 1,
                'aircon' => 1,
                'img' => 'images/toyota-avanza.png',
                'available' => 1,
                'bookings' => 12,
                'live' => 2,
                'repair_note' => null,
                'repair_until' => null,
            ],
            [
                'id' => 2,
                'name' => 'Honda City',
                'type' => 'Sedan',
                'price' => 2_400,
                'gear' => 'Auto',
                'seats' => 5,
                'doors' => 4,
                'bag_large' => 2,
                'bag_small' => 1,
                'kids' => 1,
                'aircon' => 1,
                'img' => 'images/honda-city.png',
                'available' => 1,
                'bookings' => 8,
                'live' => 1,
                'repair_note' => null,
                'repair_until' => null,
            ],
            [
                'id' => 3,
                'name' => 'Mitsubishi Xpander',
                'type' => 'MPV',
                'price' => 3_000,
                'gear' => 'Auto',
                'seats' => 7,
                'doors' => 5,
                'bag_large' => 3,
                'bag_small' => 2,
                'kids' => 2,
                'aircon' => 1,
                'img' => 'images/mitsubishi-xpander.png',
                'available' => 1,
                'bookings' => 5,
                'live' => 1,
                'repair_note' => 'Rear bumper cracked on return, panel beater booked',
                'repair_until' => '2026-10-13',
            ],
            [
                'id' => 4,
                'name' => 'Suzuki Swift',
                'type' => 'Hatchback',
                'price' => 2_200,
                'gear' => 'Manual',
                'seats' => 5,
                'doors' => 5,
                'bag_large' => 1,
                'bag_small' => 2,
                'kids' => 1,
                'aircon' => 1,
                'img' => 'images/suzuki-swift.png',
                'available' => 1,
                'bookings' => 3,
                'live' => 0,
                'repair_note' => null,
                'repair_until' => null,
            ],
            [
                'id' => 5,
                'name' => 'Ford Everest',
                'type' => 'SUV',
                'price' => 4_000,
                'gear' => 'Auto',
                'seats' => 7,
                'doors' => 5,
                'bag_large' => 3,
                'bag_small' => 3,
                'kids' => 2,
                'aircon' => 1,
                'img' => 'images/ford-everest.png',
                'available' => 1,
                'bookings' => 0,
                'live' => 0,
                'repair_note' => 'Aircon compressor replaced, waiting on parts from Cebu',
                'repair_until' => '2026-10-09',
            ],
            [
                'id' => 6,
                'name' => 'Kia Picanto',
                'type' => 'Hatchback',
                'price' => 1_900,
                'gear' => 'Manual',
                'seats' => 4,
                'doors' => 5,
                'bag_large' => 1,
                'bag_small' => 1,
                'kids' => 0,
                'aircon' => 1,
                'img' => 'images/kia-picanto.png',
                'available' => 0,
                'bookings' => 0,
                'live' => 0,
                'repair_note' => null,
                'repair_until' => null,
            ],
        ];
    }
}
