<?php

namespace App\Http\Controllers\Patient;

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

    public function index(?User $doctor = null): View|RedirectResponse
    {
        /** @var User $patient */
        $patient = Auth::user();
        $this->chat->touchPresence($patient);

        $contacts = $this->chat->contactsForPatient($patient);

        if ($doctor && ! $this->eligible($patient, $doctor)) {
            return redirect()->route('patient.messages')->with('error', 'You can only chat with doctors you have completed an appointment with.');
        }

        $conversation = null;
        $messages = collect();

        if ($doctor) {
            $doctor->is_online = $this->chat->isOnline($doctor);
            $conversation = $this->chat->conversationBetween($doctor, $patient);
            $this->chat->markRead($conversation, $patient);
            $messages = $this->chat->messagesFor($conversation)->map(fn ($message) => $this->chat->toPayload($message, $patient));
        }

        return view('patient.dashboard.messages.patient_messages', [
            'contacts' => $contacts,
            'activeContact' => $doctor,
            'messages' => $messages,
            'lastMessageId' => optional($messages->last())['id'] ?? 0,
        ]);
    }

    public function poll(Request $request, User $doctor): JsonResponse
    {
        /** @var User $patient */
        $patient = Auth::user();
        abort_unless($this->eligible($patient, $doctor), 403);

        $this->chat->touchPresence($patient);

        $conversation = $this->chat->conversationBetween($doctor, $patient);
        $afterId = $request->integer('after') ?: null;
        $messages = $this->chat->messagesFor($conversation, $afterId);
        $this->chat->markRead($conversation, $patient);

        return response()->json([
            'messages' => $messages->map(fn ($message) => $this->chat->toPayload($message, $patient))->values(),
            'partner_online' => $this->chat->isOnline($doctor),
        ]);
    }

    public function store(StoreChatMessageRequest $request, User $doctor): JsonResponse
    {
        /** @var User $patient */
        $patient = Auth::user();
        abort_unless($this->eligible($patient, $doctor), 403);

        $conversation = $this->chat->conversationBetween($doctor, $patient);
        $message = $this->chat->send($conversation, $patient, $request->validated('body'), $request->file('image'));
        $this->chat->touchPresence($patient);

        return response()->json([
            'message' => $this->chat->toPayload($message, $patient),
        ]);
    }

    protected function eligible(User $patient, User $doctor): bool
    {
        return $doctor->role === 'doctor' && $this->chat->canChat($doctor, $patient);
    }
}
