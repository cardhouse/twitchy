<?php

declare(strict_types=1);

use App\Events\MessageDemoted;
use App\Events\MessagePromoted;
use App\Models\Chatroom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    Log::spy();
});

it('logs when a message is promoted', function () {
    $user = User::factory()->create();
    $chatroom = Chatroom::factory()->create();
    $message = Message::factory()->create([
        'chatroom_id' => $chatroom->id,
        'username' => 'testuser',
        'display_name' => 'TestUser',
        'message' => 'This is a test message',
        'platform' => 'twitch',
    ]);

    // Dispatch the event
    MessagePromoted::dispatch($message);

    // Assert the log was written (allow for multiple calls due to broadcasting)
    Log::shouldHaveReceived('info')
        ->with('Message promoted', [
            'message_id' => $message->id,
            'username' => 'testuser',
            'display_name' => 'TestUser',
            'message_excerpt' => 'This is a test message',
            'platform' => 'twitch',
            'timestamp' => $message->timestamp,
        ]);
});

it('logs when a message is demoted', function () {
    $messageId = 123;

    // Dispatch the event
    MessageDemoted::dispatch($messageId);

    // Assert the log was written
    Log::shouldHaveReceived('info')
        ->with('Message demoted', [
            'message_id' => $messageId,
        ]);
});

it('logs message promotion through the action', function () {
    $user = User::factory()->create();
    $chatroom = Chatroom::factory()->create();
    $message = Message::factory()->create([
        'chatroom_id' => $chatroom->id,
        'username' => 'testuser',
        'display_name' => 'TestUser',
        'message' => 'This is a test message',
        'platform' => 'twitch',
    ]);

    // Use the PromoteMessage action
    app(\App\Messages\Actions\PromoteMessage::class)->handle($message);

    // Assert the log was written
    Log::shouldHaveReceived('info')
        ->with('Message promoted', [
            'message_id' => $message->id,
            'username' => 'testuser',
            'display_name' => 'TestUser',
            'message_excerpt' => 'This is a test message',
            'platform' => 'twitch',
            'timestamp' => $message->timestamp,
        ]);
});

it('logs message demotion through the action', function () {
    $messageId = 123;

    // Use the DemoteMessage action
    app(\App\Messages\Actions\DemoteMessage::class)->handle($messageId);

    // Assert the log was written
    Log::shouldHaveReceived('info')
        ->with('Message demoted', [
            'message_id' => $messageId,
        ]);
});

it('truncates long messages in promotion logs', function () {
    $user = User::factory()->create();
    $chatroom = Chatroom::factory()->create();
    $longMessage = str_repeat('This is a very long message that should be truncated. ', 10);
    $message = Message::factory()->create([
        'chatroom_id' => $chatroom->id,
        'username' => 'testuser',
        'display_name' => 'TestUser',
        'message' => $longMessage,
        'platform' => 'twitch',
    ]);

    // Dispatch the event
    MessagePromoted::dispatch($message);

    // Assert the log was written with truncated message
    Log::shouldHaveReceived('info')
        ->with('Message promoted', [
            'message_id' => $message->id,
            'username' => 'testuser',
            'display_name' => 'TestUser',
            'message_excerpt' => \Str::limit($longMessage, 50),
            'platform' => 'twitch',
            'timestamp' => $message->timestamp,
        ]);
});
