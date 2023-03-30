<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $ipfile = explode("\n" , str_replace("\r" , "" , file_get_contents(storage_path('app/bots/ip.txt'))));
        $ispfile = explode("\n" , str_replace("\r" , "" , file_get_contents(storage_path('app/bots/isp.txt'))));
        $hostfile = explode("\n" , str_replace("\r" , "" , file_get_contents(storage_path('app/bots/hostname.txt'))));
        $uafile = explode("\n" , str_replace("\r" , "" , file_get_contents(storage_path('app/bots/useragent.txt'))));
        $uafile2 = explode("\n" , str_replace("\r" , "" , file_get_contents(storage_path('app/bots/ua-full.txt'))));

        foreach($ipfile as $ip)
        {
            \App\Models\Ip::create([
                'content' => $ip,
                'reason' => 'Bot IP Detected',
            ]);
        }

        foreach($ispfile as $isp)
        {
            \App\Models\Isp::create([
                'content' => $isp,
                'reason' => 'Bot ISP Detected',
            ]);
        }

        foreach($hostfile as $host)
        {
            \App\Models\Hostname::create([
                'content' => $host,
                'reason' => 'Bot Hostname Detected',
            ]);
        }

        foreach($uafile as $ua)
        {
            \App\Models\Useragent::create([
                'content' => $ua,
                'reason' => 'Bot User Agent Detected',
            ]);
        }

        foreach($uafile2 as $ua)
        {
            \App\Models\Useragent::create([
                'content' => $ua,
                'reason' => 'Bot User Agent Detected',
            ]);
        }

    }
}
