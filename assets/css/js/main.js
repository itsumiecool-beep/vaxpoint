function subscribe() {
    const email = document.getElementById('newsletterEmail').value;

    if (email === '') {
        alert('Please enter your email');
    } else {
        alert('Thank you for subscribing!');
        document.getElementById('newsletterEmail').value = '';
    }
}
