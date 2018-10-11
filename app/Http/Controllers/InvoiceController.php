<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function create()
    {
        return view('invoice.create');
    }

    public function store()
    {
        Invoice::create([
            'number' => request('number'),
            'date'   => request('date'),
            'amount' => request('amount') ? intval(floatval(request('amount')) * 100) : null,
        ]);

        return redirect(route('invoice.index'));
    }
}
