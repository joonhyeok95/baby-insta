<?php
// CHILD_ID는 보통 UUID나 고유 조합을 사용하지만, 여기서는 클라이언트에서 보낸다고 가정합니다.
$child_id  = $_POST['child_id']  ?? ''; 
$user_id   = $_POST['user_id']   ?? ''; // 부모(사용자) ID
$name      = $_POST['name']      ?? '';
$birth_day = $_POST['birth_day'] ?? ''; // 형식: YYYY-MM-DD
$sex       = $_POST['sex']      ?? ''; // 예: M, F 혹은 남, 여

// 4. 필수값 유효성 검사
if (empty($child_id) || empty($user_id) || empty($name) || empty($birth_day) || empty($sex)) {
    echo json_encode(["status" => "error", "message" => "모든 필드를 입력해야 합니다."]);
    exit;
}

// 6. SQL 작성 및 실행 (Prepared Statement 권장)
$sql = "INSERT INTO child (CHILD_ID, USER_ID, NAME, BIRTH_DAY, SEX) VALUES (?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
// 파라미터 바인딩 (s: string)
$stmt->bind_param( "sssss", $child_id, $user_id, $name, $birth_day, $sex);

if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "자녀 정보가 등록되었습니다.",
            "child_id" => $child_id
        ]);
} else {
    // 중복된 ID나 EMAIL 처리
    $error = mysqli_stmt_error($stmt);
    echo json_encode(["status" => "error", "message" => "등록 실패: " . $error]);
}

$stmt->close();
?>