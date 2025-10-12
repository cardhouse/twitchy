<?php

use App\Events\ToastHide;
use App\Events\ToastShow;

it('displays toast on overlay page when triggered via javascript', function () {
    visit('/test/livewire-toast')
        ->assertSee('Livewire Toast Test')
        ->assertNoJavascriptErrors()
        ->click('Show Toast (Direct Livewire)')
        ->pause(500) // Give Livewire time to process
        ->assertSee('TestUser')
        ->assertSee('This is a direct Livewire test toast!')
        ->assertSee('tester');
});

it('hides toast when hide button is clicked', function () {
    visit('/test/livewire-toast')
        ->assertSee('Livewire Toast Test')
        ->click('Show Toast (Direct Livewire)')
        ->pause(500)
        ->assertSee('TestUser')
        ->click('Hide Toast (Direct Livewire)')
        ->pause(500)
        ->assertDontSee('TestUser')
        ->assertDontSee('This is a direct Livewire test toast!');
});

it('displays toast on overlay page via echo events', function () {
    // Start the browser test
    $page = visit('/overlay/local');

    // Verify overlay page loads
    $page->assertNoJavascriptErrors()
        ->pause(1000); // Give Echo time to connect

    // Dispatch the event from the backend
    ToastShow::dispatch('local', [
        'display_name' => 'EchoTestUser',
        'username' => 'echotest',
        'badges' => [['name' => 'echo-tester']],
        'message' => 'This is an Echo broadcast test!',
    ], [
        'duration_ms' => 8000,
        'theme' => 'dark',
        'fontScale' => 1.0,
    ]);

    // Give time for the event to propagate through Reverb and be displayed
    $page->pause(2000)
        ->assertSee('EchoTestUser')
        ->assertSee('This is an Echo broadcast test!')
        ->assertSee('echo-tester');
});

it('hides toast on overlay page via echo events', function () {
    $page = visit('/overlay/local');

    // First show a toast
    ToastShow::dispatch('local', [
        'display_name' => 'HideTestUser',
        'username' => 'hidetest',
        'message' => 'This toast will be hidden!',
    ], [
        'duration_ms' => 10000, // Long duration so it doesn't auto-hide
        'theme' => 'dark',
    ]);

    $page->pause(2000)
        ->assertSee('HideTestUser')
        ->assertSee('This toast will be hidden!');

    // Now hide it
    ToastHide::dispatch('local', 'manual');

    $page->pause(1000)
        ->assertDontSee('HideTestUser')
        ->assertDontSee('This toast will be hidden!');
});

it('displays multiple toasts in sequence', function () {
    $page = visit('/overlay/local');

    // First toast
    ToastShow::dispatch('local', [
        'display_name' => 'FirstUser',
        'message' => 'First message!',
    ], ['duration_ms' => 2000]);

    $page->pause(1000)
        ->assertSee('FirstUser')
        ->assertSee('First message!');

    // Second toast (should replace the first)
    ToastShow::dispatch('local', [
        'display_name' => 'SecondUser',
        'message' => 'Second message!',
    ], ['duration_ms' => 5000]);

    $page->pause(1000)
        ->assertSee('SecondUser')
        ->assertSee('Second message!')
        ->assertDontSee('FirstUser')
        ->assertDontSee('First message!');
});

it('auto-dismisses toast after specified duration', function () {
    $page = visit('/overlay/local');

    // Show toast with short duration
    ToastShow::dispatch('local', [
        'display_name' => 'AutoDismissUser',
        'message' => 'This will auto-dismiss!',
    ], [
        'duration_ms' => 2000, // 2 seconds
    ]);

    $page->pause(1000)
        ->assertSee('AutoDismissUser')
        ->assertSee('This will auto-dismiss!')
        ->pause(2500) // Wait for auto-dismiss
        ->assertDontSee('AutoDismissUser')
        ->assertDontSee('This will auto-dismiss!');
});

it('handles different themes correctly', function () {
    $page = visit('/overlay/local');

    // Test dark theme
    ToastShow::dispatch('local', [
        'display_name' => 'DarkThemeUser',
        'message' => 'Dark theme test!',
    ], [
        'theme' => 'dark',
        'duration_ms' => 3000,
    ]);

    $page->pause(1000)
        ->assertSee('DarkThemeUser')
        ->assertElementHasClass('[data-overlay-key="local"] > div > div', 'bg-gray-900/90');

    // Test light theme
    ToastShow::dispatch('local', [
        'display_name' => 'LightThemeUser',
        'message' => 'Light theme test!',
    ], [
        'theme' => 'light',
        'duration_ms' => 3000,
    ]);

    $page->pause(1000)
        ->assertSee('LightThemeUser')
        ->assertElementHasClass('[data-overlay-key="local"] > div > div', 'bg-white/90');
});

it('verifies echo connection and console logs', function () {
    $page = visit('/overlay/local');

    $page->pause(2000) // Give Echo time to connect
        ->assertNoJavascriptErrors();

    // Check that Echo setup logs are present
    $logs = $page->driver->manage()->getLog('browser');

    $echoSetupFound = false;
    foreach ($logs as $log) {
        if (str_contains($log['message'], 'Setting up Echo listeners for overlay: local')) {
            $echoSetupFound = true;
            break;
        }
    }

    expect($echoSetupFound)->toBeTrue('Echo setup should be logged');
});

it('works on different overlay keys', function () {
    // Test with a different overlay key
    $page = visit('/overlay/test-key');

    ToastShow::dispatch('test-key', [
        'display_name' => 'TestKeyUser',
        'message' => 'Different overlay key test!',
    ], [
        'theme' => 'dark',
        'duration_ms' => 3000,
    ]);

    $page->pause(2000)
        ->assertSee('TestKeyUser')
        ->assertSee('Different overlay key test!');
});
