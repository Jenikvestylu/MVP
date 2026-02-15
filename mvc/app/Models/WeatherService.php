<?php
class WeatherService {
    public function getWeather($lat, $lon) {
        $apiUrl = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,weather_code&timezone=auto";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data['error']) && $data['error']) return ['error' => 'Chyba služby'];

        return $this->processRisk($data);
    }

    private function processRisk($data) {
        $temp = $data['current']['temperature_2m'];
        $humidity = $data['current']['relative_humidity_2m'];
        $code = $data['current']['weather_code'];

        $weatherDesc = "Jasno / Polojasno";
        if ($code >= 51 && $code <= 67) $weatherDesc = "Déšť / Mrholení";
        if ($code >= 71 && $code <= 77) $weatherDesc = "Sněžení";
        if ($code >= 95) $weatherDesc = "Bouřka";
        if ($code == 45 || $code == 48) $weatherDesc = "Mlha";

        $riskLevel = "none";
        $message = "Podmínky OK.";

        if ($temp <= 0) {
            $riskLevel = "high";
            $message = "POZOR: Mrzne! Riziko ledovky.";
        } elseif ($temp > 0 && $temp <= 3) {
            if ($humidity > 85 || ($code >= 51 && $code <= 67)) {
                $riskLevel = "medium";
                $message = "VAROVÁNÍ: Teplota u nuly a vlhko.";
            } else {
                $message = "Chladno, ale sucho.";
            }
        }

        return [
            'temp' => $temp,
            'desc' => $weatherDesc,
            'humidity' => $humidity,
            'risk' => $riskLevel,
            'message' => $message
        ];
    }
}