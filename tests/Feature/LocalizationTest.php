<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_login_message_is_in_spanish()
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'incorrecta'])
            ->assertSessionHasErrors(['email' => 'El email o la contraseña no son correctos.']);
    }

    public function test_validation_messages_are_in_spanish()
    {
        $this->post('/register', ['name' => '', 'email' => 'no-es-email', 'password' => 'x', 'password_confirmation' => 'y'])
            ->assertSessionHasErrors([
                'name' => 'Completá el campo nombre.',
                'email' => 'Ingresá un email válido.',
                'password' => 'Las contraseñas no coinciden.',
            ]);
    }

    public function test_password_reset_status_is_in_spanish()
    {
        $this->post('/forgot-password', ['email' => 'nadie@example.com'])
            ->assertSessionHas('status', 'Si el email está registrado, te va a llegar un link para crear una contraseña nueva.');
    }
}
