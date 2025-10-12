<?php

use App\Livewire\Overlay\ToastDisplay;
use Livewire\Livewire;

it('can mount the toast display component', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => [
            'theme' => 'dark',
            'fontScale' => 1.0,
            'safeMargin' => 24,
        ],
    ]);

    $component->assertSet('overlayKey', 'test')
        ->assertSet('isVisible', false)
        ->assertSet('currentMessage', null);
});

it('can show a toast via event', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => ['theme' => 'dark'],
    ]);

    $testMessage = [
        'display_name' => 'TestUser',
        'username' => 'testuser',
        'badges' => [['name' => 'tester']],
        'message' => 'This is a test toast!',
    ];

    $testOptions = [
        'duration_ms' => 5000,
        'theme' => 'dark',
        'fontScale' => 1.0,
    ];

    // Trigger the toast-show event
    $component->dispatch('toast-show', [
        'message' => $testMessage,
        'options' => $testOptions,
    ]);

    // Assert the component state changed
    $component->assertSet('isVisible', true)
        ->assertSet('currentMessage', $testMessage)
        ->assertSet('currentOptions', $testOptions);
});

it('can hide a toast via event', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => ['theme' => 'dark'],
    ]);

    // First show a toast
    $testMessage = [
        'display_name' => 'TestUser',
        'message' => 'Test message',
    ];

    $component->dispatch('toast-show', [
        'message' => $testMessage,
        'options' => [],
    ]);

    $component->assertSet('isVisible', true);

    // Then hide it
    $component->dispatch('toast-hide', 'manual');

    $component->assertSet('isVisible', false)
        ->assertSet('currentMessage', null);
});

it('can call showToast method directly', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => ['theme' => 'dark'],
    ]);

    $testMessage = [
        'display_name' => 'TestUser',
        'username' => 'testuser',
        'message' => 'Direct method call test',
    ];

    $testOptions = [
        'theme' => 'light',
        'fontScale' => 1.2,
    ];

    // Call the method directly
    $component->call('showToast', $testMessage, $testOptions);

    $component->assertSet('isVisible', true)
        ->assertSet('currentMessage', $testMessage)
        ->assertSet('currentOptions', $testOptions);
});

it('generates correct container classes when visible', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => ['safeMargin' => 30],
    ]);

    $component->call('showToast', ['message' => 'test'], ['safeMargin' => 50]);

    $containerClasses = $component->get('containerClasses');

    expect($containerClasses)->toContain('opacity-100')
        ->toContain('translate-y-0')
        ->toContain('mt-[50px]')
        ->not->toContain('pointer-events-none');
});

it('generates correct container classes when hidden', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => ['safeMargin' => 30],
    ]);

    // Component should start hidden
    $containerClasses = $component->get('containerClasses');

    expect($containerClasses)->toContain('opacity-0')
        ->toContain('-translate-y-4')
        ->toContain('pointer-events-none')
        ->toContain('mt-[30px]'); // Uses default from params
});

it('handles invalid message types gracefully', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => [],
    ]);

    // Try to dispatch with invalid message type
    $component->dispatch('toast-show', [
        'message' => 'invalid string instead of array',
        'options' => [],
    ]);

    // Should remain hidden since message validation failed
    $component->assertSet('isVisible', false)
        ->assertSet('currentMessage', null);
});

it('renders correctly when no message is present', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => ['theme' => 'dark'],
    ]);

    $component->assertSee('data-overlay-key="test"', false)
        ->assertDontSee('TestUser'); // No message content should be present
});

it('renders correctly when message is present', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'test',
        'params' => ['theme' => 'dark'],
    ]);

    $testMessage = [
        'display_name' => 'TestUser',
        'username' => 'testuser',
        'badges' => [['name' => 'moderator']],
        'message' => 'Hello world!',
    ];

    $component->call('showToast', $testMessage, []);

    $component->assertSee('TestUser')
        ->assertSee('Hello world!')
        ->assertSee('moderator');
});
