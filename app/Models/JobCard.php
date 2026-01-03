<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_no',
        'customer_id',
        'item',
        'problem',
        'status',
        'estimated_cost',
        'delivery_date',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'estimated_cost' => 'decimal:2',
    ];

    protected $appends = [
        'delivery_date_formatted',
        'created_at_formatted',
    ];

    public function getDeliveryDateFormattedAttribute(): string
    {
        return $this->delivery_date
            ? $this->delivery_date->format('M d, Y') // Jan 03, 2026
            : '-';
    }

    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y h:i A')  
            : '-';
    }
}
