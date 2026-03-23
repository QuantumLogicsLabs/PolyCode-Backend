/**
 * JavaScript Async/Await
 * This tutorial covers asynchronous programming using the modern syntax.
 */

// Simulated asynchronous API call
const fetchUserData = (id) => {
    return new Promise((resolve, reject) => {
        console.log(`Fetching user ${id}...`);
        setTimeout(() => {
            if (id > 0) {
                resolve({ id, name: 'John Doe', status: 'Active' });
            } else {
                reject('Invalid user ID');
            }
        }, 1500);
    });
};

// Async function using await
async function main() {
    try {
        // Successful call
        const user = await fetchUserData(1);
        console.log('User Received:', user);

        // Chaining (wait for first to finish)
        console.log('Wait 1 second...');
        await new Promise(r => setTimeout(r, 1000));
        
        const user2 = await fetchUserData(2);
        console.log('User 2 Received:', user2);

        // Error handling
        await fetchUserData(-1);
    } catch (error) {
        console.error('Error occurred:', error);
    }
}

main();