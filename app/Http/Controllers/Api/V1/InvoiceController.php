<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreInvoiceRequest;
use App\Http\Requests\Api\V1\UpdateInvoiceRequest;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $invoices = Invoice::query()
            ->with('client')
            ->orderByDesc('id')
            ->paginate();

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['amount'])) {
            $data['amount'] = (int) (floatval($data['amount']) * 100);
        }

        $invoice = Invoice::create($data);

        return (new InvoiceResource($invoice->load('client')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->load('client'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): InvoiceResource
    {
        $data = $request->validated();

        if (isset($data['amount'])) {
            $data['amount'] = (int) (floatval($data['amount']) * 100);
        }

        $invoice->update($data);

        return new InvoiceResource($invoice->fresh('client'));
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();

        return response()->json(null, 204);
    }
}
