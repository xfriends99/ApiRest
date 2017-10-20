<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class YoutubeController extends Controller
{
    protected $curl;

    protected $urlAccounts = 'https://www.googleapis.com/youtube/v3/search';

    protected $urlComments = 'https://www.googleapis.com/youtube/v3/commentThreads';

    public function __construct()
    {
        $this->curl = new Client();
    }

    public function user($id)
    {
        try {
            $response = $this->curl->get($this->urlAccounts,['query' =>
                ['q' => $id, 'part' => 'snippet',
                    'key' => env('GOOGLE_KEY'),
                    'type' => 'channel']]);
        } catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        $response = json_decode($response->getBody());
        $user = '';
        foreach ($response->items as $item){
            if(strtolower($item->snippet->title)==strtolower($id)){
                $user = $item;
            }
        }
        if($user==''){
            return response()->json(['error' => true, 'message' => 'No se encontro coincidencia'],
                404);
        }
        return response()->json(['error' => false,
            'data' =>array_merge((array)$user, ['posts' => $this->posts($user->id->channelId)])], 200);

    }

    public function posts($id)
    {
        try {
            $response = $this->curl->get($this->urlAccounts,
                ['query' =>
                    ['key' => env('GOOGLE_KEY'),
                    'channelId' => $id,
                    'part'  => 'snippet,id',
                    'order' => 'date',
                    'maxResults' => 20,
                    'type' => 'video']
                ]);
        } catch(\Exception $e) {
            return [];
        }
        return (array)json_decode($response->getBody());
    }

    public function comments($post_id)
    {
        try {
            $response = $this->curl->get($this->urlComments,
                ['query' =>
                    ['key' => env('GOOGLE_KEY'),
                    'part' => 'snippet,id,replies',
                    'videoId' => $post_id]
                ]);
        } catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        return response()->json(['error' => false,
            'data' =>json_decode($response->getBody())]);
    }
}
