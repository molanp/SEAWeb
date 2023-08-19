<?php
class requests {
    public function get($url, $data=[], $cookie=[], $header=[]) {
        return $this->request($url, "GET", $data, $cookie, $header);
    }
    
    public function post($url, $data=[], $cookie=[], $header=[]) {
        return $this->request($url, "POST", $data, $cookie, $header);
    }
    
    public function put($url, $data=[], $cookie=[], $header=[]) {
        return $this->request($url, "PUT", $data, $cookie, $header);
    }
    
    public function delete($url, $data=[], $cookie=[], $header=[]) {
        return $this->request($url, "DELETE", $data, $cookie, $header);
    }
    
    public function patch($url, $data=[], $cookie=[], $header=[]) {
        return $this->request($url, "PATCH", $data, $cookie, $header);
    }
    
    public function head($url, $data=[], $cookie=[], $header=[]) {
        return $this->request($url, "HEAD", $data, $cookie, $header);
    }
    
    public function connect($url, $header=[]) {
        return $this->request($url, "CONNECT", [], [], $header);
    }
    
    public function trace($url, $header=[]) {
        return $this->request($url, "TRACE", [], [], $header);
    }
    
    public function options($url, $header=[]) {
        return $this->request($url, "OPTIONS", [], [], $header);
    }
    
    // 添加其他HTTP请求方法，可根据需要继续扩展
    
    private function request($url, $method, $data=[], $cookie=[], $headers=[],  $timeout = 5) {
         $url = $url . '?' . http_build_query($data); 
         $ch = curl_init(); 
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
         curl_setopt($ch, CURLOPT_URL, $url); 
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_HEADER, true); // 添加此行以获取头部信息
         curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.82"); 
         curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
         if (!empty($cookie)) { 
             $cookieString = ""; 
             foreach ($cookie as $key => $value) { 
                 $cookieString .= $key . "=" . $value . "; "; 
             } 
             curl_setopt($ch, CURLOPT_COOKIE, rtrim($cookieString, "; ")); 
         } 
         if (!empty($headers)) { 
             $headerString = []; 
             foreach ($headers as $key => $value) { 
                 $headerString[] = $key . ": " . $value; 
             } 
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headerString); 
         } 
         curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); 
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获取头部的大小
        $headers = substr($response, 0, $headerSize); // 截取头部信息
        $body = substr($response, $headerSize); // 获取响应体信息
        $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($response === false) { 
            return curl_error($ch); 
        } 
        
        // 返回Response对象
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
    
    public function json() {//返回json解析后数组或字符串
        return json($this->body);
    }
    
    public function text() {//返回原始内容
        return $this->body;
    }
    
    public function code() {//返回http状态码
        return $this->httpCode;
    }
    
    public function headers() {//返回响应头数组
        return $this->headers;
    }
}

// 使用示例
/*
$a = new requests();
$b = $a->get("url");
echo $b->text();

$c = $a->get("url")->json();
echo $c;

$d = $a->get("url")->code();
echo $d;

$e = $a->get("url")->headers();
echo $e;*/
?>