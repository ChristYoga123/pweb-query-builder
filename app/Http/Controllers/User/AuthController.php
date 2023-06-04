<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function google()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallBack()
    {
        $callback = Socialite::driver("google")->stateless()->user();
        $data = [
            "name" => $callback->getName(),
            "email" => $callback->getEmail(),
        ];

        // check if user is exists
        $user = DB::table('users')
            ->where('email', $data["email"])
            ->first();
        // if it is not, insert it
        if ($user === null) {
            $userId = DB::table('users')->insertGetId([
                'name' => $data["name"],
                'email' => $data["email"],
            ]);

            $user = DB::table('users')
                ->where('id', $userId)
                ->first();
        }
        $authenticatableUser = new \stdClass();
        $authenticatableUser->id = $user->id;
        // assign other properties as needed

        Auth::loginUsingId($authenticatableUser->id);

        return redirect("/");
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
