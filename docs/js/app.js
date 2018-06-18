if ('serviceWorker' in navigator) {
    navigator.serviceWorker
        .register('./serviceworker.js', {scope: './'})
        .then(function(registration) {
            console.log('Service Worker Registered')
        })
        .catch(function(err) {
            console.log('Service Worker Failed to Register', err)
        })
}

// Function to perform HTTP request
var get = function(url) {
    return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest()
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var result = xhr.responseText
                    result = JSON.parse(result)
                    resolve(result)
                } else {
                    reject(xhr)
                }
            }
        }

        xhr.open('GET', url, true)
        xhr.send()
    })
}

let deferredPrompt

window.addEventListener('beforeinstallprompt', e => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault()
    // Stash the event so it can be triggered later.
    deferredPrompt = e
    alert('fired')
})

Notification.requestPermission().then(function(result) {
    console.log(result)
    notifyMe()
})

function notifyMe() {
    // Let's check if the browser supports notifications
    if (!('Notification' in window)) {
        console.log('This browser does not support system notifications')
    }

    // Let's check whether notification permissions have already been granted
    else if (Notification.permission === 'granted') {
        // If it's okay let's create a notification
        var notification = new Notification('Hi there!')
    }

    // Otherwise, we need to ask the user for permission
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function(permission) {
            // If the user accepts, let's create a notification
            if (permission === 'granted') {
                var notification = new Notification('Hi there!')
            }
        })
    }

    // Finally, if the user has denied notifications and you
    // want to be respectful there is no need to bother them any more.
}
