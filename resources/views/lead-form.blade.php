<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Capture Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow">
            <h2 class="mb-4 text-center">Lead Capture Form</h2>
            <form id="leadForm">
                <input type="hidden" name="user_id" id="user_id" value="{{ $userId }}">

                <div class="mb-3">
                    <label class="form-label">First Name:</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name:</label>
                    <input type="text" class="form-control" name="last_name">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone:</label>
                    <input type="text" class="form-control" name="phone" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tag:</label>
                    <input type="text" class="form-control" name="tag">
                </div>

                <div class="mb-3">
                    <label class="form-label">Stage:</label>
                    <input type="text" class="form-control" name="stage">
                </div>

                <button type="submit" class="btn btn-success w-100">Submit</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById("leadForm").addEventListener("submit", function(event) {
                event.preventDefault();
                
                let formData = new FormData(this);
                
                axios.post('/api/leads', formData)
                    .then(response => {
                        Swal.fire({
                            title: "Success!",
                            text: "Lead Created Successfully!",
                            icon: "success",
                            confirmButtonText: "OK"
                        });

                        document.getElementById("leadForm").reset();
                    })
                    .catch(error => {
                        console.error(error);
                        
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to create lead. Please try again.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    });
            });
    </script>
</body>
</html>
