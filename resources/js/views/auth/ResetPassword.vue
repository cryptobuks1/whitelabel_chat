<template>
    <div class="app flex-row align-items-center">
        <div class="container">
            <b-row class="justify-content-center">
                <b-col md="6" sm="8">
                    <b-card no-body class="mx-4">
                        <b-card-body class="p-4">
                            <b-form>
                                <h1>{{$t('auth.resetPassword')}}</h1>
                                <p class="text-muted">{{$t('auth.resetPassHeader')}}</p>
                                <div>
                                    <b-input-group class="mb-3">
                                        <b-input-group-prepend>
                                            <b-input-group-text>@</b-input-group-text>
                                        </b-input-group-prepend>
                                        <b-form-input type="email" class="form-control" name="email"
                                                      v-validate="!validated?'required|email':''"
                                                      :placeholder="$t('auth.email')"
                                                      v-model="passwordResetForm.email"
                                                      autocomplete="email"
                                                      :class="errors.has('email')?'br-danger':''"
                                                      @keydown.stop="resetPasswordOnEnterPassword"
                                                      @keydown="validated=false"/>
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
                                                      autocomplete="new-password" v-model="passwordResetForm.password"
                                                      name="password"
                                                      v-validate="!validated?{ required: true, min: 8 }:''"
                                                      ref="password"
                                                      :class="errors.has('password')?'br-danger':''"
                                                      @keydown.stop="resetPasswordOnEnterPassword"
                                                      @keydown="validated=false"/>
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
                                                      v-model="passwordResetForm.password_confirmation"
                                                      v-validate="!validated?'required|confirmed:password':''"
                                                      :class="errors.has('repeat_password')?'br-danger':''"
                                                      @keydown.stop="resetPasswordOnEnterPassword"
                                                      @keydown="validated=false"/>
                                    </b-input-group>
                                    <div class="error-msg" v-if="errors.has('repeat_password')">
                                        {{ errors.first('repeat_password') }}
                                    </div>
                                </div>

                                <b-button variant="primary" block @click="onResetPassword()" :disabled="isProcessing">
                                    <i class="fa fa-btn mr-2"
                                       :class="isProcessing?'fa-spinner fa-spin': 'fa-refresh'"></i>
                                    {{$t('auth.reset')}}
                                </b-button>
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
    import * as types from '../../store/types'
    import { successMsgToast } from '../../utils/toasts'

    const passwordResetForm = {
        email: '',
        password: '',
        password_confirmation: '',
    }
    export default {
        name: 'ResetPassword',
        data: () => {
            return {
                passwordResetForm: Object.assign({}, passwordResetForm),
                isProcessing: false,
                validated: false,
            }
        },
        methods: {
            ...mapActions({
                resetPassword: types.APP_PASSWORD_RESET,
            }),
            onResetPassword () {
                this.$validator.validateAll()
                this.$validator.validate().then(valid => {
                    if (valid) {
                        const req = {
                            ...this.passwordResetForm,
                            token: this.$route.query.token,
                        }
                        this.validated = false
                        this.isProcessing = true
                        this.resetPassword(req).then((response) => {
                            this.isProcessing = false
                            this.validated = true
                            successMsgToast(response.message)
                            this.passwordResetForm = passwordResetForm
                            this.$router.push('/login')
                        }, () => {
                            this.isProcessing = false
                        })
                    }
                })
            },
            resetPasswordOnEnterPassword (event) {
                if (event.key === 'Enter' || event.which === 13) {
                    this.onResetPassword()
                    return false
                }
            },
        },
    }
</script>

<style scoped>

</style>
