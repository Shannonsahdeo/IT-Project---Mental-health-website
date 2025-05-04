document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Get the contact name and number
    const contactName = document.getElementById('contactName').value;
    const contactNumber = document.getElementById('contactNumber').value;

    // Create a new contact element to display on the page
    const contactList = document.getElementById('savedContactsList');

    // Create a new div element to hold the contact info
    const newContact = document.createElement('div');
    newContact.classList.add('contact-card');
    newContact.innerHTML = `
        <h3>${contactName}</h3>
        <p>${contactNumber}</p>
    `;

    // Append the new contact to the list
    contactList.appendChild(newContact);

    // Optionally, clear the form
    document.getElementById('contactForm').reset();

    // Optional: Store the contact in localStorage for persistence (e.g., page reload)
    saveContactToLocalStorage(contactName, contactNumber);
});

// Store contacts in localStorage (so they persist even if the page is refreshed)
function saveContactToLocalStorage(name, number) {
    let contacts = JSON.parse(localStorage.getItem('userContacts')) || [];

    // Add the new contact to the array
    contacts.push({ name, number });

    // Save the updated array back to localStorage
    localStorage.setItem('userContacts', JSON.stringify(contacts));

    // Optionally, reload the contacts to ensure they're displayed even after refresh
    loadSavedContacts();
}

// Load saved contacts from localStorage when the page loads
function loadSavedContacts() {
    const contacts = JSON.parse(localStorage.getItem('userContacts')) || [];
    const contactList = document.getElementById('savedContactsList');

    // Clear the current list before repopulating
    contactList.innerHTML = '';

    contacts.forEach(contact => {
        const contactDiv = document.createElement('div');
        contactDiv.classList.add('contact-card');
        contactDiv.innerHTML = `
            <h3>${contact.name}</h3>
            <p>${contact.number}</p>
        `;
        contactList.appendChild(contactDiv);
    });
}

// Call loadSavedContacts when the page is loaded
window.onload = loadSavedContacts;
