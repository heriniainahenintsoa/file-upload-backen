<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['show', 'index']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->get();
        return response([
            "posts" => $posts->load(["user", "images"]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "content" => "required|string",
            "images.*" => "image|mimes:jpeg,png,jpg,gif,svg"
        ]);
        $post = $request->user()->posts()->create([
            'content' => $request->input('content'),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts/images', 'public');
                $url = Storage::disk('public')->url($path);
                $post->images()->create([
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'url' => $url,
                ]);
            }
        }


        return response([
            'post' => $post->load(["user", "images"]),
            'message' => "Post created successfully"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response([
            'post' => $post->load(["user", "images"])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize("modify", $post);
        $fields = $request->validate([
            "content" => "required|string",
            "images.*" => "image|mimes:jpeg,png,jpg,gif,svg"
        ]);
        $post->update([
            'content' => $fields['content']
        ]);
        $post->save();

        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts/images', 'public');
                $url = Storage::disk('public')->url($path);
                $post->images()->create([
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'url' => $url,
                ]);
            }
        }

        return response([
            'post' => $post->load(["user", "images"]),
            'message' => "Post updated successfully"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize("modify", $post);
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        $post->delete();
        return response([
            'message' => "Post deleted successfully"
        ], 200);
    }
}
