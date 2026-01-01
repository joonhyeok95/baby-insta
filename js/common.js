// function playVideo(element) {
//     // 이미 재생 중이면 중복 실행 방지
//     if (element.classList.contains('playing')) return;

//     const container = element.querySelector('.video-container');
//     const videoSrc = container.getAttribute('data-src');

//     // 비디오 태그 생성
//     const videoHtml = `
//         <video controls autoplay playsinline style="width:100%; height:100%; object-fit: cover;">
//             <source src="${videoSrc}" type="video/mp4">
//             브라우저가 영상을 지원하지 않습니다.
//         </video>
//     `;

//     // 컨테이너에 삽입 및 클래스 추가
//     container.innerHTML = videoHtml;
//     element.classList.add('playing');
// }

// function setRepresentative(fileId) {

//     if (!confirm('이 사진을 앨범 대표 사진으로 설정하시겠습니까?')) return;

//     // API 호출
//     fetch('/api/child/rep', {
//         method: 'POST',
//         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//         body: `file_id=${fileId}`
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status === 'success') {
//             alert('대표 사진으로 설정되었습니다.');
//             // UI 업데이트: 모든 배지를 숨기고 클릭한 곳만 보이기
//             document.querySelectorAll('.rep-badge').forEach(b => b.classList.add('d-none'));
//             element.querySelector('.rep-badge').classList.remove('d-none');
//         } else {
//             alert('설정 실패: ' + data.message);
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }