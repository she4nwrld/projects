<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminMaintenanceController extends AbstractController
{
    private const VIEWS = ['open', 'resolved', 'all'];

    #[Route('/admin/maintenance', name: 'admin_maintenance')]
    public function index(#[MapQueryParameter] string $view = 'open'): Response
    {
        $filter = \in_array($view, self::VIEWS, true) ? $view : 'open';

        $repairs = array_values(array_filter(
            $this->sampleRepairs(),
            static function (array $job) use ($filter): bool {
                return match ($filter) {
                    'open' => 0 === $job['resolved'],
                    'resolved' => 1 === $job['resolved'],
                    default => true,
                };
            }
        ));

        return $this->render('admin_maintenance/index.html.twig', [
            'views' => self::VIEWS,
            'filter' => $filter,
            'fleet' => $this->sampleFleet(),
            'stats' => [
                'open_jobs' => 2,
                'done_jobs' => 2,
                'spend' => 23_400,
            ],
            'off_road' => 2,
            'repairs' => $repairs,
        ]);
    }

    /**
     * Every unit can be sent to the shop, hidden ones included.
     *
     * @return list<array<string, mixed>>
     */
    private function sampleFleet(): array
    {
        return [
            ['id' => 4, 'name' => 'Suzuki Swift', 'type' => 'Hatchback'],
            ['id' => 6, 'name' => 'Kia Picanto', 'type' => 'Hatchback'],
            ['id' => 2, 'name' => 'Honda City', 'type' => 'Sedan'],
            ['id' => 1, 'name' => 'Toyota Avanza', 'type' => 'MPV'],
            ['id' => 3, 'name' => 'Mitsubishi Xpander', 'type' => 'MPV'],
            ['id' => 5, 'name' => 'Ford Everest', 'type' => 'SUV'],
        ];
    }

    /**
     * Shaped like the legacy maintenance query: repairs joined onto cars,
     * with the count of live bookings the repair window lands on.
     *
     * @return list<array<string, mixed>>
     */
    private function sampleRepairs(): array
    {
        return [
            [
                'id' => 21,
                'car_id' => 3,
                'car_name' => 'Mitsubishi Xpander',
                'car_type' => 'MPV',
                'car_img' => 'images/mitsubishi-xpander.png',
                'description' => 'Rear bumper cracked on return, panel beater booked',
                'start_date' => '2026-10-06',
                'end_date' => '2026-10-13',
                'cost' => 8_500,
                'resolved' => 0,
                'created_at' => '2026-10-03 07:45:00',
                'clashes' => 1,
            ],
            [
                'id' => 20,
                'car_id' => 5,
                'car_name' => 'Ford Everest',
                'car_type' => 'SUV',
                'car_img' => 'images/ford-everest.png',
                'description' => 'Aircon compressor replaced, waiting on parts from Cebu',
                'start_date' => '2026-09-28',
                'end_date' => '2026-10-09',
                'cost' => 11_200,
                'resolved' => 0,
                'created_at' => '2026-09-27 14:20:00',
                'clashes' => 0,
            ],
            [
                'id' => 18,
                'car_id' => 4,
                'car_name' => 'Suzuki Swift',
                'car_type' => 'Hatchback',
                'car_img' => 'images/suzuki-swift.png',
                'description' => 'Windscreen chip filled, back on the road same week',
                'start_date' => '2026-08-14',
                'end_date' => '2026-08-16',
                'cost' => 1_900,
                'resolved' => 1,
                'created_at' => '2026-08-13 10:02:00',
                'clashes' => 0,
            ],
            [
                'id' => 15,
                'car_id' => 1,
                'car_name' => 'Toyota Avanza',
                'car_type' => 'MPV',
                'car_img' => 'images/toyota-avanza.png',
                'description' => 'Routine service at 60,000 km plus four new tyres',
                'start_date' => '2026-07-02',
                'end_date' => '2026-07-04',
                'cost' => 1_800,
                'resolved' => 1,
                'created_at' => '2026-06-30 09:11:00',
                'clashes' => 0,
            ],
        ];
    }
}
