<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function updateProfileImage(Request $request)
    {
        $request->validate(["image" => "image|required|max:2048"]);
        $user = $request->user();

        if ($request->hasFile('image')) {

            if ($user->image_path && Storage::disk('public')->exists($user->image_path)) {
                Storage::disk('public')->delete($user->image_path);
            }

            $image = $request->file('image');
            $path = $image->store('users/images', 'public');
            $url = Storage::disk('public')->url($path);

            $fields = [
                'image_path' => $path,
                'image_name' => $image->getClientOriginalName(),
                'image_url' => $url,
            ];

            $user->update($fields);

            return response([
                'message' => 'Profile image updated successfully.',
                'user' => $user
            ], 200);
        } else {
            return response([
                "errors" => ["image" => "The image field is required"]
            ], 422);
        }
    }
}
