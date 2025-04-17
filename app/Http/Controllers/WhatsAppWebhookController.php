<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Message;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode = $request->get('hub_mode');
        $token = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($mode === 'subscribe' && $token === env('WHATSAPP_VERIFY_TOKEN')) {
            return response($challenge, 200);
        }

        return response('Unauthorized', 403);
    }

    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info('Incoming WhatsApp Message:', $data);

        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $from = $message['from'] ?? null;
            $text = $message['text']['body'] ?? null;
            Message::create([
                'user_number' => $from,
                'message' => $text,
                'direction' => 'incoming',
            ]);
            // if ($from && $text) {
            //     // Log::info("Message from $from: $text");
            //     // Here you can process the message, store it, or respond
            // }
        }

        return response()->json(['status' => 'received'], 200);
    }
}
