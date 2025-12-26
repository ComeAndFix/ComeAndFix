<div id="imageViewer" class="image-viewer-modal">
    <span class="image-viewer-close">&times;</span>
    <img class="image-viewer-content" id="viewerImage">
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageViewer = document.getElementById('imageViewer');
        const viewerImage = document.getElementById('viewerImage');
        const viewerClose = document.querySelector('.image-viewer-close');

        if (imageViewer && viewerImage) {
            window.openImageViewer = function(src) {
                viewerImage.src = src;
                imageViewer.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            };

            const closeImageViewer = function() {
                imageViewer.classList.remove('show');
                document.body.style.overflow = '';
                setTimeout(() => {
                    viewerImage.src = '';
                }, 300);
            };

            if (viewerClose) {
                viewerClose.onclick = closeImageViewer;
            }

            imageViewer.onclick = function(e) {
                if (e.target !== viewerImage) {
                    closeImageViewer();
                }
            };

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && imageViewer.classList.contains('show')) {
                    closeImageViewer();
                }
            });
        }
    });
</script>
@endpush
