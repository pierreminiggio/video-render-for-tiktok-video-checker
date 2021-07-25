<?php

namespace PierreMiniggio\VideoRenderForTiktokVideoChecker;

class VideoRenderForTiktokVideoChecker
{

    public function __construct(private string $spinnerApiUrl, private string $spinnerApiToken)
    {
    }

    public function getRenderedVideoUrl(string $tikTokUrl): ?string
    {

        $curl = curl_init($this->spinnerApiUrl . '/tiktok-video-file');

        $authHeader = ['Content-Type: application/json' , 'Authorization: Bearer ' . $this->spinnerApiToken];
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $authHeader,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $tikTokUrl
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            return null;
        }

        if (! $response) {
            return null;
        }

        $jsonResponse = json_decode($response, true);

        if (! $jsonResponse) {
            return null;
        }

        if (! isset($jsonResponse['id']) || ! isset($jsonResponse['hasRenderedFile'])) {
            return null;
        }

        if ($jsonResponse['hasRenderedFile'] === false) {
            return null;
        }

        return $this->spinnerApiUrl . '/render/' . $jsonResponse['id'];
    }
}
