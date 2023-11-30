<?php
class xb {
    public function getInfo() {
        return [
            'name' => '喜/悲报生成器',
            'version' => '1.0',
            'profile'=> '一键生成喜/悲报',
            'method'=>'GET',
            'author'=>'molanp',
            'type'=>'memes',
            'request_par'=> re_par(["*content"=>"喜/悲报内容", "type"=>"生成类型，`0`为喜报，`1`为悲报，默认为`0`"]),
            'return_par'=> re_par(),
        ];
    }
    private function drawPost($text = '', $imageMode = 0) {
        if (mb_strlen($text) > 100) {
            _return_("字数超出最大限制", 400);
        }
        $happy = __DIR__.'/happy.jpg';
        $sad = __DIR__.'/sad.jpg';
        if ($imageMode==1) {
            list($width, $height) = getimagesize($sad);
        } else {
            list($width, $height) = getimagesize($happy);
        }
        $canvas = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($canvas, 255, 255, 0);
        if ($imageMode == 1)
        {
            $backgroundColor = imagecolorallocate($canvas, 34, 34, 34);
        }
        imagefill($canvas, 0, 0, $backgroundColor);
        $jpg = $imageMode == 0 ? $happy : $sad;
        $image = imagecreatefromjpeg($jpg);
        imagecopy($canvas, $image, 0, 0, 0, 0, $width, $height);
        $centerX = $width / 2;
        $centerY = $height / 2;
        $lines = array_filter(explode("\n", trim($text)));
        $fontFile = __DIR__.'/font.ttf';
        $fontSize = 200;
        $fontColor = imagecolorallocate($canvas, 195, 20, 27);
        if ($imageMode == 1)
        {
            $fontColor = imagecolorallocate($canvas, 238, 238, 238);
        }
        //阴影偏移
        $shadowOffsetX = 6;
        $shadowOffsetY = 6;
        $shadowColor = imagecolorallocate($canvas, 128, 128, 128);
        foreach ($lines as $index => $line)
        {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $line);
            $textWidth = $bbox[2] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[7];
            
            $textBoxWidth = $textWidth + 2 * $shadowOffsetX;
            $textBoxHeight = $textHeight + 2 * $shadowOffsetY;
            
            while ($textBoxWidth > $width || $textBoxHeight > $height) {
                $fontSize -= 2;
                $bbox = imagettfbbox($fontSize, 0, $fontFile, $line);
                $textWidth = $bbox[2] - $bbox[0];
                $textHeight = $bbox[1] - $bbox[7];
                $textBoxWidth = $textWidth + 2 * $shadowOffsetX;
                $textBoxHeight = $textHeight + 2 * $shadowOffsetY;
            }
            
            $x = $centerX - $textWidth / 2;
            $y = $centerY - count($lines) * $textHeight / 2 + ($index + 0.5) * $textHeight;
            imagettftext($canvas, $fontSize, 0, intval($x + $shadowOffsetX), intval($y + $shadowOffsetY), $shadowColor, $fontFile, $line);
            imagettftext($canvas, $fontSize, 0, intval($x - 2), intval($y - 2), $backgroundColor, $fontFile, $line);
            imagettftext($canvas, $fontSize, 0, intval($x), intval($y), $fontColor, $fontFile, $line);
        }
        header("Content-type: image/jpeg");
        imagejpeg($canvas);
        imagedestroy($canvas);
    }

    public function run($req) {
        $this->drawPost($req["content"]??"", $req["type"]??0);
    }
}