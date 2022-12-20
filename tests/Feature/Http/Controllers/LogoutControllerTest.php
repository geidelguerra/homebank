<?php

use App\Models\User;
use Database\Factories\UserFactory;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;

test('logout user', function () {
    /** @var User */
    $user = UserFactory::new()->createOne();

    actingAs($user, 'web')
        ->post(route('logout'))
        ->assertRedirect(route('login.show'));

    assertGuest('web');
});
