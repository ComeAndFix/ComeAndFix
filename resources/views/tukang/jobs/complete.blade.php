<x-app-layout>
    @vite(['resources/css/tukang/complete.css'])

    <div class="completion-page-wrapper">
        <div class="completion-container">
            <h1 class="page-title">
                <i class="bi bi-check-circle text-brand-orange"></i>
                Submit Job Completion
            </h1>

            <!-- Order Info Card -->
            <div class="order-info-card">
                <div class="order-info-header">
                    <div>
                        <h2 class="order-title">{{ $order->service->name }}</h2>
                        <p class="order-number">Order #{{ $order->order_number }}</p>
                    </div>
                    <span class="order-badge">
                        <i class="bi bi-tools"></i>
                        In Progress
                    </span>
                </div>

                @if($order->customer)
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #F1F2F4;">
                        <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--brand-orange);"></i>
                        <div>
                            <div style="font-weight: 600; color: var(--brand-dark);">{{ $order->customer->name }}</div>
                            <div style="font-size: 0.875rem; color: #6C757D;">{{ $order->customer->email }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Rejection Alert -->
            @if($order->completion && $order->completion->isRejected())
                <div class="rejection-alert">
                    <i class="bi bi-exclamation-triangle-fill rejection-alert-icon"></i>
                    <div class="rejection-alert-content">
                        <div class="rejection-alert-title">Previous Submission Was Rejected</div>
                        <p class="rejection-alert-message">{{ $order->completion->rejection_reason }}</p>
                    </div>
                </div>
            @endif

            <!-- Completion Form Card -->
            <div class="completion-form-card">
                <h3 class="form-section-title">
                    <i class="bi bi-clipboard-check text-brand-orange"></i>
                    Completion Details
                </h3>

                <form action="{{ route('tukang.jobs.submitCompletion', $order) }}" method="POST" enctype="multipart/form-data" id="completion-form">
                    @csrf

                    <!-- Work Description -->
                    <div class="form-group">
                        <label class="form-label">
                            Work Description
                            <span class="required">*</span>
                        </label>
                        <textarea 
                            name="description" 
                            id="description"
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="5" 
                            required
                            placeholder="Describe the work you completed, including any challenges faced and how they were resolved..."
                        >{{ old('description', $order->completion?->description) }}</textarea>
                        
                        <div class="character-counter" id="char-counter">
                            <span id="char-count">0</span> / 20 characters minimum
                        </div>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Provide a detailed description of the work completed (minimum 20 characters)</small>
                    </div>

                    <!-- Photo Upload -->
                    <div class="form-group">
                        <label class="form-label">
                            Upload Completion Photos
                            <span class="required">*</span>
                        </label>
                        
                        <div class="file-upload-wrapper">
                            <input 
                                type="file" 
                                name="photos[]" 
                                id="photos"
                                class="form-control file-upload-input @error('photos.*') is-invalid @enderror" 
                                multiple 
                                accept="image/jpeg,image/jpg,image/png" 
                                required
                                style="display: none;"
                            >
                            <label for="photos" class="file-upload-label" style="cursor: pointer; padding: 1.5rem; border: 2px dashed #E0E0E0; border-radius: 12px; background: #F8F9FA; transition: all 0.3s ease; display: flex; align-items: center; gap: 1rem;">
                                <div class="file-upload-icon">
                                    <i class="bi bi-cloud-upload"></i>
                                </div>
                                <div class="file-upload-text">
                                    <div class="file-upload-title">Click to upload photos</div>
                                    <div class="file-upload-subtitle">JPEG, JPG, PNG (max 2MB each) - Multiple files allowed</div>
                                </div>
                            </label>
                        </div>

                        @error('photos.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Photo Preview Grid -->
                        <div id="photo-preview-grid" class="photo-preview-grid" style="display: none;"></div>
                        
                        <small class="form-hint">Upload clear photos showing the completed work. At least one photo is required.</small>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            <span>Submit Completion</span>
                        </button>
                        <a href="{{ route('tukang.jobs.show', $order) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            <span>Cancel</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Character counter for description
        const descriptionTextarea = document.getElementById('description');
        const charCounter = document.getElementById('char-counter');
        const charCount = document.getElementById('char-count');

        function updateCharCount() {
            const length = descriptionTextarea.value.length;
            charCount.textContent = length;

            if (length < 20) {
                charCounter.classList.add('error');
                charCounter.classList.remove('warning');
            } else if (length < 50) {
                charCounter.classList.add('warning');
                charCounter.classList.remove('error');
            } else {
                charCounter.classList.remove('error', 'warning');
            }
        }

        descriptionTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count

        // Photo preview functionality
        const photoInput = document.getElementById('photos');
        const photoPreviewGrid = document.getElementById('photo-preview-grid');
        let selectedFiles = [];

        photoInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            // Validate file sizes
            const invalidFiles = files.filter(file => file.size > 2 * 1024 * 1024);
            if (invalidFiles.length > 0) {
                alert(`${invalidFiles.length} file(s) exceed the 2MB size limit and will be skipped.`);
            }

            // Filter valid files
            const validFiles = files.filter(file => file.size <= 2 * 1024 * 1024);
            selectedFiles = [...selectedFiles, ...validFiles];

            updatePhotoPreview();
        });

        function updatePhotoPreview() {
            if (selectedFiles.length === 0) {
                photoPreviewGrid.style.display = 'none';
                return;
            }

            photoPreviewGrid.style.display = 'grid';
            photoPreviewGrid.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'photo-preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}" class="photo-preview-img">
                        <button type="button" class="photo-preview-remove" onclick="removePhoto(${index})">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    photoPreviewGrid.appendChild(previewItem);
                };

                reader.readAsDataURL(file);
            });

            // Update the file input with the current selected files
            updateFileInput();
        }

        function removePhoto(index) {
            selectedFiles.splice(index, 1);
            updatePhotoPreview();
        }

        function updateFileInput() {
            // Create a new DataTransfer object to update the file input
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            photoInput.files = dataTransfer.files;
        }

        // Form validation before submit
        document.getElementById('completion-form').addEventListener('submit', function(e) {
            const description = descriptionTextarea.value.trim();
            
            if (description.length < 20) {
                e.preventDefault();
                alert('Please provide a description of at least 20 characters.');
                descriptionTextarea.focus();
                return false;
            }

            if (selectedFiles.length === 0) {
                e.preventDefault();
                alert('Please upload at least one photo of the completed work.');
                photoInput.focus();
                return false;
            }
        });

        // Enhance file upload label hover effect
        const fileLabel = document.querySelector('.file-upload-label');
        fileLabel.addEventListener('mouseenter', function() {
            this.style.borderColor = 'var(--brand-orange)';
            this.style.background = '#FFF3E0';
        });
        fileLabel.addEventListener('mouseleave', function() {
            this.style.borderColor = '#E0E0E0';
            this.style.background = '#F8F9FA';
        });
    </script>
</x-app-layout>
