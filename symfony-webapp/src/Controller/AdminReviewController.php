<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminReviewController extends AbstractController
{
    private const VIEWS = ['all', 'visible', 'hidden'];

    #[Route('/admin/reviews', name: 'admin_review')]
    public function index(#[MapQueryParameter] string $view = 'all'): Response
    {
        $filter = \in_array($view, self::VIEWS, true) ? $view : 'all';

        $reviews = array_values(array_filter(
            $this->sampleReviews(),
            static function (array $review) use ($filter): bool {
                return match ($filter) {
                    'visible' => 1 === $review['approved'],
                    'hidden' => 0 === $review['approved'],
                    default => true,
                };
            }
        ));

        return $this->render('admin_review/index.html.twig', [
            'views' => self::VIEWS,
            'filter' => $filter,
            'stats' => [
                'total' => 4,
                'shown' => 3,
                'hidden' => 1,
                'avg_rating' => 4.7,
            ],
            'reviews' => $reviews,
        ]);
    }

    /**
     * Shaped like the legacy reviews query: reviews joined onto users.
     *
     * @return list<array<string, mixed>>
     */
    private function sampleReviews(): array
    {
        return [
            [
                'id' => 301,
                'full_name' => 'Maria Dela Cruz',
                'email' => 'maria.delacruz@example.com',
                'rating' => 5,
                'review_text' => "The Avanza was spotless and they met us right at the airport.\nWill book again on our next trip down south.",
                'approved' => 1,
                'created_at' => '2026-09-02 15:22:00',
            ],
            [
                'id' => 300,
                'full_name' => 'Edgar Villanueva',
                'email' => 'edgar.villanueva@example.com',
                'rating' => 5,
                'review_text' => 'Easy pickup in Bacong and the rate was exactly what was quoted. No surprises at all.',
                'approved' => 1,
                'created_at' => '2026-08-11 09:48:00',
            ],
            [
                'id' => 298,
                'full_name' => 'Aileen Ramos',
                'email' => 'aileen.ramos@example.com',
                'rating' => 4,
                'review_text' => 'Good car and friendly staff. Only note is that pickup ran about twenty minutes late.',
                'approved' => 1,
                'created_at' => '2026-07-26 18:03:00',
            ],
            [
                'id' => 295,
                'full_name' => 'Rhea Sablan',
                'email' => 'rhea.sablan@example.com',
                'rating' => 1,
                'review_text' => 'Posted in frustration after a cancelled trip. Hidden while we sort it out with the customer.',
                'approved' => 0,
                'created_at' => '2026-07-20 21:14:00',
            ],
        ];
    }
}
