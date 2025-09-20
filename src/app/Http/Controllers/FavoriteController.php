<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Item $item)
    {
        $user = Auth::user();

        if ($item->favoritedBy()->where('user_id', $user->id)->exists()) {
            $item->favoritedBy()->detach($user->id); // 解除
        } else {
            $item->favoritedBy()->attach($user->id); // 追加
        }

        return back();
    }
}
