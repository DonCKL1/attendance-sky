<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance System - KNUST</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: url('/images/knust.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .container {
            max-width: 1200px;
        }
        
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            padding: 25px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.);
        }
        
        .logo {
            width: 100px;
            height: 100px;
            margin-bottom: 15px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0d6efd;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 15px 25px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }
        
        #current-time {
            font-weight: 700;
            color: #0d6efd;
        }
        
        .stats-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .stats-box h4 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.8rem;
        }
        
        footer {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }
        
        .date-picker {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <header class="my-4">
            <div class="logo-container">
                <img src="/images/knu.jpg" alt="KNUST Logo" class="logo">
                <h1 class="text-center mb-2 mt-3">KNUST ATTENDANCE SYSTEM</h1>
                <p class="text-center text-muted">College of Engineering</p>
            </div>
            
            <!-- Date and Time Display -->
            <div class="text-center mb-3 text-white">
                <div id="current-date" class="fw-bold fs-5"></div>
                <div id="current-time" class="fw-bold fs-3"></div>
            </div>
        </header>
        
        <main>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h2 class="card-title mb-0"><i class="bi bi-pencil-square me-2"></i>Sign In</h2>
                        </div>
                        <div class="card-body">
                            <form id="attendanceForm">
                                <div class="mb-3">
                                    <label for="employeeName" class="form-label">Your Name:</label>
                                    <input type="text" class="form-control form-control-lg" id="employeeName" required placeholder="Enter your full name">
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100" id="submitButton">
                                    <i class="bi bi-check-circle me-2"></i>Sign In
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Card -->
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title mb-0"><i class="bi bi-graph-up me-2"></i>Today's Stats</h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="stats-box">
                                        <h4 class="text-primary mb-0" id="total-attendance">0</h4>
                                        <small class="text-muted">Total Signed In</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stats-box">
                                        <h4 class="text-success mb-0" id="last-signin">--:--</h4>
                                        <small class="text-muted">Last Sign In</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="card-title mb-0"><i class="bi bi-list-check me-2"></i>Today's Attendance</h2>
                                <span class="badge bg-primary fs-6" id="record-count">0</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="controls mb-3">
                                <div class="btn-group w-100" role="group">
                                    <button id="refreshBtn" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-repeat me-2"></i>Refresh
                                    </button>
                                    <button id="exportBtn" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                                        <i class="bi bi-download me-2"></i>Export PDF
                                    </button>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover" id="recordsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Sign-in Time</th>
                                        </tr>
                                    </thead> 
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2">Loading attendance records...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="text-center mt-5 py-3">
            <p class="mb-0"><strong>&copy; 2025 KNUST Attendance System. Developed By CKL<strong></p>
        </footer>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2"></i>Attendance Recorded</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <p id="confirmationMessage" class="mt-3 fs-5"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    <p id="errorMessage" class="mt-3 fs-5"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-download me-2"></i>Export Records</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date:</label>
                        <input type="date" class="form-control" id="startDate">
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date:</label>
                        <input type="date" class="form-control" id="endDate">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmExport">Export</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Live clock functionality
        function updateClock() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
        }
        
        updateClock();
        setInterval(updateClock, 1000);

        // CSRF token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');       

        // Fetch attendance records from server
        async function fetchRecords() {
            const recordsTableBody = document.querySelector('#recordsTable tbody');
            recordsTableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading attendance records...</p></td></tr>';
            
            try {
                const response = await fetch('/attendance');
                if (!response.ok) throw new Error('Failed to fetch records');
                
                const records = await response.json();
                renderRecords(records);
                updateStats(records);
            } catch (error) {
                recordsTableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Error loading records. Please try again.</td></tr>';
            }
        }

        function renderRecords(records) {
            const recordsTableBody = document.querySelector('#recordsTable tbody');
            recordsTableBody.innerHTML = '';

            if (!records || records.length === 0) {
                recordsTableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4">No records found for today.</td></tr>';
                document.getElementById('record-count').textContent = '0';
                return;
            }

            records.forEach((rec, index) => {
                const row = recordsTableBody.insertRow();
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${rec.name}</td>
                    <td>${new Date(rec.sign_in_time).toLocaleString()}</td>
                `;
            });

            document.getElementById('record-count').textContent = records.length;
        }

        function updateStats(records) {
            document.getElementById('total-attendance').textContent = records.length;

            if (records.length > 0) {
                const lastSignIn = new Date(records[records.length - 1].sign_in_time);
                document.getElementById('last-signin').textContent = lastSignIn.toLocaleTimeString();
            } else {
                document.getElementById('last-signin').textContent = '--:--';
            }
        }

        // Attendance form submission
        document.getElementById('attendanceForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitButton = document.getElementById('submitButton');
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing In...';

            const name = document.getElementById('employeeName').value.trim();
            if (name === '') {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                return;
            }

            try {
                const response = await fetch('/attendance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ name })
                });

                const data = await response.json();

                if (response.ok) {
                    document.getElementById('confirmationMessage').textContent = `Thank you, ${data.name}! Your attendance has been recorded successfully.`;
                    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                    modal.show();
                    document.getElementById('employeeName').value = '';
                    fetchRecords();
                } else {
                    document.getElementById('errorMessage').textContent = data.error || 'Could not record attendance.';
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            } catch (error) {
                document.getElementById('errorMessage').textContent = 'Network error. Please check your connection and try again.';
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });

        // Refresh button
        document.getElementById('refreshBtn').addEventListener('click', fetchRecords);

        // Export PDF
        document.getElementById('confirmExport').addEventListener('click', async function () {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            if (!startDate || !endDate) {
                document.getElementById('errorMessage').textContent = 'Please select both start and end dates.';
                new bootstrap.Modal(document.getElementById('errorModal')).show();
                return;
            }

            try {
                // Open PDF export in a new tab (backend generates PDF)
                const exportUrl = `/attendance/export?start=${startDate}&end=${endDate}`;
                window.open(exportUrl, '_blank');

                // Close modal
                const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
                exportModal.hide();
            } catch (error) {
                document.getElementById('errorMessage').textContent = 'Failed to export records.';
                new bootstrap.Modal(document.getElementById('errorModal')).show();
            }
        });

        // Set default export dates
        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date();
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(today.getDate() - 7);

            document.getElementById('startDate').valueAsDate = sevenDaysAgo;
            document.getElementById('endDate').valueAsDate = today;

            fetchRecords();
        });
    </script>

</body>
</html>