import Pusher from 'pusher-js' // this needs to be included
import Echo from 'laravel-echo'

export function laravelEcho () {
    let token = document.head.querySelector('meta[name="csrf-token"]')
    return new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        encrypted: true,
        authEndpoint: '/api/broadcasting/auth',
        auth: {
            headers: {
                Authorization: `Bearer ${localStorage.getItem(
                    'infyChatAppToken')}`,
                'X-CSRF-TOKEN': token.content,
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        namespace: 'App\\Events',
    })
}
