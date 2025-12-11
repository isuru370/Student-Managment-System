<?php

namespace App\Http\Controllers;

use App\Services\LegarSummaryService;
use Illuminate\Http\Request;

class LegarSummaryController extends Controller
{
    protected $legarSummaryService;
    
    public function __construct(LegarSummaryService $legarSummaryService)
    {
        $this->legarSummaryService = $legarSummaryService;
    }
}
