<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PcareActivityLog;

class PCareService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $appCode;
    private string $userKey;

    public function __construct()
    {
        $this->baseUrl = config('bpjs.pcare.base_url');
        $this->username = config('bpjs.pcare.username');
        $this->password = config('bpjs.pcare.password');
        $this->appCode = config('bpjs.pcare.app_code');
        $this->userKey = config('bpjs.pcare.user_key');
    }

    /**
     * Generate authentication headers for PCare API
     */
    private function generateHeaders(): array
    {
        $timestamp = time();
        $auth = base64_encode($this->username . ':' . $this->password . ':' . $this->appCode);

        // Generate signature menggunakan HMAC SHA256
        $signature = hash_hmac('sha256', $this->username . '&' . $timestamp, $this->userKey, false);

        return [
            'X-cons-id' => $this->username,
            'X-timestamp' => (string)$timestamp,
            'X-signature' => $signature,
            'X-authorization' => 'Basic ' . $auth,
            'user_key' => $this->userKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Make HTTP request to PCare API
     */
    private function makeRequest(string $method, string $endpoint, array $data = null, array $logInfo = [])
    {
        try {
            $headers = $this->generateHeaders();
            $url = $this->baseUrl . $endpoint;

            Log::info('PCare API Request', [
                'method' => $method,
                'url' => $url,
                'data' => $data,
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(config('bpjs.settings.timeout', 30));

            $result = match(strtoupper($method)) {
                'GET' => $response->get($url),
                'POST' => $response->post($url, $data),
                'PUT' => $response->put($url, $data),
                'DELETE' => $response->delete($url, $data),
                default => throw new \Exception('Invalid HTTP method')
            };

            $responseData = $result->json();
            $statusCode = $result->status();

            // Log activity
            $this->logActivity(
                activityType: $logInfo['activity_type'] ?? 'unknown',
                action: strtolower($method),
                endpoint: $endpoint,
                status: $result->successful() ? 'success' : 'failed',
                httpCode: $statusCode,
                requestData: $data,
                responseData: $responseData,
                noRawat: $logInfo['no_rawat'] ?? null,
                noKunjungan: $logInfo['no_kunjungan'] ?? null,
                errorMessage: $result->successful() ? null : ($responseData['message'] ?? 'Unknown error')
            );

            if (!$result->successful()) {
                throw new \Exception($responseData['message'] ?? 'API request failed');
            }

            return [
                'success' => true,
                'data' => $responseData,
                'code' => $statusCode
            ];

        } catch (\Exception $e) {
            Log::error('PCare API Error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    /**
     * Log PCare activity
     */
    private function logActivity(
        string $activityType,
        string $action,
        string $endpoint,
        string $status,
        ?int $httpCode = null,
        ?array $requestData = null,
        ?array $responseData = null,
        ?string $noRawat = null,
        ?string $noKunjungan = null,
        ?string $errorMessage = null
    ): void {
        if (!config('bpjs.settings.log_enabled', true)) {
            return;
        }

        PcareActivityLog::create([
            'no_rawat' => $noRawat,
            'no_kunjungan' => $noKunjungan,
            'activity_type' => $activityType,
            'action' => $action,
            'endpoint' => $endpoint,
            'status' => $status,
            'http_code' => $httpCode,
            'request_data' => $requestData ? json_encode($requestData) : null,
            'response_data' => $responseData ? json_encode($responseData) : null,
            'error_message' => $errorMessage,
            'user' => auth()->user()->username ?? 'system',
        ]);
    }

    /**
     * ====================================================================
     * PESERTA - Get data peserta by No Kartu
     * ====================================================================
     */
    public function getPeserta(string $noKartu)
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.peserta') . $noKartu,
            logInfo: ['activity_type' => 'get_peserta']
        );
    }

    /**
     * ====================================================================
     * PENDAFTARAN - Daftar pasien ke PCare
     * ====================================================================
     */
    public function insertPendaftaran(array $data, string $noRawat)
    {
        return $this->makeRequest(
            'POST',
            config('bpjs.pcare.endpoints.pendaftaran.insert'),
            $data,
            [
                'activity_type' => 'insert_pendaftaran',
                'no_rawat' => $noRawat
            ]
        );
    }

    public function getPendaftaran(string $tglDaftar, int $start = 0, int $limit = 10)
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.pendaftaran.list') . "/{$tglDaftar}/{$start}/{$limit}",
            logInfo: ['activity_type' => 'get_pendaftaran']
        );
    }

    public function deletePendaftaran(string $noUrut, string $noKartu, string $tglDaftar, string $kdProvider, string $noRawat)
    {
        return $this->makeRequest(
            'DELETE',
            config('bpjs.pcare.endpoints.pendaftaran.delete') . "{$noUrut}/{$noKartu}/{$tglDaftar}/{$kdProvider}",
            logInfo: [
                'activity_type' => 'delete_pendaftaran',
                'no_rawat' => $noRawat
            ]
        );
    }

    /**
     * ====================================================================
     * KUNJUNGAN - Insert dan update kunjungan
     * ====================================================================
     */
    public function insertKunjungan(array $data, string $noRawat, string $noKunjungan)
    {
        return $this->makeRequest(
            'POST',
            config('bpjs.pcare.endpoints.kunjungan.insert'),
            $data,
            [
                'activity_type' => 'insert_kunjungan',
                'no_rawat' => $noRawat,
                'no_kunjungan' => $noKunjungan
            ]
        );
    }

    public function updateKunjungan(array $data, string $noRawat, string $noKunjungan)
    {
        return $this->makeRequest(
            'PUT',
            config('bpjs.pcare.endpoints.kunjungan.update'),
            $data,
            [
                'activity_type' => 'update_kunjungan',
                'no_rawat' => $noRawat,
                'no_kunjungan' => $noKunjungan
            ]
        );
    }

    public function getKunjungan(string $noKunjungan)
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.kunjungan.detail') . $noKunjungan,
            logInfo: [
                'activity_type' => 'get_kunjungan',
                'no_kunjungan' => $noKunjungan
            ]
        );
    }

    /**
     * ====================================================================
     * TINDAKAN - Insert tindakan
     * ====================================================================
     */
    public function insertTindakan(array $data, string $noRawat, string $noKunjungan)
    {
        return $this->makeRequest(
            'POST',
            config('bpjs.pcare.endpoints.tindakan.insert'),
            $data,
            [
                'activity_type' => 'insert_tindakan',
                'no_rawat' => $noRawat,
                'no_kunjungan' => $noKunjungan
            ]
        );
    }

    /**
     * ====================================================================
     * RUJUKAN - Insert rujukan
     * ====================================================================
     */
    public function insertRujukan(array $data, string $noRawat, string $noKunjungan)
    {
        return $this->makeRequest(
            'POST',
            config('bpjs.pcare.endpoints.rujukan.insert'),
            $data,
            [
                'activity_type' => 'insert_rujukan',
                'no_rawat' => $noRawat,
                'no_kunjungan' => $noKunjungan
            ]
        );
    }

    /**
     * ====================================================================
     * REFERENSI - Get data referensi
     * ====================================================================
     */
    public function getProvider(string $start = '0', string $limit = '10')
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.provider') . "{$start}/{$limit}",
            logInfo: ['activity_type' => 'get_provider']
        );
    }

    public function getStatusPulang()
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.status_pulang'),
            logInfo: ['activity_type' => 'get_status_pulang']
        );
    }

    public function getSpesialis()
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.spesialis'),
            logInfo: ['activity_type' => 'get_spesialis']
        );
    }

    public function getSubSpesialis(string $kdSpesialis)
    {
        $endpoint = str_replace('{kode}', $kdSpesialis, config('bpjs.pcare.endpoints.subspesialis'));
        return $this->makeRequest(
            'GET',
            $endpoint,
            logInfo: ['activity_type' => 'get_subspesialis']
        );
    }

    public function getSarana()
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.sarana'),
            logInfo: ['activity_type' => 'get_sarana']
        );
    }

    public function getKhusus()
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.khusus'),
            logInfo: ['activity_type' => 'get_khusus']
        );
    }

    public function getDiagnosa(string $keyword, int $start = 0, int $limit = 20)
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.diagnosa.list') . "/{$keyword}/{$start}/{$limit}",
            logInfo: ['activity_type' => 'get_diagnosa']
        );
    }

    public function getObat(string $keyword, int $start = 0, int $limit = 20)
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.obat.list') . "/{$keyword}/{$start}/{$limit}",
            logInfo: ['activity_type' => 'get_obat']
        );
    }

    public function getTindakan(string $keyword, int $start = 0, int $limit = 20)
    {
        return $this->makeRequest(
            'GET',
            config('bpjs.pcare.endpoints.tindakan.list') . "/{$keyword}/{$start}/{$limit}",
            logInfo: ['activity_type' => 'get_tindakan']
        );
    }
}
