<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class GoogleController extends Controller
{
    protected $curl;

    protected $urlPerson = 'https://www.googleapis.com/plus/v1/people';

    protected $urlPosts = 'https://www.googleapis.com/plus/v1/people/{user_id}/activities/public';

    protected $urlPostsActivity = 'https://www.googleapis.com/plus/v1/activities/{activity_id}/comments';

    public function __construct()
    {
        $this->curl = new Client();
    }


    public function user($id)
    {
        try {
            $response = $this->curl->get($this->urlPerson,['query' => ['query' => $id,
                'key' => env('GOOGLE_KEY')]]);
        } catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        $response = json_decode($response->getBody());
        $user = '';
        foreach ($response->items as $item){
            if($item->objectType=='page' && strtolower($item->displayName)==strtolower($id)){
                $user = $item;
            }
        }
        if($user==''){
            return response()->json(['error' => true, 'message' => 'No se encontro coincidencia'],
                404);
        }
        return response()->json(['error' => false,
            'data' =>array_merge((array)$user, ['posts' => $this->posts($user->id)])], 200);

    }

    public function posts($id)
    {
        try {
            $response = $this->curl->get(str_replace('{user_id}', $id, $this->urlPosts),
                ['query' => ['key' => env('GOOGLE_KEY')]]);
        } catch(\Exception $e) {
            return [];
        }
        return (array)json_decode($response->getBody());
    }

    public function comments($post_id)
    {
        try {
            $response = $this->curl->get(str_replace('{activity_id}', $post_id, $this->urlPostsActivity),
                ['query' => ['key' => env('GOOGLE_KEY')]]);
        } catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        return response()->json(['error' => false,
            'data' =>json_decode($response->getBody())]);
    }

}
