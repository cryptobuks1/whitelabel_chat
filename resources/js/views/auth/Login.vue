<template>
    <div class="app flex-row align-items-center">
        <div class="container">
            <b-row class="justify-content-center">
                <b-col md="8">
                    <b-card-group>
                        <b-card no-body class="p-4">
                            <div v-if="userRegistered" class="alert alert-danger mb-0">{{$t('auth.loginMailMsg')}}
                            </div>
                            <b-card-body>
                                <b-form autocomplete="nope">
                                    <h1>{{$t('auth.login')}}</h1>
                                    <p class="text-muted">{{$t('auth.signIn')}}</p>
                                    <div>
                                        <b-input-group class="mb-3">
                                            <b-input-group-prepend>
                                                <b-input-group-text><i class="fa fa-btn fa-envelope"></i>
                                                </b-input-group-text>
                                            </b-input-group-prepend>
                                            <b-form-input type="email" class="form-control" name="email"
                                                          v-model="loginForm.email" v-validate="'required|email'"
                                                          :placeholder="$t('auth.email')" autocomplete="email"
                                                          :class="errors.has('email')?'br-danger':''"
                                                          @keydown.stop="loginOnEnterKey"/>
                                        </b-input-group>
                                        <div class="error-msg" v-if="errors.has('email')">
                                            {{ errors.first('email') }}
                                        </div>
                                    </div>
                                    <div>
                                        <b-input-group class="mb-3">
                                            <b-input-group-prepend>
                                                <b-input-group-text><i class="fa fa-lock lock-icon-size"></i>
                                                </b-input-group-text>
                                            </b-input-group-prepend>
                                            <b-form-input type="password" class="form-control"
                                                          :placeholder="$t('auth.password')"
                                                          autocomplete="current-password" v-model="loginForm.password"
                                                          name="password" v-validate="{ required: true, min: 5 }"
                                                          :class="errors.has('password')?'br-danger':''"
                                                          @keydown.stop="loginOnEnterKey"/>
                                        </b-input-group>
                                        <div class="error-msg" v-if="errors.has('password')">
                                            {{ errors.first('password') }}
                                        </div>
                                    </div>
                                    <div class="remember-me">
                                        <input type="checkbox" tabindex="3" id="remember_me" v-model="rememberMe">
                                        <label class="remember-me__text"
                                               for="remember_me">{{$t('auth.rememberMe')}}</label>
                                    </div>
                                    <b-row>
                                        <b-col cols="6">
                                            <b-button variant="primary" class="px-4" @click.prevent="onLogin()">
                                                {{$t('auth.login')}}
                                            </b-button>
                                        </b-col>
                                        <b-col cols="6" class="text-right" @click="goToForgotPassword()">
                                            <b-button variant="link" class="px-0">{{$t('auth.forgotPassword')}}
                                            </b-button>
                                        </b-col>
                                    </b-row>
                                </b-form>
                            </b-card-body>
                        </b-card>
<!--                        <b-card no-body class="text-white bg-primary py-5 d-md-down-none" style="width:44%">-->
<!--                            <b-card-body class="text-center">-->
<!--                                <div>-->
<!--                                    <h2>{{$t('auth.signup')}}</h2>-->
<!--                                    <p>{{$t('auth.sighupMsg')}}</p>-->
<!--                                    <b-button variant="primary" class="active mt-3" @click="goToRegister()">-->
<!--                                        {{$t('auth.registerNow')}}-->
<!--                                    </b-button>-->
<!--                                </div>-->
<!--                            </b-card-body>-->
<!--                        </b-card>-->
                    </b-card-group>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex'
    import * as types from './../../store/types'
    import { errorMsgToast, successMsgToast } from '../../utils/toasts'

    const loginForm = {
        email: '',
        password: '',
    }
    export default {
        name: 'Login',
        data: () => {
            return {
                loginForm: Object.assign({}, loginForm),
                rememberMe: false,
                userRegistered: false,
            }
        },
        methods: {
            ...mapActions({
                login: types.APP_LOGIN,
            }),
            onLogin () {
                this.$validator.validateAll()
                this.$validator.validate().then(valid => {
                    if (valid) {
                        localStorage.setItem('rememberMe', this.rememberMe)
                        if (this.rememberMe) {
                            localStorage.setItem('currentUser', btoa(JSON.stringify(this.loginForm)))
                        } else {
                            localStorage.removeItem('currentUser')
                        }
                        this.login(this.loginForm).then(() => {
                            this.$router.push('/conversations')
                        })
                    }
                })
            },
            goToRegister () {
                this.$router.push('/register')
            },
            goToForgotPassword () {
                this.$router.push('/forgot-password').then((response) => {
                    successMsgToast(response.message)
                })
            },
            loginOnEnterKey (event) {
                if (event.key === 'Enter' || event.which === 13) {
                    this.onLogin()
                    return false
                }
            },
            showAccountActiveMsg () {
                if (+this.$route.query.success === 1) {
                    successMsgToast(this.$route.query.msg)
                } else {
                    errorMsgToast(this.$route.query.msg)
                }
            },
        },
        created () {
            if (this.$route.query.userRegistered) {
                this.userRegistered = true
            }
            this.showAccountActiveMsg()
        },
        mounted () {
            const user = localStorage.getItem('currentUser')
            if (user) {
                const userObject = atob(user)
                this.loginForm = JSON.parse(userObject)
            }
            this.rememberMe = localStorage.getItem('rememberMe') === 'true'
        },

    }
</script>

<style lang="scss">
    .remember-me {
        margin-top: -10px;
        margin-bottom: 15px;

        input {
            vertical-align: sub;
        }

        &__text {
            margin-left: 2px;
            cursor: pointer;
            vertical-align: top;
        }
    }

    .lock-icon-size {
        font-size: 21px;
    }
</style>
