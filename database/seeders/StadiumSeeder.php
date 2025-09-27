<?php

namespace Database\Seeders;

use App\Models\Pitch;
use App\Models\Stadium;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $stadiumCount = config('app.seed_stadium_count');
        $pitchCount = config('app.seed_pitch_count');

        Stadium::factory()
            ->count($stadiumCount)
            ->create()
            ->each(function ($stadium) use ($pitchCount) {
                Pitch::factory()
                    ->count($pitchCount)
                    ->create(['stadium_id' => $stadium->id]);
            });
    }
}
