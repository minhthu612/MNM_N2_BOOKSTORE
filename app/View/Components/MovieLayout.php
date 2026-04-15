<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
class MovieLayout extends Component
{
    /**
     * Create a new component instance.
     */
     public $genre;
 
    public function __construct()
    {
        //
        $this->genre = DB::table("genre")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.movie-layout');
    }
}
