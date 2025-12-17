<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างข้อมูลจำลอง 50 รายการ
        Item::factory()->count(50)->create();
    }
}
