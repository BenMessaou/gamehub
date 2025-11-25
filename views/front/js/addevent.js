
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});



document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);

    const form = document.getElementById('event-form');
    if (form && !window.location.pathname.includes('admin_update_event.html')) {
        if (urlParams.get('admin') === '1') {
            form.action = 'addevent.php?admin=1';
        } else {
            form.action = 'addevent.php';
        }
    }

    if (urlParams.get('success') === '1') {
        if (urlParams.get('admin') === '1') {
            alert('Event created and approved successfully!');
            window.location.href = 'admindashboard.html';
        } else {
            alert('Event created successfully and is pending approval!');
            window.location.href = 'events.php';
        }
    }

    if (window.location.pathname.includes('admin_update_event.html')) {
        const eventId = urlParams.get('id');
        if (eventId) {
            fetch(`get_event_by_id.php?id=${eventId}`)
                .then(response => response.json())
                .then(event => {
                    document.getElementById('idEvent').value = event.idEvent;
                    document.getElementById('title').value = event.title;
                    document.getElementById('description').value = event.description;
                    document.getElementById('eventType').value = event.eventType;
                    document.getElementById('platform').value = event.platform;
                    document.getElementById('location').value = event.location;
                    document.getElementById('startDate').value = event.startDate.replace(' ', 'T');
                    document.getElementById('endDate').value = event.endDate.replace(' ', 'T');
                    document.getElementById('ticketPrice').value = event.ticketPrice;
                    document.getElementById('availability').value = event.availability;
                    document.getElementById('status').value = event.status;
                    document.getElementById('prizePool').value = event.prizePool;
                    document.getElementById('imageURL').value = event.imageURL;
                })
                .catch(error => console.error('Error loading event:', error));
        }
    }
});

if (document.querySelector('.event-controls')) {
    const searchBar = document.getElementById('search-bar');
    const sortBar = document.getElementById('sort-bar');
    const createEventBtn = document.querySelector('.create-event-btn');

    function loadEvents() {
        fetch('get_events.php')
            .then(response => response.json())
            .then(events => {
                const eventList = document.getElementById('event-list');
                eventList.innerHTML = '';
                events.forEach(event => {
                    const eventCard = document.createElement('div');
                    eventCard.className = 'event-card';
                    eventCard.innerHTML = `
                        <h4>${event.title}</h4>
                        <p>Date: ${event.startDate}</p>
                        <p>Type: ${event.eventType}</p>
                        <p>Location: ${event.location}</p>
                        <p>Status: ${event.status}</p>
                        ${event.ticketPrice ? `<p>Ticket Price: $${event.ticketPrice}</p>` : ''}
                        ${event.prizePool ? `<p>Prize Pool: $${event.prizePool}</p>` : ''}
                        <button class="get-details-btn">Get Details</button>
                        <button class="reserve-btn">Reserve Spot</button>
                    `;
                    eventList.appendChild(eventCard);
                });
                attachEventListeners();
            })
            .catch(error => console.error('Error loading events:', error));
    }

    function attachEventListeners() {
        const eventCards = document.querySelectorAll('.event-card');

        searchBar.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            eventCards.forEach(card => {
                const title = card.querySelector('h4').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        sortBar.addEventListener('change', function() {
            const sortBy = this.value;
            const eventList = document.querySelector('.event-list');
            const cardsArray = Array.from(eventCards);

            cardsArray.sort((a, b) => {
                if (sortBy === 'date') {
                    const dateA = new Date(a.querySelector('p').textContent.replace('Date: ', ''));
                    const dateB = new Date(b.querySelector('p').textContent.replace('Date: ', ''));
                    return dateA - dateB;
                } else if (sortBy === 'popular') {
                    const statusA = a.querySelectorAll('p')[3].textContent.replace('Status: ', '');
                    const statusB = b.querySelectorAll('p')[3].textContent.replace('Status: ', '');
                    const statusOrder = { 'upcoming': 3, 'ongoing': 2, 'finished': 1, 'cancelled': 0 };
                    return statusOrder[statusB] - statusOrder[statusA];
                } else if (sortBy === 'name') {
                    const nameA = a.querySelector('h4').textContent.toLowerCase();
                    const nameB = b.querySelector('h4').textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                }
            });

            cardsArray.forEach(card => eventList.appendChild(card));
        });

        document.querySelectorAll('.get-details-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const eventCard = this.closest('.event-card');
                const eventTitle = eventCard.querySelector('h4').textContent;
                const eventDate = eventCard.querySelector('p').textContent;
                const eventType = eventCard.querySelectorAll('p')[1].textContent;
                const eventLocation = eventCard.querySelectorAll('p')[2].textContent;
                alert(`Event Details:\n\n${eventTitle}\n${eventDate}\n${eventType}\n${eventLocation}\n\nMore details coming soon!`);
            });
        });

        document.querySelectorAll('.reserve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Reservation functionality would be implemented here.');
            });
        });
    }

    loadEvents();
}
