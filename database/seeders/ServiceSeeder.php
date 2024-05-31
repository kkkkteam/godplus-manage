<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceRegistation;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceArray = array(
            array(1, NULL, 'Plf8jF9GSIfRgdrl', '2024-05-04 15:00:00', '2024-05-04 16:30:00', 'God plus+ : SIGN', 'Pastor Esther', '2024-04-25 18:42:11', '2024-04-26 15:42:13', '2024-04-25 18:42:11'),
            array(2, NULL, 'HIFx1t2K07WmRdYE', '2024-05-11 15:00:00', '2024-05-11 16:30:00', 'SGIN', 'Pastor Esther', '2024-05-04 15:30:19', '2024-05-04 15:30:19', '2024-05-04 15:30:19'),
            array(3, NULL, '42GOqlzRacOdiAvA', '2024-05-18 15:00:00', '2024-05-18 16:30:00', 'Sign - 持守的印記', '吳東', '2024-05-11 15:39:48', '2024-05-11 15:39:48', '2024-05-11 15:39:48'),
            array(4, NULL, 'ZrCAGHVTvFV3WTcR', '2024-05-25 15:00:00', '2024-05-25 16:30:00', 'Sign - 應許的印記', 'Pastor Esther', '2024-05-18 15:20:24', '2024-05-18 15:20:24', '2024-05-18 16:00:00'),
            array(6, NULL, '7zSVrMj71fgwFSKg', '2024-06-01 15:00:00', '2024-06-01 17:00:00', '新的視野 - 將行新事', 'Pastor Esther', '2024-05-24 14:00:12', '2024-05-29 18:04:11', '2024-05-25 16:00:00'),
        );

        foreach($serviceArray as $service){
            Service::create([
                "slug" => $service[2],
                "start_at" => $service[3],
                "end_at" => $service[4],
                "title" => $service[5],
                "speaker" => $service[6],
                "public_at" => $service[9],
            ]);
        }
    }
}
