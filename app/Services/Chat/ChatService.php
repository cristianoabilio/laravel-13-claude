<?php

namespace App\Services\Chat;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\User;
use App\Services\Doctor\DoctorPatientService;
use App\Services\Patient\PatientDoctorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ChatService
{
    /**
     * A patient counts as "recently online" if their last poll/page-load/send
     * happened within this many seconds - a little over one poll interval so
     * a momentary lag between polls doesn't flicker the status.
     */
    protected const ONLINE_THRESHOLD_SECONDS = 45;

    public function __construct(
        protected DoctorPatientService $doctorPatients,
        protected PatientDoctorService $patientDoctors,
    ) {}

    /**
     * Whether this doctor/patient pair has at least one completed
     * appointment together - the gate for chatting at all.
     */
    public function canChat(User $doctor, User $patient): bool
    {
        return $this->doctorPatients->isTreatingPatient($doctor, $patient);
    }

    public function conversationBetween(User $doctor, User $patient): Conversation
    {
        return Conversation::firstOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
        ]);
    }

    /**
     * Every patient this doctor can chat with, annotated with their
     * conversation summary (last message, unread count, online status).
     *
     * @return Collection<int, User>
     */
    public function contactsForDoctor(User $doctor): Collection
    {
        return $this->annotateContacts(
            $this->doctorPatients->forDoctor($doctor),
            Conversation::where('doctor_id', $doctor->id),
            $doctor,
            'patient_id',
        );
    }

    /**
     * Every doctor this patient can chat with, annotated with their
     * conversation summary (last message, unread count, online status).
     *
     * @return Collection<int, User>
     */
    public function contactsForPatient(User $patient): Collection
    {
        return $this->annotateContacts(
            $this->patientDoctors->forPatient($patient),
            Conversation::where('patient_id', $patient->id),
            $patient,
            'doctor_id',
        );
    }

    /**
     * @param  Collection<int, User>  $contacts
     * @return Collection<int, User>
     */
    protected function annotateContacts(Collection $contacts, Builder $conversationQuery, User $viewer, string $partnerKey): Collection
    {
        $conversations = $conversationQuery
            ->with('latestMessage')
            ->withCount(['messages as unread_count' => function (Builder $query) use ($viewer) {
                $query->where('sender_id', '!=', $viewer->id)->whereNull('read_at');
            }])
            ->get()
            ->keyBy($partnerKey);

        return $contacts
            ->map(function (User $contact) use ($conversations) {
                $conversation = $conversations->get($contact->id);

                $contact->conversation_id = $conversation?->id;
                $contact->last_message = $conversation?->latestMessage;
                $contact->last_activity_at = $conversation?->last_message_at ?? $contact->last_booking_date;
                $contact->unread_count = $conversation?->unread_count ?? 0;
                $contact->is_online = $this->isOnline($contact);

                return $contact;
            })
            ->sortByDesc('last_activity_at')
            ->values();
    }

    /**
     * A doctor's online status is their own manual "Availability" toggle;
     * a patient (who has no such toggle) is considered online if they've
     * been active in the chat - polling, loading the page, or sending a
     * message - within the last few seconds.
     */
    public function isOnline(User $user): bool
    {
        if ($user->role === 'doctor') {
            return $user->availability_status === 'available';
        }

        return $user->last_seen_at !== null
            && $user->last_seen_at->greaterThanOrEqualTo(now()->subSeconds(self::ONLINE_THRESHOLD_SECONDS));
    }

    public function touchPresence(User $user): void
    {
        $user->forceFill(['last_seen_at' => now()])->saveQuietly();
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function messagesFor(Conversation $conversation, ?int $afterId = null): Collection
    {
        return $conversation->messages()
            ->when($afterId, fn (Builder $query) => $query->where('id', '>', $afterId))
            ->orderBy('id')
            ->get();
    }

    public function send(Conversation $conversation, User $sender, ?string $body, ?UploadedFile $image): ChatMessage
    {
        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $body,
        ];

        if ($image) {
            $data['attachment_path'] = $image->store('chat-attachments', 's3');
            $data['attachment_original_name'] = $image->getClientOriginalName();
            $data['attachment_mime'] = $image->getClientMimeType();
        }

        $message = ChatMessage::create($data);

        $conversation->update(['last_message_at' => $message->created_at]);

        return $message;
    }

    public function markRead(Conversation $conversation, User $reader): void
    {
        $conversation->messages()
            ->where('sender_id', '!=', $reader->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(ChatMessage $message, User $viewer): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'is_mine' => $message->sender_id === $viewer->id,
            'attachment_url' => $message->attachment_url,
            'attachment_original_name' => $message->attachment_original_name,
            'is_image' => $message->is_image_attachment,
            'time' => $message->created_at->format('g:i A'),
        ];
    }
}
