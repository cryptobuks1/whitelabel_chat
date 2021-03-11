<template>
    <div class="app flex-row align-items-center">
        <div class="container">
            <b-row class="justify-content-center">
                <b-col md="6" sm="8">
                    <b-card no-body class="mx-4">
                        <b-card-body class="p-4">
                            <b-form>
                                <h1>{{$t('auth.register')}}</h1>
                                <p class="text-muted">{{$t('auth.createYourAccount')}}</p>
                                <div>
                                    <b-input-group class="mb-3">
                                        <b-input-group-prepend>
                                            <b-input-group-text><i class="icon-user"></i></b-input-group-text>
                                        </b-input-group-prepend>
                                        <b-form-input type="text" class="form-control" name="name"
                                                      v-model="registerForm.name"
                                                      v-validate="{ required: true, min: 3 }"
                                                      autocomplete="name"
                                                      :placeholder="$t('auth.name')"
                                                      @keydown.stop="registerOnEnter"
                                                      :class="errors.has('name')?'br-danger':''"/>
                                    </b-input-group>
                                    <div class="error-msg" v-if="errors.has('name')">
                                        {{ errors.first('name') }}
                                    </div>
                                </div>

                                <div>
                                    <b-input-group class="mb-3">
                                        <b-input-group-prepend>
                                            <b-input-group-text>@</b-input-group-text>
                                        </b-input-group-prepend>
                                        <b-form-input type="email" class="form-control" name="email"
                                                      v-model="registerForm.email"
                                                      v-validate="'required|email'" :placeholder="$t('auth.email')"
                                                      autocomplete="email"
                                                      :class="errors.has('email')?'br-danger':''"
                                                      @keydown.stop="registerOnEnter"/>
                                    </b-input-group>
                                    <div class="error-msg" v-if="errors.has('email')">
                                        {{ errors.first('email') }}
                                    </div>
                                </div>

                                <div>
                                    <b-input-group class="mb-3">
                                        <b-input-group-prepend>
                                            <b-input-group-text><i class="icon-lock"></i></b-input-group-text>
                                        </b-input-group-prepend>
                                        <b-form-input type="password" class="form-control"
                                                      :placeholder="$t('auth.password')"
                                                      autocomplete="new-password" v-model="registerForm.password"
                                                      name="password" v-validate="{ required: true, min: 8 }"
                                                      ref="password"
                                                      :class="errors.has('password')?'br-danger':''"
                                                      @keydown.stop="registerOnEnter"/>
                                    </b-input-group>
                                    <div class="error-msg" v-if="errors.has('password')">
                                        {{ errors.first('password') }}
                                    </div>
                                </div>

                                <div>
                                    <b-input-group class="mb-3">
                                        <b-input-group-prepend>
                                            <b-input-group-text><i class="icon-lock"></i></b-input-group-text>
                                        </b-input-group-prepend>
                                        <b-form-input type="password" class="form-control"
                                                      :placeholder="$t('auth.rePassword')"
                                                      autocomplete="new-password"
                                                      name="repeat_password"
                                                      v-model="registerForm.password_confirmation"
                                                      v-validate="'required|confirmed:password'"
                                                      :class="errors.has('repeat_password')?'br-danger':''"
                                                      @keydown.stop="registerOnEnter"/>
                                    </b-input-group>
                                    <div class="error-msg" v-if="errors.has('repeat_password')">
                                        {{ errors.first('repeat_password') }}
                                    </div>
                                </div>

                                <b-button variant="primary" block @click="onRegister" :disabled="isProcessing">
                                    {{$t('auth.createAccount')}}
                                </b-button>
                                <a href="/login" class="text-center">{{$t('auth.alreadyMembership')}}</a>
                            </b-form>
                        </b-card-body>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex'
    import * as types from './../../store/types'

    const registerForm = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    }
    export default {
        name: 'Register',
        data: () => {
            return {
                registerForm: Object.assign({}, registerForm),
                isProcessing: false,
            }
        },
        methods: {
            ...mapActions({
                register: types.APP_REGISTER,
            }),
            onRegister () {
                this.$validator.validateAll()
                this.isProcessing = true
                this.$validator.validate().then(valid => {
                    if (valid) {
                        const protocol = location.protocol
                        const slashes = protocol.concat('//')
                        const host = slashes.concat(window.location.hostname)
                        const req = {
                            ...this.registerForm,
                            url: host,
                        }
                        this.register(req).then(() => {
                            this.isProcessing = false
                            this.$router.push({
                                path: '/login',
                                query: { userRegistered: true },
                            })
                        }, () => {
                            this.isProcessing = false
                        })
                    }
                })
            },
            registerOnEnter (event) {
                if (event.key === 'Enter' || event.which === 13) {
                    this.onRegister()
                    return false
                }
            },
        },
    }
</script>
