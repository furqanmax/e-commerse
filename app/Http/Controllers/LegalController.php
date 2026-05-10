<?php

namespace App\Http\Controllers;

use Shopper\Core\Models\Legal;

class LegalController extends Controller
{
    public function privacy()
    {
        return view('legal.privacy', [
            'legal' => Legal::enabled()->where('slug', 'privacy')->first(),
        ]);
    }

    public function refund()
    {
        return view('legal.refund', [
            'legal' => Legal::enabled()->where('slug', 'refund')->first(),
        ]);
    }

    public function terms()
    {
        return view('legal.terms', [
            'legal' => Legal::enabled()->where('slug', 'terms')->first(),
        ]);
    }

    public function shipping()
    {
        return view('legal.shipping', [
            'legal' => Legal::enabled()->where('slug', 'shipping')->first(),
        ]);
    }
}