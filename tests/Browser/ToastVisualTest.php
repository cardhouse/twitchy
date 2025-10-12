<?php

use App\Events\ToastShow;

it('takes a screenshot of the toast in action', function () {
    // Visit the overlay page
    $page = visit('/overlay/local')
        ->assertNoJavascriptErrors()
        ->pause(1000); // Give Echo time to connect

    // Take a screenshot of the empty overlay first
    $page->screenshot('overlay-empty');

    // Dispatch a toast event from the backend
    ToastShow::dispatch('local', [
        'display_name' => 'Screenshot User',
        'username' => 'screenshot',
        'badges' => [
            ['name' => 'moderator'],
            ['name' => 'vip'],
        ],
        'message' => 'This is a screenshot test! 📸 Look at this beautiful toast notification with badges and styling.',
    ], [
        'duration_ms' => 10000, // Long duration so we have time to see it
        'theme' => 'dark',
        'fontScale' => 1.2,
        'safeMargin' => 30,
    ]);

    // Wait for the toast to appear and take a screenshot
    $page->pause(2000) // Give time for the event to propagate and display
        ->screenshot('toast-displayed')
        ->assertSee('Screenshot User')
        ->assertSee('This is a screenshot test!')
        ->assertSee('moderator')
        ->assertSee('vip');

    // Test the light theme as well
    ToastShow::dispatch('local', [
        'display_name' => 'Light Theme User',
        'username' => 'lightuser',
        'badges' => [['name' => 'tester']],
        'message' => 'This is the light theme version! ☀️',
    ], [
        'duration_ms' => 10000,
        'theme' => 'light',
        'fontScale' => 1.0,
        'safeMargin' => 24,
    ]);

    $page->pause(2000)
        ->screenshot('toast-light-theme')
        ->assertSee('Light Theme User')
        ->assertSee('This is the light theme version!');

    // Test different font scale
    ToastShow::dispatch('local', [
        'display_name' => 'Large Font User',
        'username' => 'bigfont',
        'message' => 'BIG TEXT! This shows the font scaling feature.',
    ], [
        'duration_ms' => 10000,
        'theme' => 'dark',
        'fontScale' => 1.5,
        'safeMargin' => 40,
    ]);

    $page->pause(2000)
        ->screenshot('toast-large-font')
        ->assertSee('Large Font User')
        ->assertSee('BIG TEXT!');
});

it('takes a screenshot of the test page in action', function () {
    // Visit the test page
    $page = visit('/test/livewire-toast')
        ->assertSee('Livewire Toast Test')
        ->assertNoJavascriptErrors();

    // Take a screenshot of the test page
    $page->screenshot('test-page-initial');

    // Click the show toast button
    $page->click('Show Toast (Direct Livewire)')
        ->pause(1000) // Give Livewire time to process
        ->screenshot('test-page-with-toast')
        ->assertSee('TestUser')
        ->assertSee('This is a direct Livewire test toast!');

    // Click hide button
    $page->click('Hide Toast (Direct Livewire)')
        ->pause(500)
        ->screenshot('test-page-toast-hidden')
        ->assertDontSee('TestUser');
});

it('demonstrates toast animations and transitions', function () {
    $page = visit('/overlay/local')
        ->pause(1000);

    // Show toast and capture the transition
    ToastShow::dispatch('local', [
        'display_name' => 'Animation Demo',
        'username' => 'animator',
        'badges' => [['name' => 'demo']],
        'message' => 'Watch this toast animate in! 🎬',
    ], [
        'duration_ms' => 8000,
        'theme' => 'dark',
        'fontScale' => 1.1,
    ]);

    // Take screenshots at different stages
    $page->pause(500)
        ->screenshot('toast-appearing')
        ->pause(1000)
        ->screenshot('toast-fully-visible');

    // Show a different toast to see the replacement
    ToastShow::dispatch('local', [
        'display_name' => 'Replacement Toast',
        'username' => 'replacer',
        'message' => 'This toast replaces the previous one!',
    ], [
        'duration_ms' => 5000,
        'theme' => 'light',
        'fontScale' => 1.0,
    ]);

    $page->pause(1000)
        ->screenshot('toast-replaced')
        ->assertSee('Replacement Toast')
        ->assertDontSee('Animation Demo');
});


