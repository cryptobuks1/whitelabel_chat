<template>
    <main class="main">
        <div class="page-container">
            <div class="profile bg-white">
                <h1>{{$t('profile.editProfile')}}</h1>
                <form enctype="multipart/form-data">
                    <div class="profile__inner m-auto">
                        <div class="profile__img-wrapper">
                            <img :src="profileForm.photo_url?profileForm.photo_url:'assets/images/avatar.png'" alt=""
                                 id="upload-photo-img">
                        </div>
                        <div class="text-center mt-2">
                            <label class="btn profile__update-label">{{$t('profile.uploadPhoto')}}
                                <input id="upload-photo" class="d-none" name="photo" type="file"
                                       @change="uploadImage($event)">
                            </label>
                        </div>
                        <div class="alert alert-danger w-100 d-none"
                             id="editProfileValidationErrorsBox"></div>
                        <div class="form-group bordered-input w-100">
                            <label for="user-name" class="mb-0">{{$t('profile.name')}}<span class="text-danger">*</span></label>
                            <input type="text" class="profile__name form-control pl-0" id="user-name"
                                   aria-describedby="User name" :placeholder="$t('profile.name')" name="name"
                                   v-validate="{ required: true, min: 3 }" autocomplete="name"
                                   v-model="profileForm.name"
                                   :class="errors.has('name')?'br-bottom-danger':''">
                            <div class="error-msg-no-mt" v-if="errors.has('name')">
                                {{ errors.first('name') }}
                            </div>
                        </div>
                        <div class="form-group bordered-input w-100">
                            <label for="email" class="mb-0">{{$t('profile.email')}}<span
                                class="text-danger">*</span></label>
                            <input type="email" class="profile__email form-control pl-0" id="email"
                                   aria-describedby="User email" :placeholder="$t('profile.email')" name="email"
                                   v-validate="'required|email'" v-model="profileForm.email"
                                   :class="errors.has('email')?'br-bottom-danger':''">
                            <div class="error-msg-no-mt" v-if="errors.has('email')">
                                {{ errors.first('email') }}
                            </div>
                        </div>
                        <div class="form-group bordered-input w-100">
                            <label for="about">{{$t('profile.about')}}</label>
                            <textarea class="profile__about form-control pl-0" id="about" rows="3"
                                      v-model="profileForm.about" name="about"></textarea>
                        </div>
                        <div class="w-100">
                            <div class="form-group bordered-input">
                                <label for="phone" class="mb-0">{{$t('profile.phone')}}</label>
                                <input type="tel" class="profile__phone form-control pl-0" id="phone"
                                       aria-describedby="User phone no" :placeholder="$t('profile.phoneNumber')"
                                       name="phone" v-model="profileForm.phone">
                            </div>
                        </div>
                        <div class="ml-auto">
                            <button class="btn btn-primary mr-2" @click.prevent="onUpdateProfile()">
                                {{$t('profile.save')}}
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnCancel" @click.prevent="goToBack()">
                                {{$t('profile.cancel')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    import * as types from '../../store/types'
    import { successMsgToast } from '../../utils/toasts'

    const profileForm = {
        name: '',
        email: '',
        about: '',
        phone: '',
        photo_url: '',
    }

    export default {
        data: () => {
            return {
                profileForm: Object.assign({}, profileForm),
                file: null,
            }
        },
        computed: {
            ...mapGetters({
                loggedUser: types.LOGGED_USER,
            }),
        },
        watch: {
            loggedUser (loggedUser) {
                this.profileForm = Object.assign(this.profileForm, loggedUser)
            },
        },
        methods: {
            ...mapActions({
                updateProfile: types.UPDATE_LOGGED_USER_PROFILE,
                getLoggedUser: types.GET_LOGGED_USER_PROFILE,
            }),
            uploadImage (fileInput) {
                this.file = fileInput.target.files[0]
                const reader = new FileReader()
                reader.onload = (e) => {
                    this.profileForm.photo_url = e.target.result
                }
                reader.readAsDataURL(fileInput.target.files[0])
            },
            onUpdateProfile () {
                this.$validator.validate().then(valid => {
                    if (valid) {
                        let data = new FormData()
                        if (this.file) {
                            data.append('photo', this.file)
                        }
                        data.append('name', this.profileForm.name)
                        data.append('email', this.profileForm.email)
                        data.append('about', this.profileForm.about)
                        if (this.profileForm.phone) {
                            data.append('phone', this.profileForm.phone)
                        }
                        this.updateProfile(data).then((response) => {
                            this.getLoggedUser()
                            successMsgToast(response.message)
                        })
                    }
                })
            },
            goToBack () {
                this.$router.back()
            },
        },
        mounted () {
            if (this.loggedUser) {
                this.profileForm = Object.assign(this.profileForm, this.loggedUser)
            }
        },
    }
</script>

<style scoped>

</style>
