<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->from = env('TWILIO_WHATSAPP_FROM');

        if ($sid && $token) {
            // En entorno local (Windows), desactivamos la verificación SSL de cURL.
            if (app()->environment('local')) {
                $httpClient = new \Twilio\Http\CurlClient([
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]);
                $this->client = new Client($sid, $token, null, null, $httpClient);
            } else {
                $this->client = new Client($sid, $token);
            }
        }
    }

    /**
     * Envía un mensaje de WhatsApp.
     *
     * @param string $to Número de destino (ej. +521234567890)
     * @param string $message Mensaje a enviar
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!$this->client || !$this->from) {
            Log::warning('Twilio Credentials are not set properly.');
            return false;
        }

        // Limpiar espacios y asegurar formato para Twilio
        $to = str_replace(' ', '', $to);
        
        try {
            $this->client->messages->create(
                "whatsapp:" . $to,
                [
                    'from' => "whatsapp:" . $this->from,
                    'body' => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message: ' . $e->getMessage());
            return false;
        }
    }
}
