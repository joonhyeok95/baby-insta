<?php
// CHILD_ID는 보통 UUID나 고유 조합을 사용하지만, 여기서는 클라이언트에서 보낸다고 가정합니다.
// $child_id  = $_POST['child_id']  ?? '';
$fileId = $_POST['file_id'] ?? '';

// 4. 필수값 유효성 검사
if (!$fileId) {
    echo json_encode(['status' => 'error', 'message' => 'ID가 없습니다.']);
    exit;
}
// 2. 선택한 파일의 날짜(taken_at)와 유저 ID 조회
$query = "SELECT taken_at, user_id FROM tb_file WHERE FILE_ID = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $fileId);
$stmt->execute();
$result = $stmt->get_result();
$fileInfo = $result->fetch_assoc();

if (!$fileInfo) {
    throw new Exception("파일 정보를 찾을 수 없습니다.");
}

$takenAt = $fileInfo['taken_at'];
$userId = $fileInfo['user_id'];

// 3. 같은 날짜의 기존 대표 사진들을 모두 'N'으로 변경
$resetQuery = "UPDATE tb_file SET is_rep = 'N' WHERE user_id = ? AND taken_at = ?";
$resetStmt = $mysqli->prepare($resetQuery);
$resetStmt->bind_param("ss", $userId, $takenAt);
$resetStmt->execute();

// 4. 선택한 파일만 'Y'로 변경
$setQuery = "UPDATE tb_file SET is_rep = 'Y' WHERE FILE_ID = ?";
$setStmt = $mysqli->prepare($setQuery);
$setStmt->bind_param("s", $fileId);
$setStmt->execute();

// echo json_encode(['status' => 'success', 'date' => $takenAt]);

if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "대표사진이 변경되었습니다.",
            "file_id" => $fileId
        ]);
} else {
    // 중복된 ID나 EMAIL 처리
    $error = mysqli_stmt_error($stmt);
    echo json_encode(["status" => "error", "message" => "등록 실패: " . $error]);
}

$stmt->close();
?>