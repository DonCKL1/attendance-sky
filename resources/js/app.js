import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    initAttendanceSystem();
});

function initAttendanceSystem() {
    // --- Live Clock ---
    function updateClock() {
        const now = new Date();
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        
        const currentDateElement = document.getElementById('current-date');
        const currentTimeElement = document.getElementById('current-time');
        
        if (currentDateElement && currentTimeElement) {
            currentDateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
            currentTimeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // --- Default Export Dates ---
    const today = new Date();
    const sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(today.getDate() - 7);
    
    const startDateElement = document.getElementById('startDate');
    const endDateElement = document.getElementById('endDate');
    if (startDateElement && endDateElement) {
        startDateElement.valueAsDate = sevenDaysAgo;
        endDateElement.valueAsDate = today;
    }

    // --- CSRF Token ---
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // --- Fetch Attendance Records ---
    async function fetchRecords() {
        const recordsTableBody = document.querySelector('#recordsTable tbody');
        if (!recordsTableBody) return;

        recordsTableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4">' +
            '<div class="spinner-border text-primary" role="status">' +
            '<span class="visually-hidden">Loading...</span></div>' +
            '<p class="mt-2">Loading attendance records...</p></td></tr>';

        try {
            const res = await fetch('/attendance');
            if (!res.ok) throw new Error('Network response was not ok');

            const records = await res.json();
            renderRecords(records);
            updateStats(records);
        } catch (error) {
            console.error(error);
            recordsTableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">' +
                'Error loading records. Please try again.</td></tr>';
        }
    }

    function renderRecords(records) {
        const recordsTableBody = document.querySelector('#recordsTable tbody');
        const recordCountElement = document.getElementById('record-count');

        if (!recordsTableBody) return;
        recordsTableBody.innerHTML = '';

        if (!records || records.length === 0) {
            recordsTableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4">No records found for today.</td></tr>';
            if (recordCountElement) recordCountElement.textContent = '0';
            return;
        }

        records.forEach(function (rec, index) {
            const row = recordsTableBody.insertRow();
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${rec.name}</td>
                <td>${new Date(rec.sign_in_time).toLocaleString()}</td>
            `;
        });

        if (recordCountElement) recordCountElement.textContent = records.length;
    }

    function updateStats(records) {
        const totalAttendanceElement = document.getElementById('total-attendance');
        const lastSigninElement = document.getElementById('last-signin');

        if (totalAttendanceElement) totalAttendanceElement.textContent = records.length;

        if (lastSigninElement) {
            if (records.length > 0) {
                const lastSignIn = new Date(records[records.length - 1].sign_in_time);
                lastSigninElement.textContent = lastSignIn.toLocaleTimeString();
            } else {
                lastSigninElement.textContent = '--:--';
            }
        }
    }

    // --- Submit Attendance ---
    const attendanceForm = document.getElementById('attendanceForm');
    if (attendanceForm) {
        attendanceForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const employeeNameInput = document.getElementById('employeeName');
            if (!employeeNameInput) return;

            const name = employeeNameInput.value.trim();
            if (name === '') return;

            const submitButton = document.getElementById('submitButton');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...';

            try {
                const res = await fetch('/attendance', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ name })
                });

                const data = await res.json();

                if (res.ok) {
                    const confirmationMessage = document.getElementById('confirmationMessage');
                    if (confirmationMessage) confirmationMessage.textContent = `Thank you, ${data.name}! Your attendance has been recorded successfully.`;

                    new bootstrap.Modal(document.getElementById('confirmationModal')).show();

                    employeeNameInput.value = '';
                    fetchRecords();
                } else {
                    const errorMessage = document.getElementById('errorMessage');
                    if (errorMessage) errorMessage.textContent = 'Error: ' + (data.error || 'Could not record attendance.');
                    new bootstrap.Modal(document.getElementById('errorModal')).show();
                }
            } catch (error) {
                console.error(error);
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) errorMessage.textContent = 'Network error. Please try again.';
                new bootstrap.Modal(document.getElementById('errorModal')).show();
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }

    // --- Refresh Button ---
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) refreshBtn.addEventListener('click', fetchRecords);

    // --- Export Attendance PDF ---
    const exportPdfBtn = document.getElementById('confirmExport');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function() {
            const startDate = startDateElement.value;
            const endDate = endDateElement.value;

            if (!startDate || !endDate) {
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) errorMessage.textContent = 'Please select both start and end dates.';
                new bootstrap.Modal(document.getElementById('errorModal')).show();
                return;
            }

            if (startDate > endDate) {
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) errorMessage.textContent = 'Start date cannot be after end date.';
                new bootstrap.Modal(document.getElementById('errorModal')).show();
                return;
            }

            // Open PDF in a new tab
            window.open(`/export/attendance/pdf?start_date=${startDate}&end_date=${endDate}`, '_blank');

            const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
            if (exportModal) exportModal.hide();
        });
    }

    // --- Load records on page load ---
    fetchRecords();
}
