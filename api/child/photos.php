<?php
// header('Content-Type: application/json; charset=utf-8');
// header('Access-Control-Allow-Origin: *');// 3. 파라미터 받기 (년/월 필터링용)
$year  = isset($_GET['y']) ?  $_GET['y'] : date('Y');
$month = isset($_GET['m']) ? $_GET['m'] : date('m');

// 4. SQL 쿼리 작성 
// 특정 년-월에 해당하는 데이터만 가져오며, 최신순으로 정렬합니다.
$sql = "SELECT 
            FILE_ID as id, 
            USER_ID as user_id, 
            FILE_NAME_ORG as title, 
            FILE_PATH as image_url, 
            TAKEN_AT as taken_at,
            IS_REP as is_rep
        FROM tb_file 
        WHERE YEAR(TAKEN_AT) = '$year' AND MONTH(TAKEN_AT) = '$month'
        ORDER BY TAKEN_AT DESC, CREATE_DATE DESC";

$result = $mysqli->query( $sql);
$photos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // 웹에서 접근 가능한 경로로 수정 (예: ./uploads/ -> http://domain.com/uploads/)
        $row['image_url'] = str_replace('./', '/', $row['image_url']);
        $photos[] = $row;
    }
}

// 5. 최종 JSON 출력
$response = [
    "status" => "success",
    "year" => $year,
    "month" => $month,
    "total_count" => count($photos),
    "photos" => $photos
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>