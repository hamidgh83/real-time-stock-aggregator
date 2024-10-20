<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class CustomPaginator extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(protected LengthAwarePaginator $paginator) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return $this->paginator->render('components.custom-paginator');
    }
}
