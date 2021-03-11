<template>
    <div class="app flex-row align-items-center">
        <div class="container">
            <b-row class="justify-content-center">
                <b-col md="6" sm="8">
                    <b-card no-body class="mx-4">
                        <b-card-body class="p-4">
                            <b-form autocomplete="nope">
                                <h1>{{$t('auth.forgotYourPassword')}}</h1>
                                <p class="text-muted">{{$t('auth.emailToResetPassword')}}</p>
                                <div>
                                    <b-input-group class="mb-3">
                                        <b-input-group-prepend>
                                            <b-input-group-text><i class="fa fa-envelope"></i></b-input-group-text>
                                        </b-input-group-prepend>
                                        <b-form-input type="text" class="form-control" name="email"
                                                      v-model="forgotPasswordForm.email"
                                                      v-validate="!validated?'required|email':''" placeholder="Email"
                                                      autocomplete="email"
                                                      :class="errors.has('email')?'br-danger':''"
                                                      @keydown="validated=false"
                                                      @keydown.stop="sendMailForgotPasswordOnPressEnter"/>
                                    </b-input-group>
                                    <div class="error-msg" v-if="errors.has('email')">
                                        {{ errors.first('email') }}
                                    </div>
                                </div>

                                <b-button variant="primary" @click.prevent="onSendMailForgotPassword"
                                          class="float-right" :disabled="isProcessing">
                                    <i class="fa fa-btn mr-2"
                                       :class="isProcessing?'fa-spinner fa-spin':'fa-envelope'"></i>
                                    {{$t('auth.resetPassword')}}
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
    import * as types from '../../store/types'
    import { mapActions } from 'vuex'
    import { successMsgToast } from '../../utils/toasts'

    export default {
        name: 'ForgotPassword',
        data: () => {
            return {
                forgotPasswordForm: {
                    email: '',
                },
                validated: false,
                isProcessing: false,
            }
        },
        methods: {
            ...mapActions({
                sendMailForgotPassword: types.APP_FORGOT_PASSWORD,
            }),
            onSendMailForgotPassword () {
                this.$validator.validateAll()
                this.$validator.validate().then(valid => {
                    if (valid) {
                        this.validated = false
                        this.isProcessing = true
                        const protocol = location.protocol
                        const slashes = protocol.concat('//')
                        const host = slashes.concat(window.location.hostname)
                        const resetPasswordRoute = `${host}/password/reset`
                        const req = {
                            email: this.forgotPasswordForm.email,
                            url: resetPasswordRoute,
                        }
                        this.sendMailForgotPassword(req).then((response) => {
                            this.forgotPasswordForm = {
                                email: '',
                            }
                            this.validated = true
                            this.isProcessing = false
                            successMsgToast(response.message)
                        }, () => {
                            this.isProcessing = false
                        })
                    }
                })
            },
            sendMailForgotPasswordOnPressEnter (event) {
                if (event.key === 'Enter' || event.which === 13) {
                    this.onSendMailForgotPassword()
                    return false
                }
            },
        },
    }
</script>

<style scoped>

</style>
