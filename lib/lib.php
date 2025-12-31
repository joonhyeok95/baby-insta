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

?>