<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => function (array $attributes) {
                return Conversation::find($attributes['conversation_id'])->doctor_id;
            },
            'body' => fake()->sentence(),
            'attachment_path' => null,
            'attachment_original_name' => null,
            'attachment_mime' => null,
            'read_at' => null,
        ];
    }
}
