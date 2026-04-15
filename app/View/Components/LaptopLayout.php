<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
class LaptopLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public $categories;
 
    public function __construct()
    {
        //
        $this->categories = DB::table("danh_muc_laptop")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.laptop-layout');
    }
}
