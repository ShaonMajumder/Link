<?php

namespace Database\Seeders;

use App\Models\Link;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        // $content = json_decode(Storage::get( '/public/links.json'), true);
        $file_name = "links.json";
        $file_url = 'public/'. $file_name;
        $content = json_decode( file_get_contents(base_path($file_url)), true);
        Link::insert($content);
    }
}
