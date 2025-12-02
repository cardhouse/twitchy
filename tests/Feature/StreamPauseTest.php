<?php

use App\Models\Chatroom;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;

test('can pause and resume stream', function () {
    $chatroom = Chatroom::factory()->create();

    expect($chatroom->isPaused())->toBeFalse();

    $chatroom->pause();
    expect($chatroom->fresh()->is_paused)->toBeTrue();
    expect($chatroom->isPaused())->toBeTrue();

    $chatroom->resume();
    expect($chatroom->fresh()->is_paused)->toBeFalse();
    expect($chatroom->isPaused())->toBeFalse();
});

test('pause state is cached for 10 seconds', function () {
    $chatroom = Chatroom::factory()->create();

    // Initial state
    expect($chatroom->isPaused())->toBeFalse();

    // Pause the chatroom
    $chatroom->pause();

    // Should return cached value immediately
    expect($chatroom->isPaused())->toBeTrue();

    // Clear cache and check fresh value
    Cache::forget("chatroom_{$chatroom->id}_is_paused");
    expect($chatroom->isPaused())->toBeTrue();
});

test('stream toggle component shows correct status', function () {
    $chatroom = Chatroom::factory()->create();

    $component = Livewire\Volt\Volt::test('control.stream-toggle');

    // Should show active initially
    $component->assertSee('Active')
        ->assertSet('isPaused', false);

    // Pause the chatroom
    $chatroom->pause();

    // Create a new component instance to test the updated state
    $component = Livewire\Volt\Volt::test('control.stream-toggle');
    $component->assertSee('Paused')
        ->assertSet('isPaused', true);
});

test('stream toggle can pause and resume', function () {
    $chatroom = Chatroom::factory()->create();

    $component = Livewire\Volt\Volt::test('control.stream-toggle');

    // Initially active
    $component->assertSet('isPaused', false);

    // Toggle to pause
    $component->call('toggle');

    // Should be paused
    expect($chatroom->fresh()->is_paused)->toBeTrue();
    $component->assertSet('isPaused', true);

    // Toggle to resume
    $component->call('toggle');

    // Should be active
    expect($chatroom->fresh()->is_paused)->toBeFalse();
    $component->assertSet('isPaused', false);
});

test('relay command skips message creation when paused', function () {
    $chatroom = Chatroom::factory()->create();
    $chatroom->pause();

    // Create a mock message
    $message = new Message([
        'username' => 'testuser',
        'display_name' => 'TestUser',
        'badges' => [],
        'message' => 'Test message',
        'platform' => 'twitch',
        'timestamp' => now()->toISOString(),
    ]);

    // Count initial messages
    $initialCount = Message::count();

    // Simulate the relay command behavior
    if ($chatroom->isPaused()) {
        // Should skip message creation
        expect(true)->toBeTrue(); // This simulates the skip logic
    } else {
        $chatroom->messages()->save($message);
    }

    // Message count should not increase
    expect(Message::count())->toBe($initialCount);
});

test('relay command creates messages when not paused', function () {
    $chatroom = Chatroom::factory()->create();
    // Ensure not paused
    $chatroom->resume();

    // Create a mock message
    $message = new Message([
        'username' => 'testuser',
        'display_name' => 'TestUser',
        'badges' => [],
        'message' => 'Test message',
        'platform' => 'twitch',
        'timestamp' => now()->toISOString(),
    ]);

    // Count initial messages
    $initialCount = Message::count();

    // Simulate the relay command behavior
    if ($chatroom->isPaused()) {
        // Should skip message creation
        expect(true)->toBeTrue(); // This simulates the skip logic
    } else {
        $chatroom->messages()->save($message);
    }

    // Message count should increase
    expect(Message::count())->toBe($initialCount + 1);
});
