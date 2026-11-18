<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:make-admin {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error("No existe un usuario con el email {$email}. Registrate primero en la web.");

        return 1;
    }

    $user->is_admin = true;
    $user->save();

    $this->info("{$user->name} ahora es admin.");

    return 0;
})->purpose('Give admin access to an existing user');
