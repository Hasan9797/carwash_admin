<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * @param  array<int, array{label: string, url?: string}>|null  $breadcrumbs
     */
    public function __construct(
        public ?string $title = null,
        public ?string $header = null,
        public ?array $breadcrumbs = null,
    ) {}

    public function render(): View
    {
        return view('layouts.app');
    }
}
