<?php
class requests {
    private $time = 5;
    private $cookie = [];
    private $header = [];
    
    public function timeout($time) {
        $this->time = $time;
    }
    
    public function cookie($cookie) {
        $this->cookie = $cookie;
    }
    
    public function header($header) {
        $this->header = $header;
    }

    public function get($url, $data=[]) {
        return $this->request($url, "GET", $data);
    }
    
    public function post($url, $data=[]) {
        return $this->request($url, "POST", $data);
    }
    
    public function put($url, $data = null) {
        $ip = rand(10,200).'.'.rand(10,200).'.'.rand(10,200).'.'.rand(10,200);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-FORWARDED-FOR:$ip", "CLIENT-IP:$ip"]);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.82");
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->time);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        if (!empty($this->header)) {
            $headerString = [];
            foreach ($this->header as $key => $value) {
                $headerString[] = $key . ": " . $value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerString);
        }

        if (!empty($this->cookie)) {
            $cookieString = "";
            foreach ($this->cookie as $key => $value) {
                $cookieString .= $key . "=" . $value . "; ";
            }
            curl_setopt($ch, CURLOPT_COOKIE, rtrim($cookieString, "; "));
        }

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_PUT, 1);
            curl_setopt($ch, CURLOPT_INFILE, fopen($data, 'rb'));
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($data));
        }

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($response === false) {
            return curl_error($ch);
        }
        return new Response($headers, $body, $httpCode);
    }
    
    public function delete($url, $data=[]) {
        return $this->request($url, "DELETE", $data);
    }
    
    public function patch($url, $data=[]) {
        return $this->request($url, "PATCH", $data);
    }
    
    public function head($url, $data=[]) {
        return $this->request($url, "HEAD", $data);
    }
    
    public function connect($url) {
        return $this->request($url, "CONNECT");
    }
    
    public function trace($url) {
        return $this->request($url, "TRACE");
    }
    
    public function options($url) {
        return $this->request($url, "OPTIONS");
    }
        
    private function request($url, $method, $data=[]) {
         $url = $url . '?' . http_build_query($data);
         $ip = rand(10,200).'.'.rand(10,200).'.'.rand(10,200).'.'.rand(10,200);
         $ch = curl_init(); 
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
         curl_setopt($ch, CURLOPT_URL, $url); 
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_HEADER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-FORWARDED-FOR:$ip", "CLIENT-IP:$ip"]);
         curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.82"); 
         curl_setopt($ch, CURLOPT_TIMEOUT, $this->time); 
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
         if (!empty($this->cookie)) { 
             $cookieString = ""; 
             foreach ($this->cookie as $key => $value) { 
                 $cookieString .= $key . "=" . $value . "; "; 
             } 
             curl_setopt($ch, CURLOPT_COOKIE, rtrim($cookieString, "; ")); 
         } 
         if (!empty($this->header)) { 
             $headerString = []; 
             foreach ($this->header as $key => $value) { 
                 $headerString[] = $key . ": " . $value; 
             } 
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headerString); 
         } 
         curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); 
         $response = curl_exec($ch);
         $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
         $headers = substr($response, 0, $headerSize);
         $body = substr($response, $headerSize);
         $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
         curl_close($ch);
         if ($response === false) { 
            return curl_error($ch); 
         } 
         return new Response($headers, $body, $httpCode);
    }
}
class Response extends requests {
    private $headers;
    private $body;
    private $httpCode;
    
    public function __construct($headers, $body, $httpCode) {
        $this->headers = $headers;
        $this->body = $body;
        $this->httpCode = $httpCode;
    }
    
    public function json() {
        return json($this->body);
    }
    
    public function content() {
        return $this->body;
    }
    
    public function code() {
        return $this->httpCode;
    }
    
    public function headers() {
        return $this->headers;
    }
}
?>