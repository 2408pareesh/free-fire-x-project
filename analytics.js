// Google Analytics
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-XXXXXXXXX-X');

// WhatsApp Button Functionality
// Show the "Click to Chat!" message after 1 second
setTimeout(() => {
    document.getElementById("whatsappMessage").style.display = "block";
}, 1000);

// Hide the message after 3 seconds
setTimeout(() => {
    document.getElementById("whatsappMessage").style.display = "none";
}, 3000);

// Hide message and show an alert when the button is clicked
function hideMessage() {
    document.getElementById("whatsappMessage").style.display = "none";
}