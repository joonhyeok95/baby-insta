<?php
$uploadDir = $_SERVER['DOCUMENT_ROOT']. '/uploads/'; // 저장할 서버 경로
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

//$date = intval($_POST['date'] ?? date('y-m-d'));
$image_url = $_POST['image_url'] ?? '';
if (!$image_url && !empty($_POST['image_url'])) {
  $image_url = $_POST['image_url'];
}
$opacity = floatval($_POST['opacity'] ?? 0.3);

// 이미지 업로드
// 1. 전송된 파일 배열 가져오기
$files = $_FILES['image_files'];
$count = count($files['name']);
$success_count = 0;

$taken_at = $_POST['taken_at']; // 사용자 선택 날짜
$child_id = 'BABY_001';        // 세션 등에서 가져온 아이 ID
$user_id  = 'USER_001';        // 세션에서 가져온 사용자 ID
$now      = date('Y-m-d H:i:s');

for ($i = 0; $i < $count; $i++) {
  // 개별 파일 에러 체크
  $success = false;
  if ($files['error'][$i] === UPLOAD_ERR_OK) {
    $fileTmpPath = $files['tmp_name'][$i];
    $fileName = basename($files['name'][$i]);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    // $allowedExts = ['jpg', 'jpeg', 'png', 'gif','mov','mp4', 'webm', 'ogg'];
    // if (!in_array($fileExt, $allowedExts)) {
    //   echo json_encode(['status' => 'error', 'message' => '허용되지 않는 파일 형식입니다.'], JSON_UNESCAPED_UNICODE);
    //   exit;
    // }
    // 고유 파일명 생성
    $newFileName = uniqid('', true) . '.' . $fileExt;
    $destPath = $uploadDir . $newFileName;
    // 웹에서 접근 가능한 경로로 변경 필요
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $_SERVER['HTTP_HOST'];
    $image_url = $protocol . $domain . "/uploads/". $newFileName;
    $file_size = $files['size'][$i];
    $file_id  = uniqid('FILE_');

    if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
      $resizedStatus = resizeImage($fileTmpPath, $destPath, 1200, 80); // 압축
      if (!$resizedStatus) {
        // 리사이즈 실패 시 원본이라도 저장
        if(move_uploaded_file($fileTmpPath, $destPath)){
        } else {
          echo json_encode(['status' => 'error', 'message' => 'Image File Upload Error!!!'], JSON_UNESCAPED_UNICODE);
          exit;
        }
      }
    } else {
      // 영상 업로드
      if (move_uploaded_file($fileTmpPath, to: $destPath)) {

      } else {
        echo json_encode(['status' => 'error', 'message' => 'Movie File Upload Error!!!'], JSON_UNESCAPED_UNICODE);
        exit;
      }
    }
  }
  $sql = "INSERT INTO tb_file (
              FILE_ID, FILE_NAME, FILE_NAME_ORG, FILE_PATH, 
              TAKEN_AT, FILE_SIZE, CHILD_ID, USER_ID, 
              WRITE_DATE, CREATE_DATE
          ) VALUES (
              ?, ?, ?, ?, 
              ?, ?, ?, ?, 
              ?, ?
          )";

  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param("sssssissss", 
          $file_id, $newFileName, $fileName, $image_url, 
          $taken_at, $file_size, $child_id, $user_id, 
          $now, $now);
  $success = $stmt->execute();
}
$stmt->close();

if ($success) {
    echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
    $n_dt = new DateTime($taken_at);
    $redirectUrl = "/album/list?y=".$n_dt->format('Y')."&m=".$n_dt->format('n');
    header("Location: $redirectUrl");
} else {
    echo json_encode(['status' => 'error', 'message' => $mysqli->error], JSON_UNESCAPED_UNICODE);
}



?>
