<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/pages/api_handler.php';

// 1. 현재 선택된 년/월 파라미터 받기 (기본값: 현재 날짜)
$year = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
$month = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('n');
// 3. 이전 날짜(-1 day) 계산 및 y, m, d 추출
$currentDateStr = sprintf("%04d-%02d-%02d", $year, $month, 1);
$prevDateObj = new DateTime($currentDateStr);
$prevDateObj->modify('-1 month');
$prevY = $prevDateObj->format('Y');
$prevM = $prevDateObj->format('n');
$prevD = $prevDateObj->format('j');

// 4. 다음 날짜(+1 day) 계산 및 y, m, d 추출
$nextDateObj = new DateTime($currentDateStr);
$nextDateObj->modify('+1 month');
$nextY = $nextDateObj->format('Y');
$nextM = $nextDateObj->format('n');
$nextD = $nextDateObj->format('j');

// 2. 월 이동 계산 (1월 이전은 작년 12월, 12월 다음은 내년 1월)
$prev_ts = strtotime("$year-$month-01 -1 month");
$prev_y = date('Y', $prev_ts);
$prev_m = date('n', $prev_ts);

$next_ts = strtotime("$year-$month-01 +1 month");
$next_y = date('Y', $next_ts);
$next_m = date('n', $next_ts);

// 3. API 또는 JSON 파일에서 해당 년/월 데이터 가져오기 (필터링 적용)
$result = fetchCalendarData($year, $month); 
$photoMap = getPhotoMap($result['photos']);

// 4. 달력 그리드 계산
$firstDay = date('w', strtotime("$year-$month-01"));
$daysInMonth = date('t', strtotime("$year-$month-01"));
?>
<!-- Intro -->
<div id="intro-layer">
    <div class="intro-content">
        <h1 class="intro-text">MY BABY</h1>
        <div class="intro-line"></div>
    </div>
</div>
<!-- Main Content -->
<div id="main-content">
<div class="user-top-bar border-bottom">
    <div class="user-info dropdown">
        <img src="https://i.namu.wiki/i/7O1crMPIK4ppy2n9BUtvQXsiS0UlYrbsluS91uODKRzt0GLyrUa7UtBGCfmUHuqfQjqUfHDsW3fZ4nbx32Z3lA.webp" class="user-avatar" alt="Avatar">
        <span class="fw-bold dropdown-toggle" data-bs-toggle="dropdown">임하신</span>
        <ul class="dropdown-menu shadow border-0">
             <li><a class="dropdown-item" href="?y=<?= date('Y') ?>&m=<?= date('n') ?>">이번달로 이동</a></li>
        </ul>
    </div>
    <div class="d-flex align-items-center">
        <div class="notification-badge me-3">
            <i class="bi bi-bell fs-5"></i>
            <span class="badge-dot">2</span>
        </div>
        <i class="bi bi-person fs-4"></i>
    </div>
</div>

<div class="p-3 bg-white d-flex align-items-center justify-content-between">
    <div class="dropdown">
        <h4 class="m-0 fw-bold dropdown-toggle" data-bs-toggle="dropdown" role="button">
            <?= $year ?>. <?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>.
        </h4>
        <ul class="dropdown-menu shadow border-0" style="max-height: 300px; overflow-y: auto;">
            <?php 
            for($i = -6; $i <= 6; $i++): 
                $ts = strtotime("$year-$month-01 $i month");
                $ty = date('Y', $ts);
                $tm = date('n', $ts);
            ?>
                <li>
                    <a class="dropdown-item <?= ($ty==$year && $tm==$month)?'active':'' ?>" 
                       href="?y=<?= $ty ?>&m=<?= $tm ?>">
                        <?= $ty ?>. <?= str_pad($tm, 2, '0', STR_PAD_LEFT) ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
    </div>

