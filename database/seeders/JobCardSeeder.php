<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobCard;
use Illuminate\Support\Str;

class JobCardSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'new',
            'in_progress',
            'waiting_for_parts',
            'ready',
            'delivered',
        ];

        foreach (range(1, 15) as $i) {
            JobCard::create([
                'job_no' => 'JB-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_name' => 'Customer ' . $i,
                'phone' => '9' . rand(100000000, 999999999),
                'item' => 'Device Model ' . rand(1, 5),
                'problem' => 'Reported issue description',
                'status' => $statuses[array_rand($statuses)],
                'estimated_cost' => rand(500, 5000),
                'delivery_date' => now()->addDays(rand(1, 7)),
                'notes' => 'Initial inspection done',
            ]);
        }
    }
}
