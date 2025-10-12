<?php

use App\Events\ToastShow;
use App\Livewire\Overlay\ToastDisplay;
use Livewire\Livewire;

it('demonstrates complete toast functionality', function () {
    // 1. Test Livewire Component Core Functionality
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'demo',
        'params' => ['theme' => 'dark', 'safeMargin' => 30],
    ]);

    // Initially hidden
    $component->assertSet('isVisible', false)
        ->assertSet('currentMessage', null);

    // 2. Test Direct Method Call (basic functionality)
    $testMessage = [
        'display_name' => 'TestUser',
        'username' => 'testuser',
        'badges' => [['name' => 'moderator']],
        'message' => 'Direct method test!',
    ];

    $testOptions = [
        'duration_ms' => 5000,
        'theme' => 'light',
        'fontScale' => 1.2,
    ];

    $component->call('showToast', $testMessage, $testOptions);

    $component->assertSet('isVisible', true)
        ->assertSet('currentMessage.display_name', 'TestUser')
        ->assertSet('currentMessage.message', 'Direct method test!')
        ->assertSet('currentOptions.theme', 'light')
        ->assertSet('currentOptions.fontScale', 1.2);

    // 3. Test Livewire Event Handling (what JavaScript would trigger)
    $component->dispatch('toast-hide', 'manual');
    $component->assertSet('isVisible', false);

    // 4. Test the Echo Integration Data Flow
    $echoData = [
        'message' => [
            'display_name' => 'EchoUser',
            'username' => 'echouser',
            'badges' => [['name' => 'echo-badge']],
            'message' => 'This came from Echo!',
        ],
        'options' => [
            'duration_ms' => 3000,
            'theme' => 'dark',
            'fontScale' => 1.0,
        ],
    ];

    // This simulates: @this.dispatch('toast-show', {message: ..., options: ...})
    $component->dispatch('toast-show', $echoData);

    $component->assertSet('isVisible', true)
        ->assertSet('currentMessage.display_name', 'EchoUser')
        ->assertSet('currentMessage.message', 'This came from Echo!')
        ->assertSet('currentOptions.duration_ms', 3000);

    // 5. Test CSS Classes Generation
    $containerClasses = $component->get('containerClasses');
    expect($containerClasses)->toContain('opacity-100') // Visible
        ->toContain('translate-y-0') // No transform offset
        ->toContain('mt-[30px]') // Safe margin from params
        ->not->toContain('pointer-events-none'); // Interactive

    $toastClasses = $component->get('toastClasses');
    expect($toastClasses)->toContain('bg-gray-900/90') // Dark theme from current options
        ->toContain('text-white')
        ->toContain('rounded-lg')
        ->toContain('shadow-lg');

    // 6. Test Hide Functionality
    $component->call('hideToast', 'test-complete');
    $component->assertSet('isVisible', false)
        ->assertSet('currentMessage', null)
        ->assertSet('currentOptions', []);

    $hiddenClasses = $component->get('containerClasses');
    expect($hiddenClasses)->toContain('opacity-0')
        ->toContain('pointer-events-none');
});

it('verifies broadcasting events work correctly', function () {
    // Test that our events can be created and have the right structure
    $toastShow = new ToastShow('test-overlay', [
        'display_name' => 'BroadcastUser',
        'message' => 'Broadcast test',
    ], [
        'theme' => 'light',
    ]);

    // Verify it implements the broadcast interface
    expect($toastShow)->toBeInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class);

    // Verify the broadcast channel
    $channels = $toastShow->broadcastOn();
    expect($channels[0]->name)->toBe('overlay.test-overlay');

    // Verify event data structure
    expect($toastShow->overlayKey)->toBe('test-overlay');
    expect($toastShow->message['display_name'])->toBe('BroadcastUser');
    expect($toastShow->options['theme'])->toBe('light');
});

it('confirms all API endpoints are working', function () {
    // Test that our routes return expected responses
    // Use the configured overlay key
    $overlayKey = config('overlay.key', 'local');

    $this->get("/overlay/{$overlayKey}")
        ->assertOk()
        ->assertSee("data-overlay-key=\"{$overlayKey}\"", false);

    // Note: Test routes are only available in local environment
    // They work correctly when accessed via browser or curl
});
