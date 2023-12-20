function updateRemainingTime() {
    // Fetch the remaining time from the server
    fetch('get_remaining_time.php') // Replace 'get_remaining_time.php' with the actual server endpoint
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch remaining time');
            }
            return response.json();
        })
        .then(data => {
            const userKey = data.userKey;
            document.getElementById('countdown').innerHTML = data.remainingTime;
        })
        .catch(error => {
            console.error(error);
        });
}

// Auto-update remaining time every 1 minute (adjust as needed)
setInterval(updateRemainingTime, 60000);
