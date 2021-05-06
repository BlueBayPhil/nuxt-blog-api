<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('count') ?? 25;
        $order = $request->get('sort') ?? 'desc';


        return PostResource::collection(Post::orderBy('created_at', $order)->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePostRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            $data['image'] = $request->file('image')->store('posts');
        }

        try {
            $post = $request->user()->posts()->create($data);
        } catch (\Exception $e) {
            // Delete uploaded image when post creation failed to prevent orphaned files
            if (isset($data['image'])) {
                Storage::delete($data['image']);
            }

            // Bubble up
            throw $e;
        }

        return response()->json([
            'success', true,
            'url' => url('/api/post/' . $post->id)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StorePostRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(StorePostRequest $request, Post $post): JsonResponse
    {
        $data = $request->validated();

        $post->update($data);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //w
    }
}