<div class="calendar-section border-top">
    <div class="row g-0 py-2 border-bottom">
        <?php foreach(['일','월','화','수','목','금','토'] as $label): ?>
            <div class="col-1-7 day-label small text-secondary fw-bold"><?= $label ?></div>
        <?php endforeach; ?>
    </div>
    <div class="row g-0">
        <?php for($i=0; $i<$firstDay; $i++): ?>
            <div class="col-1-7 calendar-cell"></div>
        <?php endfor; ?>

        <?php for($day=1; $day<=$daysInMonth; $day++): 
            $currentDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $photo = $photoMap[$currentDate] ?? null;
            // 파일 확장자 추출 (소문자 변환)
            $isToday = (date('Y-m-d') === $currentDate);
        ?>
            <div class="col-1-7 calendar-cell" onclick="location.href='/album/detail?date=<?= $currentDate ?>'">
                <?php if($isToday): ?>
                    <div class="today-circle"><?= $day ?></div>
                <?php else: ?>
                    <span class="date-num"><?= $day ?></span>
                <?php endif; ?>
                
                <?php if($photo):
                      $extension = strtolower(pathinfo($photo['image_url'], PATHINFO_EXTENSION));
                      $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                  ?>
                  <div class="photo-container">
                  <?php if ($isVideo): ?>
                    <video class="album-media" autoplay muted loop playsinline>
                        <source src="<?= $photo['image_url'] ?>" type="video/<?= $extension === 'mov' ? 'mp4' : $extension ?>">
                        브라우저가 비디오 태그를 지원하지 않습니다.
                    </video>
                  <?php else: ?>
                    <img src="<?= $photo['image_url'] ?>" class="baby-thumb shadow-sm">
                  <?php endif; ?>
                  </div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
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

<nav class="bottom-nav">
    <a href="#" class="nav-link active"><i class="bi bi-calendar3-event"></i>캘린더</a>
    <a href="/album/list" class="nav-link"><i class="bi bi-images"></i>앨범</a>
</nav>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">새로운 기록 업로드</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/api/file/add" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold">촬영 날짜</label>
                        <input type="date" name="taken_at" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-4">
                      <label class="form-label small text-secondary fw-bold">사진/동영상 선택 (다중 선택 가능)</label>
                      <input type="file" name="image_files[]" class="form-control rounded-3" accept="image/*,video/*" multiple required>
                      <div class="form-text text-muted">여러 장의 사진이나 영상을 한 번에 선택할 수 있습니다.</div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold text-white py-3 rounded-3 shadow-sm" style="background-color: #FFA000; border: none;">
                        기록 저장하기
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script>
  $(document).ready(function() {
    // 1. 세션 저장소에서 인트로 확인 여부 체크
    const isIntroShown = sessionStorage.getItem('intro_shown');
    if (!isIntroShown) {
        // --- 처음 들어온 경우: 인트로 실행 ---
        
        // 1.5초(또는 애니메이션 시간) 후에 인트로 레이어 제거
        setTimeout(function() {
            $('#intro-layer').addClass('fade-out');
            
            setTimeout(function() {
                $('#intro-layer').hide();
                $('#main-content').fadeIn(1000);
                
                // 2. 인트로를 봤다는 기록을 저장
                sessionStorage.setItem('intro_shown', 'true');
            }, 1200);
            
        }, 2500); // 인트로 문구를 보여줄 시간

    } else {
        // --- 이미 인트로를 본 경우: 바로 메인 표시 ---
        $('#intro-layer').hide(); // 인트로 숨김
        $('#main-content').show(); // 메인 즉시 표시
    }
  });
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
        location.href = '/?y=<?= $nextY ?>&m=<?= $nextM ?>';
    }
    
    // 오른쪽으로 밀었을 때 (이전 날짜)
    if (touchendX - touchstartX > swipeThreshold) {
        location.href = '/?y=<?= $prevY ?>&m=<?= $prevM ?>';
    }
}
</script>