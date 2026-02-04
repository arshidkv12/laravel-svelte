<?php

namespace App\Models;

use App\Models\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_no',
        'customer_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'status',
        'job_card_id',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];


    protected $appends = [
        'created_at_formatted',
    ];

    
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y h:i A')  
            : '-';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $invoice->invoice_no = 'INV-' . str_pad(
                Invoice::max('id') + rand(1, 10000),
                5,
                '0',
                STR_PAD_LEFT
            );
            if (Auth::check() && empty($jobCard->user_id)) {
                $invoice->user_id = Auth::id();
            }
        });
        
        static::addGlobalScope(new OwnerScope);
    }
}
