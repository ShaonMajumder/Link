<?php

namespace App\Http\Components;

use Illuminate\Http\Request;

trait WebTrait{
    // protected $status   = false;
    // protected $message  = "Error";
    // protected $access_token;
    // protected $data;

    public function getLinkDetails($url){
        // Extract HTML using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        // Load HTML to DOM object
        $dom = new \DOMDocument();
        @$dom->loadHTML($data);
        // Parse DOM to get Title data
        $nodes = $dom->getElementsByTagName('title');
        $title = $nodes->item(0)->nodeValue;
        
        // Parse DOM to get meta data
        $metas = $dom->getElementsByTagName('meta');
        $description = '';
        for($i=0; $i<$metas->length; $i++){
            $meta = $metas->item($i);
            print_r($meta);
            
            if($meta->getAttribute('name') == 'description'){
                $description = $meta->getAttribute('content');
            }
    
        }
        
        return [
            'title' => $title,
            'description' => $description,
            // 'meta' => $meta
        ];
    }
}