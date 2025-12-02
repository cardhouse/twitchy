<?php

use App\Events\MessageDemoted;
use App\Events\MessagePromoted;
use App\Livewire\Overlay\ToastDisplay;
use App\Models\Message;
use Livewire\Livewire;

it('demonstrates complete toast functionality', function () {
    // Create a chatroom first
    $chatroom = \App\Models\Chatroom::factory()->create();

    // Create a message
    $message = Message::factory()->create([
        'chatroom_id' => $chatroom->id,
        'display_name' => 'TestUser',
        'username' => 'testuser',
        'message' => 'Test message content',
    ]);

    // Promote the message to the PromotedMessageStore
    app(\App\Messages\Actions\PromoteMessage::class)->handle($message);

    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'local',
    ]);

    // Test that the component can handle promoted messages
    $component->assertSee('TestUser');
});

it('verifies broadcasting events work correctly', function () {
    // Create a chatroom first
    $chatroom = \App\Models\Chatroom::factory()->create();

    // Test that our events can be created and have the right structure
    $message = Message::factory()->create(['chatroom_id' => $chatroom->id]);

    $messagePromoted = new MessagePromoted($message);

    // Verify it implements the broadcast interface
    expect($messagePromoted)->toBeInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class);

    // Verify the broadcast channel
    $channels = $messagePromoted->broadcastOn();
    expect($channels[0]->name)->toBe('local');

    // Test MessageDemoted event
    $messageDemoted = new MessageDemoted;
    expect($messageDemoted)->toBeInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class);
});

it('confirms overlay route is working', function () {
    // Test that our overlay route returns expected responses
    $overlayKey = config('overlay.key', 'local');

    $this->get("/overlay/{$overlayKey}")
        ->assertOk()
        ->assertSee("data-overlay-key=\"{$overlayKey}\"", false);
});
