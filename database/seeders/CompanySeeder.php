<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'company_name' => 'コカ・コーラ',
            'street_address' => '東京都渋谷区',
            'representative_name' => '山田 太郎',
        ]);

        Company::create([
            'company_name' => 'サントリー',
            'street_address' => '大阪府大阪市',
            'representative_name' => '佐藤 次郎',
        ]);

        Company::create([
            'company_name' => '伊藤園',
            'street_address' => '東京都渋谷区',
            'representative_name' => '鈴木 花子',
        ]);
    }
}
