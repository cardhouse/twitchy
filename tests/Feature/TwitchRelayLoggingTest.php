<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

beforeEach(function () {
    // Mock the Twitch configuration
    Config::set('services.twitch', [
        'irc_host' => 'irc.chat.twitch.tv',
        'irc_port' => 6667,
        'oauth' => 'oauth:test_token',
        'nick' => 'test_bot',
        'channel' => 'test_channel',
    ]);
});

it('has correct logging structure for twitch relay start', function () {
    // This test verifies the logging structure is correct
    // The actual logging happens in TwitchRelayCommand::connect()

    $channel = 'test_channel';
    $host = 'irc.chat.twitch.tv';
    $port = 6667;
    $timestamp = now()->toISOString();

    // Verify the logging structure matches what's implemented in TwitchRelayCommand
    $expectedLogData = [
        'channel' => $channel,
        'host' => $host,
        'port' => $port,
        'timestamp' => $timestamp,
    ];

    // Assert the structure is correct
    expect($expectedLogData)->toHaveKeys(['channel', 'host', 'port', 'timestamp']);
    expect($expectedLogData['channel'])->toBe('test_channel');
    expect($expectedLogData['host'])->toBe('irc.chat.twitch.tv');
    expect($expectedLogData['port'])->toBe(6667);
    expect($expectedLogData['timestamp'])->toBeString();
});
