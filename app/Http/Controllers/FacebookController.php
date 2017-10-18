<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class FacebookController extends Controller
{
    protected $fb;

    public function __construct(LaravelFacebookSdk $fb)
    {
        $this->fb = $fb;
    }

    public function fanpage($id)
    {
        try {
            $response = $this->fb->get("/{$id}?fields=name,location,rating_count,fan_count,talking_about_count",env('FACEBOOK_APP_TOKEN'));
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        return response()->json(['error' => false,
            'data' =>array_merge((array)json_decode($response->getBody()),
                ['posts' =>$this->posts($id)])],200);
    }

    public function posts($id)
    {
        try {
            $response = $this->fb->get("/{$id}/posts?fields=created_time,message,shares,status_type",env('FACEBOOK_APP_TOKEN'));
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [];
        }
        return (array)json_decode($response->getBody());
    }

    public function like_posts($id)
    {
        try {
            $response = $this->fb->get("/{$id}/likes",env('FACEBOOK_APP_TOKEN'));
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        return response()->json(['error' => false,
            'data' =>(array)json_decode($response->getBody())],200);
    }

    public function comment_posts($id)
    {
        try {
            $response = $this->fb->get("/{$id}/comments",env('FACEBOOK_APP_TOKEN'));
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()],404);
        }
        return response()->json(['error' => false,
            'data' =>(array)json_decode($response->getBody())],200);
    }
}
