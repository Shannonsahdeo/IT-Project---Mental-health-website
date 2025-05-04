// JavaScript for Quote Animation
document.addEventListener("DOMContentLoaded", function() {
    const quotes = document.querySelectorAll(".quote");
    let index = 0;

    function showQuote() {
        if (index > 0) {
            quotes[index - 1].style.display = 'none'; // Hide the previous quote
        }
        if (index < quotes.length) {
            quotes[index].style.display = 'block'; // Show the current quote
        }
        index++;
        if (index >= quotes.length) {
            index = 0; // Reset index to loop through quotes
        }
    }

    showQuote(); // Show the first quote immediately
    setInterval(showQuote, 4000); // Change quote every 4 seconds
});
