<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pitch Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4">Pitch Booking</h1>

    <div class="card p-4 mb-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="pitch" class="form-label">Pitch ID</label>
                <input type="number" id="pitch" class="form-control" value="1">
            </div>

            <div class="col-md-4">
                <label for="date" class="form-label">Date</label>
                <input type="date" id="date" class="form-control">
            </div>

            <div class="col-md-4">
                <label for="duration" class="form-label">Duration</label>
                <select id="duration" class="form-select">
                    <option value="60">60 minutes</option>
                    <option value="90" selected>90 minutes</option>
                </select>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button id="loadBtn" onclick="loadSlots()" class="btn btn-primary">
                Check Available Slots
            </button>
        </div>
    </div>

    <h2>Available Slots</h2>
    <ul id="slots" class="list-group"></ul>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="modalHeader" class="modal-header">
                <h5 id="modalTitle" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalHeader = document.getElementById('modalHeader');
    const loadBtn = document.getElementById('loadBtn');

    function showModal(type, title, message) {
        modalTitle.textContent = title;
        modalBody.innerHTML = message;

        // Change modal header color based on type
        if (type === 'success') {
            modalHeader.className = "modal-header bg-success text-white";
        } else {
            modalHeader.className = "modal-header bg-danger text-white";
        }

        modal.show();
    }

    async function loadSlots() {
        const pitchId = document.getElementById('pitch').value;
        const date = document.getElementById('date').value;
        const duration = document.getElementById('duration').value;

        if (!date || !pitchId || !duration) {
            showModal("error", "Validation Error", "Please fill the data.");
            return;
        }

        try {
            loadBtn.disabled = true;
            loadBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Loading...`;

            const res = await fetch(`/api/pitches/${pitchId}/slots?date=${date}&duration=${duration}`);
            const response = await res.json();
             if(!response.status){
                 showModal('error','Error',response.message);
                 return;
             }
            const list = document.getElementById('slots');
            list.innerHTML = '';
            const slots=response.data.slots;
            if (!Array.isArray(slots) || slots.length === 0) {
                list.innerHTML = '<li class="list-group-item text-muted">No available slots</li>';
            } else {
                slots.forEach(slot => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.textContent = `${slot.start_time} - ${slot.end_time}`;

                    const btn = document.createElement('button');
                    btn.className = 'btn btn-sm btn-success';
                    btn.textContent = 'Book';
                    btn.onclick = async () => {
                        try {
                            const bookingRes = await fetch('/api/bookings', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    pitch_id: pitchId,
                                    date: date,
                                    start_time: slot.start_time,
                                    end_time: slot.end_time
                                })
                            });

                            const data = await bookingRes.json();

                            if (bookingRes.ok) {
                                showModal("success", "Booking Confirmed",
                                    `Slot booked successfully:<br><b>${slot.start_time} - ${slot.end_time}</b>`);
                                    loadSlots(); // refresh
                                    } else {
                                        showModal("error", "Booking Failed", data.error || JSON.stringify(data));
                                    }

                                    } catch (err) {
                                        showModal("error", "Error", err.message);
                                    }
                                    };

                                    li.appendChild(btn);
                                    list.appendChild(li);
                                    });
                                    }

                                    } catch (error) {
                                        showModal("error", "Error Loading Slots", error.message);
                                    } finally {
                                        loadBtn.disabled = false;
                                        loadBtn.textContent = "Check Available Slots";
                                    }
                                    }
</script>
</body>
</html>
