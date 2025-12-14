<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\DefaultCategoriesCreator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct(public DefaultCategoriesCreator $defaultCategoriesCreator) {}

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('user');
        $this->defaultCategoriesCreator->createFor($user);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Anda telah masuk.');
    }
}
