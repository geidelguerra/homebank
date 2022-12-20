<?php

use Database\Factories\UserFactory;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('fail to show home page for guest user', function () {
    get(route('home'))->assertRedirect(route('login.show'));
});

test('show home page', function () {
    actingAs(UserFactory::new()->createOne())
        ->get(route('home'))
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('Home');
        });
});
