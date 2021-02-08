<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Post|null $post
     * @return AnonymousResourceCollection
     */
    public function index(Request $request, Post $post): AnonymousResourceCollection
    {
        $comments = $post->comments();
        if ($request->has('sort') && $request->get('sort') == 'desc') {
            $comments->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('count') ?? 25;

        return CommentResource::collection($comments->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'comment' => 'required|min:10|max:200'
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $request->get('comment')
        ]);

        return response()->json([
            'success' => true
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function show(Comment $comment)
    {
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreCommentRequest $request
     * @param Post $post
     * @param Comment $comment
     * @return JsonResponse
     */
    public function update(StoreCommentRequest $request, Post $post, Comment $comment): JsonResponse
    {
        $data = $request->validated();

        $comment->update($data);

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
        //
    }
}
