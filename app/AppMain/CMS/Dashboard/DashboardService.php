<?php

namespace App\AppMain\CMS\Dashboard;

use App\AppMain\Domain\User\Repositories\UserRepository;

class DashboardService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics(): array
    {
        return [
            'totalUsers' => $this->userRepository->count(),
        ];
    }
}
