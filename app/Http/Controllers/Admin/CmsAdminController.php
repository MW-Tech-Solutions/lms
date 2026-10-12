<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\View\View;

class CmsAdminController extends Controller
{
    public function index(): View
    {
        $pages = CmsPage::with('sections')->get();

        return view('admin.cms.index', compact('pages'));
    }
}
