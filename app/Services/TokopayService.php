<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokopayService
{
    protected $baseUrl;
    protected $merchantId;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tokopay.base_url', 'https://api.tokopay.id');
        $this->merchantId = config('services.tokopay.merchant_id');
        $this->secretKey = config('services.tokopay.secret_key');
    }

    public function createOrder(array $data)
    {
        $payload = [
            'merchant_id' => $this->merchantId,
            'reff_id' => $data['ref_id'],
            'amount' => $data['amount'],
            'kode_channel' => $data['channel'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? '',
            'redirect_url' => $data['redirect_url'],
            'expired_ts' => $data['expired_ts'],
            'signature' => $this->generateSignature($data['ref_id']),
            'items' => $data['items'],
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/v1/order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        Log::info("Response: " . $response);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new \Exception('Failed to create payment order. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from Tokopay: ' . $response);
        }

        if ($result['status'] !== 'success') {
            throw new \Exception('Payment gateway error: ' . ($result['message'] ?? 'Unknown error'));
        }

        return $result;
    }

    public function checkOrderStatus(string $refId)
    {
        $data = [
            'merchant_id' => $this->merchantId,
            'ref_id' => $refId,
        ];

        $data['signature'] = $this->generateSignature($refId);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/v1/order/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new \Exception('Failed to check order status. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from Tokopay: ' . $response);
        }

        return $result;
    }

    public function verifyWebhook($request)
    {
        $signature = $request->header('X-Tokopay-Signature');
        $payload = $request->getContent();

        $expectedSignature = hash_hmac('sha256', $payload, $this->secretKey);

        return hash_equals($expectedSignature, $signature);
    }

    protected function generateSignature($reffId)
    {
        // Tokopay signature format: md5(MERCHANT_ID:SECRET:REFF_ID)
        $string = $this->merchantId . ':' . $this->secretKey . ':' . $reffId;
        Log::info("Signature: " . md5($string));
        return md5($string);
    }
}
