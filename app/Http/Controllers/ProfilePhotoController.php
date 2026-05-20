<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePhotoController extends Controller
{
    public function show(User $user): StreamedResponse|Response
    {
        abort_if(!auth()->check(), 403);
        abort_if(!$user->profile_photo_path, 404);
        abort_if(!Storage::disk('public')->exists($user->profile_photo_path), 404);

        return Storage::disk('public')->response($user->profile_photo_path);
    }
}
