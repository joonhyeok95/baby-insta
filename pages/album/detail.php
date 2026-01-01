<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/pages/api_handler.php';

// 자식 정보 get

// 1. 파라미터로 넘어온 날짜 확인 (예: ?date=2025-12-31)
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$displayDate = date('Y. m. d.', strtotime($selectedDate));
// PHP의 strtotime을 사용하여 날짜 계산
$prevDate = date('Y-m-d', strtotime($selectedDate . ' -1 day'));
$nextDate = date('Y-m-d', strtotime($selectedDate . ' +1 day'));

// 2. 해당 날짜의 데이터만 가져오기 (DB 연동 시 SQL: WHERE TAKEN_AT = '$selectedDate')
// 여기서는 API를 호출하여 해당 날짜의 사진 배열만 필터링합니다.
$year = date('Y', strtotime($selectedDate));
$month = date('m', strtotime($selectedDate));
$result = fetchAlbumData($year, $month);

$dayPhotos = array_filter($result['photos'], function($p) use ($selectedDate) {
    return $p['taken_at'] === $selectedDate;
});
$dayPhotos = array_values($dayPhotos); // 인덱스 재정렬

// D-Day 계산 (예: 2025-12-17이 태어난 날이라고 가정)
$birthDate = new DateTime("2025-12-18");
$currentDate = new DateTime($selectedDate);
// 두 날짜의 차이 객체 생성
$interval = $birthDate->diff($currentDate);

// $interval->invert가 1이면 과거, 0이면 미래/현재입니다.
if ($currentDate < $birthDate) {
    // 1. 태어나기 전인 경우 (D- 표시)
    $diff = "D-" . $interval->days; 
} else {
    // 2. 당일 포함 미래인 경우 (D+ 표시, 당일은 D+1)
    $diff = "D+" . ($interval->days + 1);
}
?>

<div class="detail-header">
    <i class="bi bi-chevron-left fs-4" onclick="history.back(-1)" style="cursor:pointer;"></i>
    <div class="date-selector">
        <i class="bi bi-caret-left-fill me-2" onclick="document.location.href='/album/detail?date=<?php echo $prevDate; ?>'"></i>
        <?= $displayDate ?>
        <i class="bi bi-caret-right-fill ms-2" onclick="document.location.href='/album/detail?date=<?php echo $nextDate; ?>'"></i>
    </div>
    <i class="bi bi-three-dots-vertical fs-4"></i>
</div>

<?php if (empty($dayPhotos)): ?>
    <div class="d-day-label"><?= $diff ?></div>
    <div class="empty-state">
        (+) 버튼을 눌러 <?= date('n월 j일', strtotime($selectedDate)) ?>의 사진 혹은 일기를 추가해보세요.
    </div>

<?php else: ?>
    <div class="main-photo-area">
        <?php
            $fileUrl = $dayPhotos[0]['image_url'];
            // 파일 확장자 추출 (소문자 변환)
            $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
            $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
            if ($isVideo):
        ?>
            <video class="album-media" autoplay muted loop playsinline>
                <source src="<?= $fileUrl ?>" type="video/<?= $extension === 'mov' ? 'mp4' : $extension ?>">
                브라우저가 비디오 태그를 지원하지 않습니다.
            </video>
            <div class="video-badge"><i class="bi bi-play-fill"></i></div>
        <?php else: ?>
            <img src="<?= $dayPhotos[0]['image_url'] ?>" alt="대표사진">
        <?php endif; ?>
        <div class="d-day-overlay"><?= $diff ?></div>
    </div>

    <div class="detail-grid mt-1">
        <?php 
            foreach($dayPhotos as $photo): 
                $fileUrl = $photo['image_url'];
                // 파일 확장자 추출 (소문자 변환)
                $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
        ?>
            <div class="grid-item">
                <?php if ($isVideo): ?>
                    <video class="album-media" autoplay muted loop playsinline>
                        <source src="<?= $fileUrl ?>" type="video/<?= $extension === 'mov' ? 'mp4' : $extension ?>">
                        브라우저가 비디오 태그를 지원하지 않습니다.
                    </video>
                    <div class="video-badge"><i class="bi bi-play-fill"></i></div>
                <?php else: ?>
                    <img src="<?= $photo['image_url'] ?>" class="album-media">
                    <?php if(strpos($photo['image_url'], '.mp4') !== false): // 동영상 분기 ?>
                        <i class="bi bi-play-circle video-icon"></i>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="fab-container">
    <div class="dropdown">
        <button class="btn-fab" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-plus"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mb-3 p-2">
            <li>
                <label class="dropdown-item d-flex align-items-center py-2" style="cursor: pointer;">
                    <i class="bi bi-image-fill text-success me-3 fs-5"></i> 
                    <span data-bs-toggle="modal" data-bs-target="#uploadModal">사진 업로드</span>
                </label>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="#"><i class="bi bi-pencil-square text-primary me-3 fs-5"></i> 일기 쓰기</a></li>
        </ul>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/pages/album/upload_modal.php'; ?>

<script>
/**
 * 상세화면 날짜 이동 스와이프 기능
 */
let touchstartX = 0;
let touchendX = 0;

// 1. 터치 시작 지점 기록
document.addEventListener('touchstart', e => {
    touchstartX = e.changedTouches[0].screenX;
}, false);

// 2. 터치 종료 지점 기록 및 방향 판정
document.addEventListener('touchend', e => {
    touchendX = e.changedTouches[0].screenX;
    handleGesture();
}, false);

function handleGesture() {
    const swipeThreshold = 100; // 최소 스와이프 거리 (픽셀)
    
    // 왼쪽으로 밀었을 때 (다음 날짜)
    if (touchstartX - touchendX > swipeThreshold) {
        location.href = 'detail.php?date=<?= $nextDate ?>';
    }
    
    // 오른쪽으로 밀었을 때 (이전 날짜)
    if (touchendX - touchstartX > swipeThreshold) {
        location.href = 'detail.php?date=<?= $prevDate ?>';
    }
}
</script>