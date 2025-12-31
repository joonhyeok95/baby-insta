<?php
/**
 * API 서버로부터 데이터를 호출하는 함수
 */
function fetchAlbumData($year, $month) {
    // 1. API 주소 설정 (본인의 서버 도메인이나 response.php 경로 입력)
    // 같은 서버라면 절대경로 URL을, 테스트 중이라면 localhost URL을 입력하세요.
    $apiUrl = "http://localhost/api/child/photos?y=$year&m=$month";

    // 2. cURL을 이용한 API 호출
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 결과를 문자열로 반환
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);           // 5초 타임아웃
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // HTTPS 인증서 검사 제외 (테스트용)

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 3. 응답 결과 처리
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        
        // 4. 현재 선택된 년-월(YYYY-MM) 데이터만 필터링 (서버에서 안 해줄 경우를 대비)
        $currentFilter = sprintf("%04d-%02d", $year, $month);
        $filteredPhotos = array_filter($data['photos'] ?? [], function($photo) use ($currentFilter) {
            return strpos($photo['taken_at'], $currentFilter) === 0;
        });

        return ['photos' => array_values($filteredPhotos)];
    } else {
        // 호출 실패 시 빈 배열 반환
        return ['photos' => []];
    }
}

/**
 * 날짜를 Key로 하는 맵핑 배열 생성 (달력 바인딩용)
 */
function getPhotoMap($photos) {
    $map = [];
    foreach ($photos as $photo) {
        $date = $photo['taken_at'];
        if (!isset($map[$date])) {
            $map[$date] = $photo;
        }
    }
    return $map;
}
?>