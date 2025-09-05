<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #dropZone {
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            transition: background-color 0.3s;
        }
        #dropZone.dragover {
            background-color: #e9ecef;
        }
        .preview-img {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Upload Image</h2>
        <div class="card p-4">
            <div class="mb-3">
                <label for="formFile" class="form-label">Choose an image (JPG, JPEG, PNG)</label>
                <div id="dropZone" class="rounded">
                    Drag and drop an image here or click to select
                </div>
                <input type="file" id="formFile" name="file" class="d-none" accept="image/jpeg,image/png">
                <img id="preview" class="preview-img" alt="Image preview">
            </div>
            <div class="progress d-none mb-3" id="progressBar">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <div id="errorMessage" class="alert alert-danger d-none"></div>
            <div id="successMessage" class="alert alert-success d-none"></div>
            <button type="button" class="btn btn-primary" id="uploadBtn">Upload</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const dropZone = document.getElementById('dropZone');
        const formFile = document.getElementById('formFile');
        const preview = document.getElementById('preview');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');
        const uploadBtn = document.getElementById('uploadBtn');
        const progressBar = document.getElementById('progressBar');
        const allowedTypes = ['image/jpeg', 'image/png'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        // Drag-and-drop events
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            formFile.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        });

        // Click to open file dialog
        dropZone.addEventListener('click', () => formFile.click());

        // Preview image
        formFile.addEventListener('change', () => showPreview(formFile.files[0]));

        function showPreview(file) {
            if (file && allowedTypes.includes(file.type)) {
                const reader = new FileReader();
                reader.onload = () => {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Upload button click
        uploadBtn.addEventListener('click', () => {
            const file = formFile.files[0];
            errorMessage.classList.add('d-none');
            successMessage.classList.add('d-none');
            progressBar.classList.add('d-none');

            if (!file) {
                errorMessage.textContent = 'Please select a file.';
                errorMessage.classList.remove('d-none');
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                errorMessage.textContent = 'Only JPG, JPEG, or PNG files are allowed.';
                errorMessage.classList.remove('d-none');
                return;
            }

            if (file.size > maxSize) {
                errorMessage.textContent = 'File is too large. Max size is 5MB.';
                errorMessage.classList.remove('d-none');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            $.ajax({
                url: 'upload_file_url.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json', // Expect JSON response
                xhr: function() {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.classList.remove('d-none');
                            progressBar.querySelector('.progress-bar').style.width = percent + '%';
                        }
                    });
                    return xhr;
                },
                success: (res) => {
                    progressBar.classList.add('d-none');
                    console.log('Server response:', res); // Debugging
                    if (res.error) {
                        errorMessage.textContent = res.error;
                        errorMessage.classList.remove('d-none');
                    } else if (res.url) {
                        successMessage.innerHTML = `File uploaded successfully: <a href="${res.url}" target="_blank">${res.url}</a>`;
                        successMessage.classList.remove('d-none');
                    } else {
                        errorMessage.textContent = 'No URL returned from server.';
                        errorMessage.classList.remove('d-none');
                    }
                },
                error: (xhr, status, error) => {
                    progressBar.classList.add('d-none');
                    console.error('AJAX error:', status, error, xhr.responseText); // Debugging
                    errorMessage.textContent = `Upload failed: ${status} - ${error}. Response: ${xhr.responseText}`;
                    errorMessage.classList.remove('d-none');
                }
            });
        });
    </script>
</body>
</html>