<?php
class ascii {
    public function getInfo()
    {
        return [
            'name' => 'ASCII字符画生成',
            'version' => '1.0',
            'profile' => '上传图片生成字符画',
            'method' => 'PUT',
            'author' => 'molanp',
            'request_par' => re_par(),
            'return_par' => re_par()
        ];
    }
    public function run()
    {
        $input = file_get_contents('php://input');
        $fileType = finfo_buffer(finfo_open(), $input, FILEINFO_MIME_TYPE);
        $fileSize = strlen($input);
        if ($fileType === 'image/jpeg' && $fileSize < 5000000) {
            $image =  imagecreatefromstring($input);
            $width = imagesx($image);
            $height = imagesy($image);
            $chars = ['M', 'W', 'H', '#', '%', 'X', 'D', '8', 'A', '4', 'w', 'p', '0', '3', 'u', '?', '7', 'i', '{', '+', 't', 'c', '!', '<', '"', '~', ':', ',', '^', '.', '`', ' '];
            $ascii = "";
            $aspectRatio = $width / $height;
            $charWidth = 8;
            $charHeight = 12;
            $aspectRatioChar = $charWidth / $charHeight;
            $scaleFactor = $aspectRatio / $aspectRatioChar;
            for ($y = 0; $y < $height; $y += $charHeight) {
                for ($x = 0; $x < $width; $x += $charWidth * $scaleFactor) {
                    $rgb = imagecolorat($image, $x, $y);
                    $red = ($rgb >> 16) & 0xFF;
                    $green = ($rgb >> 8) & 0xFF;
                    $blue = $rgb & 0xFF;
                    $gray = 0.299 * $red + 0.587 * $green + 0.114 * $blue;
                    $index = intval($gray / 255 * (count($chars) - 1));
                    if ($index >= 0 && $index < count($chars)) {
                        $ascii .= $chars[$index] . " ";
                    } else {
                        $ascii .= " ";
                    }
                }
                $ascii .= "\n";
            }
            imagedestroy($image);
            $data = [$ascii, 200];
        } else {
            $data =  ["文件必须为jpg格式且大小小于5M", 413];
        }
        _return_($data[0], $data[1]);
    }
}
