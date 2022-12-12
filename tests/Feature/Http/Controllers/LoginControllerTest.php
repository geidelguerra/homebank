<?php

use App\Models\User;
use Database\Factories\UserFactory;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\assertAuthenticatedAs;
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
        'remember' => true
    ])->assertRedirect(route('home'));

    assertAuthenticatedAs($user, 'web');
});
