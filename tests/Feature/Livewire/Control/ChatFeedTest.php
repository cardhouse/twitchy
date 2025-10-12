<?php

namespace Tests\Feature\Livewire\Control;

use Livewire\Volt\Volt;
use Tests\TestCase;

class ChatFeedTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $component = Volt::test('control.chat-feed');

        $component->assertSee('');
    }
}
