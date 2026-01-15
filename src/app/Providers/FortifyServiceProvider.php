<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 新規登録処理
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
    }

    public function boot()
    {
        /*
        |--------------------------------------------------------------------------
        | View 設定
        |--------------------------------------------------------------------------
        */
        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        /*
        |--------------------------------------------------------------------------
        | ログイン認証処理（Laravel8 正解）
        |--------------------------------------------------------------------------
        */
        Fortify::authenticateUsing(function (Request $request) {

            // 🔹 認証成功
            if (Auth::attempt(
                $request->only('email', 'password'),
                $request->boolean('remember')
            )) {
                return Auth::user();
            }

            // 🔹 情報不一致（評価指定文言）
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        });
    }
}
