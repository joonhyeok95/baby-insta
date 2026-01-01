<?php
// header('Content-Type: application/json; charset=utf-8');
// header('Access-Control-Allow-Origin: *');// 3. 파라미터 받기 (년/월 필터링용)
$year  = isset($_GET['y']) ?  $_GET['y'] : date('Y');
$month = isset($_GET['m']) ? $_GET['m'] : date('n');
// 한 자리 숫자일 경우 앞에 '0'을 붙여 2자리로 만듭니다.
$month = str_pad($month, 2, '0', STR_PAD_LEFT);

$user = "USER_001";

// 4. SQL 쿼리 작성 
// 특정 년-월에 해당하는 데이터만 가져오며, 최신순으로 정렬합니다.
$sql = "SELECT 
            SUM(CASE WHEN ext IN ('jpg', 'jpeg', 'png', 'gif', 'webp') THEN 1 ELSE 0 END) AS image_count,
            SUM(CASE WHEN ext IN ('mp4', 'mov', 'avi', 'webm') THEN 1 ELSE 0 END) AS video_count
        FROM (
            SELECT LOWER(SUBSTRING_INDEX(FILE_NAME_ORG, '.', -1)) AS ext 
            FROM tb_file 
            WHERE user_id = '".$user."' AND taken_at LIKE '".$year."-".$month."%'
        ) AS photo_extensions";

$result = $mysqli->query( $sql);

if ($result) {
    $row = $result->fetch_assoc();
}

// 5. 최종 JSON 출력
$response = [
    "status" => "success",
    "year" => $year,
    "month" => $month,
    "result" => $row
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>