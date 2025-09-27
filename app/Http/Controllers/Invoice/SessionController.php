<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;

class SessionController extends Controller
{
    public function update(Invoice $invoice, Session $session): RedirectResponse
    {
        $session->attachToInvoice($invoice);

        return redirect()->route('invoice.session.index', ['invoice' => $invoice]);
    }
}
