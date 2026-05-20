<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminDashboardCache;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'admin')]
class DashboardController extends Controller
{
    #[Route(uri: '/', name: 'admin.dashboard')]
    public function index(): Response
    {
        $counts = AdminDashboardCache::counts();

        return view('admin.dashboard', [
            'totalPosts' => (int) ($counts['totalPosts'] ?? 0),
            'totalCategories' => (int) ($counts['totalCategories'] ?? 0),
            'totalTags' => (int) ($counts['totalTags'] ?? 0),
        ]);
    }
}
