<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public $category = ['electronics', 'Life_Style', 'decoration', 'Kitchen', 'Footwear', 'motorcycle'];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        foreach ($this->category as $key => $value) {
            DB::table('categories')->insert([
                'name' => $value
            ]);
        }
    }
}
