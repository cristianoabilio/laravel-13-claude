<?php

namespace App\Enums;

enum AppointmentType: string
{
    case Clinic = 'clinic';
    case VideoCall = 'video_call';
    case AudioCall = 'audio_call';
    case Chat = 'chat';
    case HomeVisit = 'home_visit';

    public function label(): string
    {
        return match ($this) {
            self::Clinic => 'Clinic',
            self::VideoCall => 'Video Call',
            self::AudioCall => 'Audio Call',
            self::Chat => 'Chat',
            self::HomeVisit => 'Home Visit',
        };
    }

    public function requiresClinic(): bool
    {
        return $this === self::Clinic;
    }

    public function requiresHomeAddress(): bool
    {
        return $this === self::HomeVisit;
    }
}
