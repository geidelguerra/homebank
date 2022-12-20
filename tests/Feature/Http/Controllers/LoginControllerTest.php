<?php

use App\Models\User;
use Database\Factories\UserFactory;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('show login page', function () {
    get(route('login.show'))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('auth/Login');
    });
});

test('login user', function () {
    /** @var User */
    $user = UserFactory::new()->createOne();

    post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ])->assertRedirect(route('home'));

    assertAuthenticatedAs($user, 'web');
});

test('return validation error for invalid credentials', function () {
    /** @var User */
    $user = UserFactory::new()->createOne();

    post(route('login'), [
        'email' => 'wrongemail@email.e',
        'password' => 'password',
        'remember' => true,
    ])->assertInvalid([
        'email' => 'These credentials do not match our records.',
    ]);

    post(route('login'), [
        'email' => $user->email,
        'password' => 'wrongpassword',
        'remember' => true,
    ])->assertInvalid([
        'email' => 'These credentials do not match our records.',
    ]);

    assertGuest('web');
});
