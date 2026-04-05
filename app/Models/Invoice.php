<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'invoice_number',
        'amount',
        'due_date',
        'status',
        'description',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function generateExpiringInvoices(): int
    {
        $customers = Customer::where('active', true)
            ->whereNotNull('expired_at')
            ->whereBetween('expired_at', [now(), now()->addDays(2)])
            ->get();

        $createdCount = 0;

        foreach ($customers as $customer) {
            $invoiceNumber = 'ISP-' . $customer->expired_at->format('Ym') . '-' . $customer->id;

            $existingInvoice = self::where('customer_id', $customer->id)
                ->where('invoice_number', $invoiceNumber)
                ->where('status', 'belum')
                ->first();

            if ($existingInvoice) {
                continue;
            }

            self::create([
                'customer_id' => $customer->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $customer->package->price,
                'due_date' => $customer->expired_at,
                'status' => 'belum',
                'description' => 'Invoice otomatis karena paket akan expired dalam 2 hari',
            ]);

            $createdCount++;
        }

        return $createdCount;
    }
}
