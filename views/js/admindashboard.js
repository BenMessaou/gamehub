

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
        
    });
});



const statCards = document.querySelectorAll('.stat-card');
statCards.forEach(card => {
    card.addEventListener('click', () => {
        console.log('Stat card clicked:', card.querySelector('h3').textContent);
    });
});

document.addEventListener('DOMContentLoaded', loadPendingEvents);

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('approved') === '1') {
    alert('Event approved successfully!');
    window.location.href = 'admindashboard.html';
}
if (urlParams.get('rejected') === '1') {
    alert('Event rejected successfully!');
    window.location.href = 'admindashboard.html';
}
if (urlParams.get('success') === '1') {
    alert('Operation completed successfully!');
    window.location.href = 'admindashboard.html';
}

function loadPendingEvents() {
    fetch('get_pending_events.php')
        .then(response => response.json())
        .then(events => {
            const pendingList = document.getElementById('pending-events-list');
            pendingList.innerHTML = '';
            if (events.length === 0) {
                pendingList.innerHTML = '<p>No pending events.</p>';
                return;
            }
            events.forEach(event => {
                const eventItem = document.createElement('div');
                eventItem.className = 'pending-event-item';
                eventItem.innerHTML = `
                    <h4>${event.title}</h4>
                    <p>Type: ${event.eventType}</p>
                    <p>Date: ${event.startDate}</p>
                    <form action="approve_event.php" method="post" style="display:inline;">
                        <input type="hidden" name="idEvent" value="${event.idEvent}">
                        <button type="submit" name="approve" class="approve-btn">Approve</button>
                    </form>
                    <form action="approve_event.php" method="post" style="display:inline;">
                        <input type="hidden" name="idEvent" value="${event.idEvent}">
                        <button type="submit" name="reject" class="reject-btn">Reject</button>
                    </form>
                `;
                pendingList.appendChild(eventItem);
            });
        })
        .catch(error => console.error('Error loading pending events:', error));
}

function loadManageEvents() {
    fetch('get_events.php')
        .then(response => response.json())
        .then(events => {
            const tbody = document.getElementById('manage-events-tbody');
            tbody.innerHTML = '';
            if (events.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">No events found.</td></tr>';
                return;
            }
            events.forEach(event => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${event.idEvent}</td>
                    <td>${event.title}</td>
                    <td>${event.eventType}</td>
                    <td>${event.startDate}</td>
                    <td>${event.status}</td>
                    <td>
                        <button onclick="confirmDelete(${event.idEvent})" class="reject-btn">Delete</button>
                        <a href="admin_update_event.html?id=${event.idEvent}" class="approve-btn" style="display:inline-block; padding:5px 10px; margin-left:5px;">Update</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading events:', error));
}

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this event?')) {
        window.location.href = `delete_event.php?id=${id}`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadPendingEvents();
    loadManageEvents();
});
