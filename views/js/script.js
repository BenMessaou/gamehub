
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

const nav = document.querySelector('nav ul');
const toggle = document.createElement('button');
toggle.textContent = 'Menu';
toggle.style.display = 'none';
toggle.addEventListener('click', () => {
    nav.classList.toggle('show');
});
document.querySelector('header .container').appendChild(toggle);

if (window.innerWidth <= 768) {
    toggle.style.display = 'block';
}

function loadEvents() {
    fetch('get_approved_events.php')
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
        })
        .catch(error => console.error('Error loading events:', error));
}

// Load events when the page loads
document.addEventListener('DOMContentLoaded', loadEvents);

if (document.querySelector('.event-controls')) {
    const searchBar = document.getElementById('search-bar');
    const sortBar = document.getElementById('sort-bar');
    const createEventBtn = document.querySelector('.create-event-btn');
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
                const popA = a.querySelectorAll('p')[1].textContent.replace('Popularity: ', '');
                const popB = b.querySelectorAll('p')[1].textContent.replace('Popularity: ', '');
                const popOrder = { 'High': 3, 'Medium': 2, 'Low': 1 };
                return popOrder[popB] - popOrder[popA];
            } else if (sortBy === 'name') {
                const nameA = a.querySelector('h4').textContent.toLowerCase();
                const nameB = b.querySelector('h4').textContent.toLowerCase();
                return nameA.localeCompare(nameB);
            }
        });

        cardsArray.forEach(card => eventList.appendChild(card));
    });

    createEventBtn.addEventListener('click', function() {
        window.location.href = 'addevent.html';
    });

    document.querySelectorAll('.get-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const eventCard = this.closest('.event-card');
            const eventTitle = eventCard.querySelector('h4').textContent;
            const eventDate = eventCard.querySelector('p').textContent;
            const eventPopularity = eventCard.querySelectorAll('p')[1].textContent;
            alert(`Event Details:\n\n${eventTitle}\n${eventDate}\n${eventPopularity}\n\nMore details coming soon!`);
        });
    });

    document.querySelectorAll('.reserve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            alert('Reservation functionality would be implemented here.');
        });
    });
}
