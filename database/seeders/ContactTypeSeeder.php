<?php

namespace Database\Seeders;

use Modules\Core\Models\ContactType;
use Illuminate\Database\Seeder;

class ContactTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['nama' => 'HP / Telepon',  'icon' => 'phone'],
            ['nama' => 'WhatsApp',      'icon' => 'message-circle'],
            ['nama' => 'Email',         'icon' => 'mail'],
            ['nama' => 'Telegram',      'icon' => 'send'],
            ['nama' => 'Instagram',     'icon' => 'instagram'],
            ['nama' => 'Facebook',      'icon' => 'facebook'],
            ['nama' => 'Website',       'icon' => 'globe'],
        ];

        foreach ($types as $data) {
            ContactType::firstOrCreate(['nama' => $data['nama']], $data);
        }
    }
}
