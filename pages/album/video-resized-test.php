<?php
header('Content-Type: text/html; charset=utf-8');
function compressVideo($inputPath, $outputPath) {
    // [옵션 설명]
    // -vf scale=-1:720 : 가로 비율은 유지하고 세로를 720px로 맞춤 (모바일용 최적)
    // -vcodec libx264 : 가장 보편적인 고효율 코덱
    // -crf 28 : 숫자가 클수록 압축률 높음 (23~28 권장. 28은 용량이 매우 작아짐)
    // -preset faster : 압축 속도를 빠르게 설정
    // -acodec aac : 오디오 코덱 설정
    
    $command = "ffmpeg -i \"$inputPath\" -vf scale=-1:720 -vcodec libx264 -crf 28 -preset faster -acodec aac \"$outputPath\" 2>&1";
    
    exec($command, $output, $return_var);
    
    return [
        'success' => ($return_var === 0),
        'msg' => $output
    ];
}

// 절대 경로를 사용하는 것이 가장 안전합니다.
$ffmpegPath = "ffmpeg"; 
$videoFile = "C:\\Users\\Metanet\\Desktop\\xampp\\htdocs\\uploads\\695557108cf1f5.56695802.mov";
$videoResizedFile = "C:\\Users\\Metanet\\Desktop\\xampp\\htdocs\\uploads\\resized_695557108cf1f5.56695802.mov";
$thumbFile = "C:\\Users\\Metanet\\Desktop\\xampp\\htdocs\\uploads\\thumb_695557108cf1f5.56695802.png";

// 1초 지점에서 썸네일 한 장 추출
// $command = "$ffmpegPath -i \"$videoFile\" -ss 00:00:01 -vframes 1 \"$thumbFile\" 2>&1";
$command = "$ffmpegPath -i \"$videoFile\" -ss 00:00:01 -vframes 1 \"$thumbFile\" 2>&1";

// 비디오 리사이징
compressVideo($videoFile, $videoResizedFile);
// 썸네일 추출
exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "썸네일 생성 성공!";
} else {
    echo "<b style='color:red'>실패 (에러 코드: $return_var)</b><br>";
    echo "<b>상세 사유:</b><br><pre>";
    foreach ($output as $line) {
        // 깨지는 한글을 UTF-8로 변환하여 출력
        echo iconv('CP949', 'UTF-8', $line) . "\n";
    }
    echo "</pre>";
}
?>