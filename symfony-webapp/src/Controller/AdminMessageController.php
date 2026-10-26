<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminMessageController extends AbstractController
{
    private const VIEWS = ['all', 'unread', 'read'];

    #[Route('/admin/messages', name: 'admin_message')]
    public function index(#[MapQueryParameter] string $view = 'unread'): Response
    {
        $filter = \in_array($view, self::VIEWS, true) ? $view : 'unread';

        $messages = array_values(array_filter(
            $this->sampleMessages(),
            static function (array $message) use ($filter): bool {
                return match ($filter) {
                    'unread' => 0 === $message['is_read'],
                    'read' => 1 === $message['is_read'],
                    default => true,
                };
            }
        ));

        return $this->render('admin_message/index.html.twig', [
            'views' => self::VIEWS,
            'filter' => $filter,
            'stats' => [
                'total' => 5,
                'unread' => 3,
                'members' => 2,
                'week' => 3,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Shaped like the legacy inbox query: messages left-joined onto users.
     *
     * @return list<array<string, mixed>>
     */
    private function sampleMessages(): array
    {
        return [
            [
                'id' => 540,
                'name' => 'Grace Montenegro',
                'email' => 'grace.montenegro@example.com',
                'phone' => '0917 884 2201',
                'account_name' => null,
                'body' => "Do you allow the car to be taken on the ferry to Siquijor?\nPlanning four days at the end of the month.",
                'is_read' => 0,
                'created_at' => '2026-10-03 08:26:00',
            ],
            [
                'id' => 539,
                'name' => 'Aileen Ramos',
                'email' => 'aileen.ramos@example.com',
                'phone' => '0995 108 2310',
                'account_name' => 'Aileen Ramos',
                'body' => 'Can I extend my current booking by two more days? Happy to pay the difference on pickup.',
                'is_read' => 0,
                'created_at' => '2026-10-02 19:40:00',
            ],
            [
                'id' => 538,
                'name' => 'Tomas Ferrer',
                'email' => 'tomas.ferrer@example.com',
                'phone' => null,
                'account_name' => null,
                'body' => 'Is there a child seat available for the Xpander? Travelling with a two year old.',
                'is_read' => 0,
                'created_at' => '2026-09-30 11:17:00',
            ],
            [
                'id' => 536,
                'name' => 'Maria Dela Cruz',
                'email' => 'maria.delacruz@example.com',
                'phone' => '0917 555 0142',
                'account_name' => 'Maria Dela Cruz',
                'body' => 'Thanks for sorting the airport pickup so quickly. Everything went smoothly.',
                'is_read' => 1,
                'created_at' => '2026-09-14 16:05:00',
            ],
            [
                'id' => 533,
                'name' => 'Lito Bascon',
                'email' => 'lito.bascon@example.com',
                'phone' => '0920 447 3312',
                'account_name' => null,
                'body' => 'What is the deposit for a weekly hire of the Everest?',
                'is_read' => 1,
                'created_at' => '2026-08-28 13:31:00',
            ],
        ];
    }
}
