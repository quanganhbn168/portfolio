<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class ServicesController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function __invoke(): View
    {
        $page = $this->systemPages->require('services');

        return view('frontend.services.index', [
            'page' => $page,
            'services' => Service::query()->active()->get(),
            'plans' => PricingPlan::query()->active()->get(),
            'seo' => $this->seo->systemPage($page, 'services.index'),
        ]);
    }
}
