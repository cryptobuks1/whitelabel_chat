const DefaultLayout = () => import('../layouts/DefaultLayout')

const Chat = () => import('../views/chat')
const Profile = () => import('../views/profile')
const Users = () => import('../views/users')
const Roles = () => import('../views/roles')

// auth
const Login = () => import('../views/auth/Login')
const Register = () => import('../views/auth/Register')
const ForgotPassword = () => import('../views/auth/ForgotPassword')
const ResetPassword = () => import('../views/auth/ResetPassword')
const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: {
            title: 'Login | InfyChat',
        },
        beforeEnter (to, from, next) {
            if (!localStorage.getItem('infyChatAppToken')) {
                next()
            } else {
                next('/conversations')
            }
        },
    },
    {
        path: '/register',
        name: 'Register',
        meta: {
            title: 'Register | InfyChat',
        },
        component: Register,
        beforeEnter (to, from, next) {
            if (!localStorage.getItem('infyChatAppToken')) {
                next()
            } else {
                next('/conversations')
            }
        },
    },
    {
        path: '/forgot-password',
        name: 'ForgotPassword',
        meta: {
            title: 'Forgot Password | InfyChat',
        },
        component: ForgotPassword,
        beforeEnter (to, from, next) {
            if (!localStorage.getItem('infyChatAppToken')) {
                next()
            } else {
                next('/conversations')
            }
        },
    },
    {
        path: '/password/reset',
        name: 'ResetPassword',
        meta: {
            title: 'Reset Password | InfyChat',
        },
        component: ResetPassword,
        beforeEnter (to, from, next) {
            if (!localStorage.getItem('infyChatAppToken')) {
                next()
            } else {
                next('/conversations')
            }
        },
    },
    {
        path: '',
        redirect: '/conversations',
        name: 'App',
        component: DefaultLayout,
        beforeEnter (to, from, next) {
            if (localStorage.getItem('infyChatAppToken')) {
                next()
            } else {
                next('/login')
            }
        },
        children: [
            {
                path: 'conversations',
                name: 'Conversations',
                meta: {
                    title: 'Conversations | InfyChat',
                },
                component: Chat,
            },
            {
                path: '/profile',
                name: 'Profile',
                meta: {
                    title: 'Edit Profile | InfyChat',
                },
                component: Profile,
            },
            {
                path: '/users',
                name: 'Users',
                meta: {
                    title: 'Users | InfyChat',
                },
                component: Users,
            },
            {
                path: '/roles',
                name: 'Roles',
                meta: {
                    title: 'Roles | InfyChat',
                },
                component: Roles,
            },
        ],
    },
]

export default routes
