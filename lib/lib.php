<?php
function resizeImage($sourcePath, $targetPath, $newWidth, $quality = 80) {
    list($width, $height, $type) = getimagesize($sourcePath);
    
    // 원본 비율 유지하며 세로 길이 계산
    $newHeight = ($height / $width) * $newWidth;
    
    // 이미지 타입에 따라 생성 함수 선택 (JPEG, PNG 등)
    switch ($type) {
        case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($sourcePath); break;
        case IMAGETYPE_PNG:  $src = imagecreatefrompng($sourcePath);  break;
        case IMAGETYPE_GIF:  $src = imagecreatefromgif($sourcePath);  break;
        default: return false;
    }

    // 새 도화지 만들기
    $dst = imagecreatetruecolor($newWidth, $newHeight);

    // PNG의 투명도 유지 설정 (선택 사항)
    if($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF){
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    // 리사이징 실행
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // 결과 저장 (품질 설정 적용) imagejpeg는 성공 시 true를 반환합니다.
    $result = imagejpeg($dst, $targetPath, $quality);

    // 메모리 해제
    imagedestroy($src);
    imagedestroy($dst);

    return $result; // 여기서 명확하게 true/false가 리턴됩니다.
}
function compressVideo($inputPath, $outputPath) {
    // [옵션 설명]
    // -vf scale=-1:720 : 가로 비율은 유지하고 세로를 720px로 맞춤 (모바일용 최적)
    // -vcodec libx264 : 가장 보편적인 고효율 코덱
    // -crf 28 : 숫자가 클수록 압축률 높음 (23~28 권장. 28은 용량이 매우 작아짐)
    // -preset faster : 압축 속도를 빠르게 설정
    // -acodec aac : 오디오 코덱 설정
    
    $command = "ffmpeg -i \"$inputPath\" -vf scale='if(gt(iw,ih),-2,720)':'if(gt(iw,ih),720,-2)' -vcodec libx264 -crf 28 -preset faster -acodec aac \"$outputPath\" 2>&1";
    
    exec($command, $output, $return_var);
    
    return [
        'success' => ($return_var === 0),
        'msg' => $output
    ];
}

?>