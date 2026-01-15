@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <h2 class="mb-4 text-center">ログイン</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- メールアドレス --}}
            <div class="mb-3">
                <label class="form-label">メールアドレス</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                >

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- パスワード --}}
            <div class="mb-3">
                <label class="form-label">パスワード</label>
                <input
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                >

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">
                ログイン
            </button>
        </form>
    </div>
</div>
@endsection
