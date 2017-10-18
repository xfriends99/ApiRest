<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Thujohn\Twitter\Facades\Twitter;

class TwitterController extends Controller
{
    public function user($id)
    {
        try {
            $response = Twitter::getUsers(['screen_name' => $id, 'format' => 'json']);
        } catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        return response()->json(['error' => false,
            'data' =>array_merge((array)json_decode($response),
                array_merge(['timeline' => $this->timeline($id)],
                    ['mentions' => $this->mentions($id)]))],200);
    }

    public function timeline($id)
    {
        try {
            $response = Twitter::getHomeTimeline(['screen_name' => $id, 'format' => 'json']);
        } catch(\Exception $e) {
            return [];
        }
        return (array)json_decode($response);
    }

    public function mentions($id)
    {
        try {
            $response = Twitter::getMentionsTimeline(['screen_name' => $id, 'format' => 'json']);
        } catch(\Exception $e) {
            return [];
        }
        return (array)json_decode($response);
    }


    public function retweets($post_id)
    {
        try {
            $response = Twitter::getRts($post_id);
        } catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        return response()->json(['error' => false,
            'data' =>json_decode($response)]);
    }

}
