<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facades\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $invoices = Invoice::with('customer')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'status'));
    }

    public function generateMonthly()
    {
        $customers = Customer::where('active', true)->get();

        foreach ($customers as $customer) {
            Invoice::updateOrCreate(
                [
                    'customer_id' => $customer->id,
                    'invoice_number' => 'ISP-' . now()->format('Ym') . '-' . $customer->id,
                ],
                [
                    'amount' => $customer->package->price,
                    'due_date' => now()->endOfMonth(),
                    'status' => 'belum',
                    'description' => 'Tagihan paket ' . $customer->package->name,
                ]
            );
        }

        return redirect()->route('invoices.index')->with('success', 'Tagihan bulanan berhasil digenerate.');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
