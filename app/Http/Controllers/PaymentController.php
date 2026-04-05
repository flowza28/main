<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\FreeRadiusService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected FreeRadiusService $freeRadius;

    public function __construct(FreeRadiusService $freeRadius)
    {
        $this->freeRadius = $freeRadius;
    }

    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'method' => 'required|string|max:50',
            'paid_at' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'amount' => $data['amount'],
            'method' => $data['method'],
            'paid_at' => $data['paid_at'],
            'note' => $data['note'] ?? null,
        ]);

        $invoice->status = 'lunas';
        $invoice->paid_at = $data['paid_at'];
        $invoice->save();

        $customer = $invoice->customer;
        $customer->expired_at = $customer->expired_at && $customer->expired_at->isFuture()
            ? $customer->expired_at->copy()->addMonth()
            : now()->addMonth();
        $customer->active = true;
        $customer->status = 'active';
        $customer->save();

        $this->freeRadius->syncCustomer($customer);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Pembayaran berhasil disimpan dan masa aktif diperpanjang.');
    }
}
