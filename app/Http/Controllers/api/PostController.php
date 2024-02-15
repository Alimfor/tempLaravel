<?php

namespace App\Http\Controllers\api;

use App\Dto\PostEventPayloadDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Kafka\PostEventType;
use App\Kafka\PostProducer;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

function convertTo(Post $post, PostEventType $type): PostEventPayloadDTO
{
    return new PostEventPayloadDTO(
        $post->id,
        $post->username,
        $post->imageUrl,
        $post->caption,
        $type,
        $post->lastModifiedBy,
        $post->createdAt,
        $post->updatedAt
    );
}

class PostController extends Controller
{
    public function store(PostRequest $request, PostProducer $producer): JsonResponse
    {

        $userData = 'alimzhan';// TODO-> here should be retrieving user data from JWT
        $username = $userData;//TODO-> the $userData should contain authenticated user data
        $lastModifiedBy = $username;

        $uuid = Uuid::uuid4()->toString();
        $post_key = $username . ':' . $uuid;
        $timestamp = now()->timestamp;

        $post = new Post(
            [
                'imageUrl' => $request->imageUrl,
                'caption' => $request->caption,
                'lastModifiedBy' => $lastModifiedBy,
                'username' => $username,
                'createdAt' => $timestamp,
                'updatedAt' => $timestamp
            ]);

        Redis::lPush($post_key, json_encode($post));

        $producer->produce(convertTo($post, PostEventType::CREATED));

        return response()->json($post);
    }

    public function destroy(Request $request, PostProducer $producer): Response
    {

        $userData = 'alimzhan';// TODO-> here should be retrieving user data from JWT
        $post_key = $userData . ':' . $request->postId;


        $post = Redis::lrange($post_key, 0, -1);
        $producer->produce(convertTo($post, PostEventType::DELETED));

        Redis::del($post_key);

        return response()->noContent();
    }

    public function findCurrentUserPosts(): JsonResponse
    {

        $userData = 'alimzhan';// TODO-> here should be retrieving user data from JWT
        $username = $userData;//TODO-> the $userData should contain authenticated user data

        $post_keys = Redis::keys("$username:*");
        $posts = collect($post_keys)
            ->map(function ($post_key) {
                $position = strpos($post_key, ':');
                $result = substr($post_key, $position + 1);
                return Redis::lrange($result, 0, -1);
            })
            ->flatMap(function ($post) {
                return $post;
            })
            ->map(function ($post) {
                return json_decode($post, true);
            })
            ->map(function ($decodedPost) {
                return new Post($decodedPost);
            });

        return response()->json($posts);
    }

    public function findPostsByIdIn(Request $request)
    {
        $userData = 'alimzhan';// TODO-> here should be retrieving user data from JWT
        $username = $userData;//TODO-> the $userData should contain authenticated user data
        $ids = $request->json()->all();

        $posts = collect($ids)
            ->map(function ($id) use ($username) {
                $postKey = "$username:$id";
                return Redis::lrange($postKey, 0, -1);
            })
            ->flatMap(function ($post) {
                return $post;
            })
            ->map(function ($post) {
                return json_decode($post, true);
            })
            ->map(function ($decodePost) {
                return new Post($decodePost);
            });

        return response()->json($posts);
    }
}
