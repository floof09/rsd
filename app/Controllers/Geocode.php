<?php

namespace App\Controllers;

class Geocode extends BaseController
{
    public function reverse()
    {
        $lat = $this->request->getGet('lat');
        $lon = $this->request->getGet('lon');
        
        if (!$lat || !$lon) {
            return $this->response->setJSON(['error' => 'Missing coordinates']);
        }
        
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'RSD Application/1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            return $this->response->setJSON(json_decode($response, true));
        }
        
        return $this->response->setJSON(['error' => 'Geocoding failed']);
    }
    
    public function search()
    {
        $query = $this->request->getGet('q');
        $limit = $this->request->getGet('limit') ?? 5;
        
        if (!$query) {
            return $this->response->setJSON([]);
        }
        
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($query) . "&limit={$limit}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'RSD Application/1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            return $this->response->setJSON(json_decode($response, true));
        }
        
        return $this->response->setJSON([]);
    }
}
