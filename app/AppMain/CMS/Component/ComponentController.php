<?php

namespace App\AppMain\CMS\Component;

use App\Http\Controllers\Controller;

class ComponentController extends Controller
{
    /**
     * Show component showcase page
     */
    public function index()
    {
        return view('admin.component.index');
    }
}
