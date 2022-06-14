<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::query()
        ->when(
            $user = Auth::user(),
            function ($query) use ($user) {
                $query->visibleForAuthenticated($user)
                    ->with([
                        'comments' => fn ($query) => $query->visibleForAuthenticated($user)->latest(),
                    ])
                    ->withCount([
                        'comments as total_comments' => fn ($query) => $query->visibleForAuthenticated($user),
                    ])
                ;
            },
            function ($query) {
                $query->visibleForGuests()
                    ->with([
                        'comments' => fn ($query) => $query->visibleForGuests()->latest(),
                    ])
                    ->withCount([
                        'comments as total_comments' => fn ($query) => $query->visibleForGuests(),
                    ])
                ;
            },
        )
        ->lazy()
        ->map(function ($post) {
            $post->setRelation('comments', $post->comments->lazy()->take(5));
            return $post;
        });

        $paginated = new LengthAwarePaginator($posts, $posts->count(), Post::query()->getModel()->getPerPage());

        return response($paginated, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::storePost(
            $request->validated(),
            $request->user()
        );

        return response($post, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response($post, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post = $post->updatePost($request->validated());

        return response($post, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->deletePost();

        return response('Post deleted.', Response::HTTP_ACCEPTED);
    }
}
