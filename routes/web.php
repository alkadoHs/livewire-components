<?php

use App\Actions\Auth\Logout;
use App\Http\Resources\UserResource;
use App\Livewire;
use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Dashboard;
use App\Livewire\EditTask;
use App\Livewire\Settings\Account;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', Livewire\Home::class)->name('home');

/** AUTH ROUTES */
Route::get('/register', Register::class)->name('register');

Route::get('/login', Login::class)->name('login');

Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');

Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/settings/account', Account::class)->name('settings.account');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/auth/verify-email', VerifyEmail::class)
        ->name('verification.notice');
    Route::post('/logout', Logout::class)
        ->name('app.auth.logout');
    Route::get('confirm-password', ConfirmPassword::class)
        ->name('password.confirm');
});

Route::middleware(['auth', 'signed'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect(route('home'));
    })->name('verification.verify');
});

Route::get('/users', function (Request $request) {
    $searchTerm = $request->get('search', '');
    $query = User::query();

    if (!empty($searchTerm)) {
        // Use whereAny for cleaner searching across multiple columns in Laravel 10+
        $query->whereAny(['name', 'email'], 'LIKE', "%{$searchTerm}%");
    }
    
    // Change take(15) to paginate(20)
    $users = $query->paginate(10);

    // It's best practice to use a Resource to format paginated data
    return $users;
});

Route::get('/users/{user}', function (User $user) {
    // Using route model binding for convenience.
    // Laravel will automatically find the user or return a 404 error.
    
    // We must return the data in the exact same format as the search route
    return new UserResource($user);
});

Route::get('/users/edit/{user}', EditTask::class)->name('users.edit');

Route::get('/guys', fn () => User::paginate(10));