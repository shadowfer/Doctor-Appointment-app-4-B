<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $idInstance;
    protected $apiToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->idInstance = env('GREEN_API_ID_INSTANCE');
        $this->apiToken = env('GREEN_API_TOKEN_INSTANCE');
        $this->apiUrl = rtrim(env('GREEN_API_URL', 'https://api.green-api.com'), '/');
    }

    /**
     * Envía un mensaje de WhatsApp usando Green API.
     *
     * @param string $to Número de destino (ej. 521234567890)
     * @param string $message Mensaje a enviar
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!$this->idInstance || !$this->apiToken) {
            Log::warning('Green API Credentials are not set.');
            return false;
        }

        // Limpiar para Green API (solo números)
        $to = preg_replace('/[^0-9]/', '', $to);
        // Green API requiere el formato de número seguido de @c.us
        $chatId = $to . '@c.us';
        
        try {
            $url = "{$this->apiUrl}/waInstance{$this->idInstance}/sendMessage/{$this->apiToken}";
            
            $http = Http::asJson();
            // En entorno local (Windows), desactivamos la verificación SSL de cURL
            if (app()->environment('local')) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($url, [
                'chatId' => $chatId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                 return true;
            }
            
            Log::error('Green API Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message via Green API: ' . $e->getMessage());
            return false;
        }
    }
}
