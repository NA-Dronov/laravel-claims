<?php

use Illuminate\Database\Seeder;

class ClaimStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'code' => 'O',
                'status' => 'Открыта',
            ],
            [
                'code' => 'P',
                'status' => 'В обработке',
            ],
            [
                'code' => 'C',
                'status' => 'Закрыта',
            ],
        ];

        \DB::table('claim_statuses')->insert($data);
    }
}
