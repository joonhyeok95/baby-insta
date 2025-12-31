<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/pages/api_handler.php';
// 1. 자녀 정보 GET
$birthDate = new DateTime("2025-12-18"); // 아이 생일
$today = new DateTime("now"); // 현재 날짜 (테스트 시 "2026-01-01" 등으로 변경 가능)
///////////////////////////////////////////////////////////////////////
// 상단 tab 자동 계산
$birthMonth = new DateTime($birthDate->format('Y-m-01')); // 생일 달의 1일
$today = new DateTime("now");
$currentMonth = new DateTime($today->format('Y-m-01')); // 현재 달의 1일
//   기간 내의 모든 년/월을 배열에 담기
$monthList = [];
$tempDate = clone $birthMonth;
while ($tempDate <= $currentMonth) {
    $monthList[] = [
        'year' => $tempDate->format("Y"),
        'month' => $tempDate->format("n"),
        'display' => $tempDate->format("Y") . " " . $tempDate->format("n") . "월"
    ];
    // 한 달씩 더하기
    $tempDate->modify('+1 month');
}
//   내림차순 정렬 (최신 월이 앞으로 오게 함)
$monthList = array_reverse($monthList);
//   현재 선택된 년/월 (파라미터가 없으면 가장 최신 월 선택)
$selectedYear = isset($_GET['y']) ? $_GET['y'] : $monthList[0]['year'];
$selectedMonth = isset($_GET['m']) ? $_GET['m'] : $monthList[0]['month'];

///////////////////////////////////////////////////////////////////////
// 월 데이터 검색
///////////////////////////////////////////////////////////////////////
$year = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');
$month = isset($_GET['m']) ? (int)$_GET['m'] : date('n');
// API 데이터 호출
$result = fetchAlbumData($year, $month);
$photos = $result['photos'];

?>
<div class="d-flex align-items-center justify-content-between p-3">
    <div class="user-info dropdown">
        <img src="https://i.namu.wiki/i/7O1crMPIK4ppy2n9BUtvQXsiS0UlYrbsluS91uODKRzt0GLyrUa7UtBGCfmUHuqfQjqUfHDsW3fZ4nbx32Z3lA.webp" class="user-avatar" alt="Avatar">
        <span class="fw-bold dropdown-toggle" data-bs-toggle="dropdown">임하신</span>
    </div>
    <div class="d-flex align-items-center">
    <div class="position-relative me-3">
        <i class="bi bi-bell fs-5"></i>
        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 0.5rem;">N</span>
    </div>
        <i class="bi bi-person fs-4"></i>
        </div>
</div>

<div class="album-tabs">
    <?php foreach ($monthList as $m): ?>
        <?php 
            $isActive = ($selectedYear == $m['year'] && $selectedMonth == $m['month']);
        ?>
        <div class="month-tab-item" 
             onclick="location.href='/album/list?y=<?= $m['year'] ?>&m=<?= $m['month'] ?>'"
             style="margin-right: 20px; cursor: pointer; white-space: nowrap; font-weight: <?= $isActive ? '800' : 'normal' ?>; color: <?= $isActive ? '#212529' : '#adb5bd' ?>; position: relative;">
            
            <?= $m['display'] ?>
            
            <?php if ($isActive): ?>
                <div style="position: absolute; bottom: -10px; left: 0; width: 100%; height: 3px; background-color: #FFB300;"></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="search-container">
    <div class="search-bar">
        <i class="bi bi-search text-secondary"></i>
        <input type="text" placeholder="키워드, 기록, 댓글로 찾을 수 있어요.">
    </div>
</div>

<div class="stat-scroll">
    <div class="stat-card">
        <div class="stat-title">사진</div>
        <div class="stat-count"><?= count($photos) ?></div>
        <img src="<?= $photos[0]['image_url'] ?? '' ?>" onerror="this.onerror=null; this.src='/img/no_img.png';" class="stat-img">
    </div>
    <div class="stat-card">
        <div class="stat-title">영상</div>
        <div class="stat-count">0</div>
        <img src="<?= $photos[0]['image_url'] ?? '' ?>" onerror="this.onerror=null; this.src='/img/no_img.png';" class="stat-img">
    </div>
    <div class="stat-card">
        <div class="stat-title">태그</div>
        <div class="stat-count">0</div>
    </div>
</div>

<div class="filter-bar">
    <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle fw-bold" data-bs-toggle="dropdown">사진</button>
    </div>
    <button class="btn-download"><i class="bi bi-download"></i> 전체 다운로드</button>
</div>

<div class="album-content">
    <?php
        // 임시 변수 초기화 (날짜 변경 감지용)
        $lastDate = "";
    ?>
    <?php if (empty($photos)): ?>
        <div class="empty-state p-5 text-center text-secondary">
            등록된 사진이 없습니다.
        </div>
    <?php else: ?>
        <?php foreach($photos as $photo): 
            $currentDate = $photo['taken_at']; // DB의 TAKEN_AT 값 (YYYY-MM-DD)
            $fileUrl = $photo['image_url'];
            // 파일 확장자 추출 (소문자 변환)
            $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
            $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);

            // 3. 이전 사진과 날짜가 다를 경우에만 헤더 출력
            if ($currentDate !== $lastDate): 
                $dateObj = new DateTime($currentDate);
                $dayOfWeek = ["일", "월", "화", "수", "목", "금", "토"][$dateObj->format('w')];
                
                // D-Day 계산
                $diff = $birthDate->diff($dateObj);
                $dDay = ($dateObj < $birthDate) ? "D-" . $diff->days : "D+" . ($diff->days + 1);
                
                // 그리드가 열려있었다면 닫아줌 (첫 번째가 아닐 때만)
                if ($lastDate !== "") echo '</div>'; 
        ?>
                <div class="album-date-header mt-4">
                    <?= $dateObj->format('Y. m. d.') ?> (<?= $dayOfWeek ?>) 
                    <span class="ms-1 text-secondary" style="font-weight: normal;"><?= $dDay ?></span>
                </div>
                
                <div class="photo-grid">
            <?php 
                $lastDate = $currentDate; // 마지막 날짜 갱신
            endif; 
            ?>

            <div class="photo-item" onclick="location.href='detail.php?date=<?= $currentDate ?>'">
                <?php if ($isVideo): ?>
                    <video class="album-media" autoplay muted loop playsinline>
                        <source src="<?= $fileUrl ?>" type="video/<?= $extension === 'mov' ? 'mp4' : $extension ?>">
                        브라우저가 비디오 태그를 지원하지 않습니다.
                    </video>
                    <div class="video-badge"><i class="bi bi-play-fill"></i></div>
                <?php else: ?>
                    <img src="<?= $photo['image_url'] ?>" alt="baby">
                    <?php if(strpos($photo['image_url'], '.mp4') !== false): ?>
                        <i class="bi bi-play-circle-fill position-absolute top-50 start-50 translate-middle text-white fs-3"></i>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        <?php endforeach; ?>
        </div> <?php endif; ?>   
</div>

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

<nav class="bottom-nav">
    <a href="/" class="nav-link"><i class="bi bi-calendar3"></i>캘린더</a>
    <a href="/album/list" class="nav-link active"><i class="bi bi-images"></i>앨범</a>
    <!-- <a href="#" class="nav-link"><i class="bi bi-chat-heart"></i>일기/수유</a>
    <a href="#" class="nav-link"><i class="bi bi-gem"></i>만들기</a>
    <a href="#" class="nav-link"><i class="bi bi-printer"></i>달력/인화</a> -->
</nav>
