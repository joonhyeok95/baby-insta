<?php
// 3. POST 데이터 받기
$id        = $_POST['id'] ?? '';
$email     = $_POST['email'] ?? '';
$raw_pwd   = $_POST['pwd'] ?? '';
$username  = $_POST['username'] ?? '';
$name      = $_POST['name'] ?? '';
$profile   = $_POST['profile_image_url'] ?? '';

// 4. 유효성 검사 (필수값 체크)
if (empty($id) || empty($email) || empty($raw_pwd) || empty($username)) {
    echo json_encode(["status" => "error", "message" => "필수 입력값이 누락되었습니다."]);
    exit;
}

// 5. 비밀번호 단방향 해시 암호화 (BCrypt)
$hashed_pwd = password_hash($raw_pwd, PASSWORD_DEFAULT);

// 6. SQL 작성 및 실행 (Prepared Statement 권장)
$sql = "INSERT INTO users (ID, EMAIL, PWD, USERNAME, NAME, PROFILE_IMAGE_URL) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
// 파라미터 바인딩 (s: string)
$stmt->bind_param( "ssssss", $id, $email, $hashed_pwd, $username, $name, $profile);

if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "사용자가 성공적으로 생성되었습니다.",
            "user_id" => $id
        ]);
} else {
    // 중복된 ID나 EMAIL 처리
    $error = mysqli_stmt_error($stmt);
    echo json_encode(["status" => "error", "message" => "등록 실패: " . $error]);
}

$stmt->close();
?>