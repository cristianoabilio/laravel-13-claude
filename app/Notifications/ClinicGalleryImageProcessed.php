<?php

namespace App\Notifications;

use App\Models\ClinicImage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClinicGalleryImageProcessed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * Sent synchronously from within {@see \App\Jobs\ProcessClinicGalleryImage} once the
     * upload has been resized and stored - it's already running in the background, so
     * queuing the notification itself would just add another hop for no benefit.
     */
    public function __construct(public ClinicImage $clinicImage) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $clinic = $this->clinicImage->clinic;

        return (new MailMessage)
            ->subject('Gallery image processed for '.$clinic->name)
            ->greeting('Hi '.$notifiable->first_name.',')
            ->line('A new gallery image for "'.$clinic->name.'" has finished processing and is now visible on your clinic profile.')
            ->action('View Clinic', route('doctor.clinics'))
            ->line('Thanks for using '.config('app.name').'!');
    }
}
