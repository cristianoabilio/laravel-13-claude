<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreChatMessageRequest;
use App\Models\User;
use App\Services\Chat\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function __construct(protected ChatService $chat) {}

    public function index(?User $patient = null): View|RedirectResponse
    {
        /** @var User $doctor */
        $doctor = Auth::user();
        $this->chat->touchPresence($doctor);

        $contacts = $this->chat->contactsForDoctor($doctor);

        if ($patient && ! $this->eligible($doctor, $patient)) {
            return redirect()->route('doctor.messages')->with('error', 'You can only chat with patients you have completed an appointment with.');
        }

        $conversation = null;
        $messages = collect();

        if ($patient) {
            $patient->is_online = $this->chat->isOnline($patient);
            $conversation = $this->chat->conversationBetween($doctor, $patient);
            $this->chat->markRead($conversation, $doctor);
            $messages = $this->chat->messagesFor($conversation)->map(fn ($message) => $this->chat->toPayload($message, $doctor));
        }

        return view('doctor.dashboard.messages.doctor_messages', [
            'contacts' => $contacts,
            'activeContact' => $patient,
            'messages' => $messages,
            'lastMessageId' => optional($messages->last())['id'] ?? 0,
        ]);
    }

    public function poll(Request $request, User $patient): JsonResponse
    {
        /** @var User $doctor */
        $doctor = Auth::user();
        abort_unless($this->eligible($doctor, $patient), 403);

        $this->chat->touchPresence($doctor);

        $conversation = $this->chat->conversationBetween($doctor, $patient);
        $afterId = $request->integer('after') ?: null;
        $messages = $this->chat->messagesFor($conversation, $afterId);
        $this->chat->markRead($conversation, $doctor);

        return response()->json([
            'messages' => $messages->map(fn ($message) => $this->chat->toPayload($message, $doctor))->values(),
            'partner_online' => $this->chat->isOnline($patient),
        ]);
    }

    public function store(StoreChatMessageRequest $request, User $patient): JsonResponse
    {
        /** @var User $doctor */
        $doctor = Auth::user();
        abort_unless($this->eligible($doctor, $patient), 403);

        $conversation = $this->chat->conversationBetween($doctor, $patient);
        $message = $this->chat->send($conversation, $doctor, $request->validated('body'), $request->file('image'));
        $this->chat->touchPresence($doctor);

        return response()->json([
            'message' => $this->chat->toPayload($message, $doctor),
        ]);
    }

    protected function eligible(User $doctor, User $patient): bool
    {
        return $patient->role === 'patient' && $this->chat->canChat($doctor, $patient);
    }
}
