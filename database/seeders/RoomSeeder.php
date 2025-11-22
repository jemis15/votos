<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $room = Event::create([
            'name' => 'Sala de Votación Principal'
        ]);

        collect([
            'Presidente',
            'Secretario',
            'Tesorero',
        ])->map(function ($name) use ($room) {
            return Cargo::create([
                'name'     => $name,
                'event_id'  => $room->id,
            ]);
        });


        $users = User::factory(30)->create();

        $candidates = $users->take(10);

        foreach ($users as $user) {
            $room->users()->attach($user->id, [
                'role_in_room' => $candidates->contains($user) ? 'both' : 'voter',
            ]);
        }
    }
}
