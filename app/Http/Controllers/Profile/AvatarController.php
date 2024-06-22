<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Str;

class AvatarController extends Controller {
    public function update(UpdateAvatarRequest $request) {

        $path = Storage::disk('public')->put('avatars', $request->file('avatar'));

        if ($oldavatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldavatar);
        }

        auth()->user()->update(['avatar' => $path]);

        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }

    public function generate(Request $request) {

        $result = OpenAI::images()->create([
            'prompt' => 'create an avatar for the user with cool style animated in tech world',
            'n' => 1,
            'size' => '256x256',
        ]);

        $content = file_get_contents($result->data[0]->url);

        if ($oldavatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldavatar);
        }

        $filename = Str::random(25);
        Storage::disk('public')->put("avatars/$filename.jpg", $content);

        auth()->user()->update(['avatar' => "avatars/$filename.jpg"]);

        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }
}
