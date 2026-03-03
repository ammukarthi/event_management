<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $events = [
            [
                'title'       => 'Laravel Conference 2025',
                'description' => 'A premier conference for Laravel developers worldwide.',
                'date'        => Carbon::create(2026, 3, 15, 10, 0, 0),
                'location'    => 'New York',
                'user_id'     => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'PHP Summit',
                'description' => 'Explore the latest trends and updates in the PHP ecosystem.',
                'date'        => Carbon::create(2026, 4, 20, 9, 0, 0),
                'location'    => 'Berlin',
                'user_id'     => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Open Source Meetup',
                'description' => 'Connect with open source contributors and maintainers.',
                'date'        => Carbon::create(2026, 5, 5, 14, 0, 0),
                'location'    => 'London',
                'user_id'     => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Web Dev Workshop',
                'description' => 'Hands-on workshop covering modern web development techniques.',
                'date'        => Carbon::create(2026, 6, 10, 11, 0, 0),
                'location'    => 'Toronto',
                'user_id'     => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Cloud & DevOps Expo',
                'description' => 'Deep dive into cloud infrastructure and DevOps best practices.',
                'date'        => Carbon::create(2026, 7, 22, 9, 30, 0),
                'location'    => 'Sydney',
                'user_id'     => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'AI & Machine Learning Forum',
                'description' => 'Discussions on the latest advancements in AI and ML.',
                'date'        => Carbon::create(2026, 8, 18, 10, 0, 0),
                'location'    => 'San Francisco',
                'user_id'     => 4,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Startup Pitch Night',
                'description' => 'An evening where startups pitch their ideas to investors.',
                'date'        => Carbon::create(2026, 9, 3, 18, 0, 0),
                'location'    => 'Dubai',
                'user_id'     => 4,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Cybersecurity Bootcamp',
                'description' => 'Intensive training on modern cybersecurity threats and defenses.',
                'date'        => Carbon::create(2026, 10, 12, 9, 0, 0),
                'location'    => 'Singapore',
                'user_id'     => 4,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'UX/UI Design Sprint',
                'description' => 'A collaborative sprint focused on user experience and interface design.',
                'date'        => Carbon::create(2026, 11, 7, 13, 0, 0),
                'location'    => 'Amsterdam',
                'user_id'     => 5,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Year-End Tech Gala',
                'description' => 'Celebrate the year\'s tech achievements with industry leaders.',
                'date'        => Carbon::create(2026, 12, 20, 19, 0, 0),
                'location'    => 'Paris',
                'user_id'     => 5,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('events')->insert($events);
    }
}
