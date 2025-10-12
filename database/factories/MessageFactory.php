<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'chatroom_id' => ChatroomFactory::class,
            'username' => $this->faker->userName(),
            'display_name' => $this->faker->name(),
            'badges' => $this->faker->words(),
            'message' => $this->faker->sentence(),
            'platform' => $this->faker->word(),
            'timestamp' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
