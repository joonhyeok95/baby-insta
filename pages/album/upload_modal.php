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
                        <input type="date" name="taken_at" class="form-control rounded-3" 
                               value="<?= isset($selectedDate) ? $selectedDate : date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small text-secondary fw-bold">사진/동영상 선택 (다중 가능)</label>
                        <input type="file" name="image_files[]" class="form-control rounded-3" accept="image/*,video/*" multiple required>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold text-white py-3 rounded-3 shadow-sm" style="background-color: #FFA000; border: none;">
                        기록 저장하기
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>