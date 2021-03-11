<template>
    <main class="main">
        <div class="page-container">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="pull-left page__heading">
                                    {{$t('users.users')}}
                                </div>
                                <button type="button" class="pull-right btn btn-primary"
                                        @click="openAddEditUserModal()">{{$t('users.newUser')}}
                                </button>
                            </div>
                            <div class="card-body user-list table-responsive">
                                <table id="user-table" class="table table-striped table-bordered nowrap" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th class="dt-profile-td">{{$t('users.profilePhoto')}}</th>
                                        <th>{{$t('users.name')}}</th>
                                        <th>{{$t('users.email')}}</th>
                                        <th class="dt-phone-th">{{$t('users.phone')}}</th>
                                        <th class="dt-role-th">{{$t('users.roles')}}</th>
                                        <th class="dt-status-th">{{$t('users.status')}}</th>
                                        <th class="dt-action-btn">{{$t('users.action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- user modal -->
        <b-modal :title="isEditUser?'Edit User':'New User'" centered v-model="showAddEditUserModal" size="lg"
                 hide-footer default="false">
            <div class="add-edit-user">
                <form autocomplete="off">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="user-name" class="mb-0">{{$t('users.name')}}<span
                                    class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="user-name"
                                       aria-describedby="User name" :placeholder="$t('users.name')" name="name"
                                       v-validate="{ required: true, min: 3 }" autocomplete="name"
                                       v-model="userForm.name"
                                       :class="errors.has('name')?'br-danger':''">
                                <div class="error-msg-no-mt" v-if="errors.has('name')">
                                    {{ errors.first('name') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">{{$t('users.email')}}<span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email"
                                       aria-describedby="User email" :placeholder="$t('users.email')" name="email"
                                       v-validate="'required|email'" v-model="userForm.email"
                                       :class="errors.has('email')?'br-danger':''">
                                <div class="error-msg-no-mt" v-if="errors.has('email')">
                                    {{ errors.first('email') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">{{$t('users.phone')}}</label>
                                <input type="tel" class="form-control" id="phone"
                                       aria-describedby="User phone no" :placeholder="$t('users.phoneNumber')"
                                       name="phone"
                                       v-model="userForm.phone">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="isActive">{{$t('users.status')}}</label>
                                        <b-form-select v-model="userForm.is_active" :options="userStatusOptions"
                                                       id="isActive"></b-form-select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="role">{{$t('users.role')}}</label>
                                        <b-form-select v-model="userForm.role_id" :options="roleOptions"
                                                       id="role"></b-form-select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="password">{{$t('users.password')}}<span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" :placeholder="$t('users.password')"
                                               autocomplete="new-password" v-model="userForm.password"
                                               name="password" v-validate="{ required: true, min: 8 }"
                                               ref="password" id="password"
                                               :class="errors.has('password')?'br-danger':''"/>
                                        <div class="error-msg-no-mt" v-if="errors.has('password')">
                                            {{ errors.first('password') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="cPassword">{{$t('users.reTypePassword')}}<span
                                            class="text-danger">*</span></label>
                                        <input type="password" class="form-control"
                                               :placeholder="$t('users.reTypePassword')"
                                               autocomplete="new-password"
                                               name="repeat_password"
                                               v-model="userForm.password_confirmation"
                                               v-validate="'required|confirmed:password'"
                                               id="cPassword"
                                               :class="errors.has('repeat_password')?'br-danger':''"/>
                                        <div class="error-msg-no-mt" v-if="errors.has('repeat_password')">
                                            {{ errors.first('repeat_password') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="profile__inner m-auto">
                                <div class="profile__img-wrapper">
                                    <img :src="userForm.photo_url?userForm.photo_url:'assets/images/avatar.png'" alt=""
                                         id="upload-photo-img">
                                </div>
                                <div class="text-center mt-2">
                                    <label class="btn profile__update-label">{{$t('users.uploadPhoto')}}
                                        <input id="upload-photo" class="d-none" name="photo" type="file"
                                               @change="uploadImage($event)">
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="about">{{$t('users.bio')}}</label>
                                <textarea class="form-control" id="about" rows="4"
                                          v-model="userForm.about" name="about"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <b-button variant="primary" class="btn btn-primary mr-2" @click.stop="save()">
                            {{$t('users.save')}}
                        </b-button>
                        <b-button variant="secondary" class="d-inline mt-0"
                                  @click.prevent="closeModal">{{$t('users.cancel')}}
                        </b-button>
                    </div>
                </form>
            </div>
        </b-modal>
        <!--// user modal -->
    </main>
</template>

<script>
    import * as types from '../../store/types'
    import { mapActions, mapGetters } from 'vuex'
    import { successMsgToast } from '../../utils/toasts'
    import { customSearchFiledDataTable } from '../../utils/custom-search-field-datatable'

    const userForm = {
        name: '',
        email: '',
        phone: '',
        about: '',
        photo: '',
        is_active: 1,
        role_id: 1,
        photo_url: '',
        password: '',
        password_confirmation: '',
    }
    export default {
        name: 'Users',
        data: () => {
            return {
                dataTable: null,
                showAddEditUserModal: false,
                userForm: Object.assign({}, userForm),
                userStatusOptions: [
                    { value: 1, text: 'Active' },
                    { value: 0, text: 'InActive' },
                ],
                roleOptions: [],
                file: null,
                isEditUser: false,
            }
        },
        computed: {
            ...mapGetters({
                users: types.USER_LIST_WITH_ROLE,
            }),
        },
        methods: {
            ...mapActions({
                getUsers: types.GET_USER_LIST_WITH_ROLE,
                addUser: types.ADD_USER,
                editUser: types.EDIT_USER,
                deleteUser: types.DELETE_USER,
                getRoles: types.GET_ROLE_LIST,
                updateUserStatus: types.UPDATE_USER_STATUS,
            }),
            save () {
                let data = new FormData()
                if (this.file) {
                    data.append('photo', this.file)
                }
                if (this.userForm.name) {
                    data.append('name', this.userForm.name)
                }
                if (this.userForm.email) {
                    data.append('email', this.userForm.email)
                }
                data.append('about', this.userForm.about)
                if (this.userForm.phone) {
                    data.append('phone', this.userForm.phone)
                }
                if (this.userForm.is_active || this.userForm.is_active === 0) {
                    data.append('is_active', this.userForm.is_active)
                }
                if (this.userForm.role_id) {
                    data.append('role_id', this.userForm.role_id)
                }
                if (this.userForm.password) {
                    data.append('password', this.userForm.password)
                }
                if (this.userForm.password_confirmation) {
                    data.append('password_confirmation', this.userForm.password_confirmation)
                }
                this.$validator.validateAll()
                this.$validator.validate().then(valid => {
                    if (valid) {
                        if (!this.isEditUser) {
                            this.addUser(data).then(() => {
                                this.showAddEditUserModal = false
                                this.drawUserDataTable(this.users)
                                successMsgToast('User added successfully')
                            })
                        } else {
                            data.append('id', this.userForm.id)
                            this.editUser(data).then(() => {
                                this.showAddEditUserModal = false
                                this.drawUserDataTable(this.users)
                                successMsgToast('User updated successfully')
                            })
                        }
                    }
                })
            },
            closeModal () {
                this.showAddEditUserModal = false
            },
            uploadImage (fileInput) {
                this.file = fileInput.target.files[0]
                const reader = new FileReader()
                reader.onload = (e) => {
                    this.userForm.photo_url = e.target.result
                }
                reader.readAsDataURL(fileInput.target.files[0])
            },
            drawUserDataTable (users) {
                this.dataTable.clear().draw()
                users.forEach(user => {
                    this.dataTable.row.add([
                        '<img src="' + user.photo_url + '" width="40" height="40" class="border-radius-50">',
                        user.name,
                        user.email,
                        user.phone ? user.phone : '---',
                        user.role_name,
                        '<label data-id="' + user.id + '" class="switch switch-label switch-outline-primary-alt">' +
                        '<input data-id="' + user.id + '" name="is_active" data-id="' + user.id +
                        '" class="switch-input is-active" type="checkbox" value="1" ' + this.checkUserStatus(user) +
                        '">' +
                        '<span data-id="' + user.id +
                        '" class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>' +
                        '</label>',
                        '<span data-id="' + user.id +
                        '" class="index__btn btn btn-ghost-success btn-sm edit-btn edit-button"\n' +
                        'title="Edit">\n' +
                        '<i data-id="' + user.id + '" class="cui-pencil action-icon"></i>\n' +
                        '</span>\n' +
                        '<span data-id="' + user.id + '" class="index__btn btn btn-ghost-danger btn-sm delete-btn"\n' +
                        'title="Delete">\n' +
                        '<i data-id="' + user.id + '" class="cui-trash action-icon"></i>\n' +
                        '</span></div>',
                    ]).draw(false)
                })
            },
            checkUserStatus (user) {
                if (user.is_active) {
                    return `checked = 'checked'`
                }
            },
            openAddEditUserModal (user = userForm, isEdit = false) {
                this.showAddEditUserModal = true
                this.isEditUser = isEdit
                this.userForm = Object.assign(this.userForm, user)
            },
            onDeleteUser (id) {
                this.deleteUser(id).then(() => {
                    this.drawUserDataTable(this.users)
                    successMsgToast('User delete successfully')
                })
            },
            listenEditButtonClick () {
                $('body').on('click', '#user-table tbody tr .edit-button', (e) => {
                    e.preventDefault()
                    const userId = $(e.target).data('id')
                    const index = this.users.findIndex(u => +u.id === +userId)
                    if (index >= 0) {
                        this.openAddEditUserModal(this.users[index], true)
                    }
                })
            },
            listenDeleteButtonClick () {
                $('body').off('click').on('click', '#user-table tbody tr .delete-btn', (e) => {
                    e.preventDefault()
                    if (confirm('Are you sure want to delete this user?')) {
                        const userId = $(e.target).data('id')
                        this.onDeleteUser(userId)
                    }
                })
            },
            listenUserStatusSwitchClick () {
                $('body').on('click', '#user-table tbody tr .switch-label', (e) => {
                    e.preventDefault()
                    const userId = $(e.target).data('id')
                    if (userId) {
                        this.updateUserStatus(userId).then(() => {
                            this.drawUserDataTable(this.users)
                        })
                    }
                })
            },
            onGetRoles () {
                this.getRoles().then((roles) => {
                    roles.forEach((c) => {
                        const role = {
                            value: c.id,
                            text: c.name,
                        }
                        this.roleOptions.push(role)
                    })
                })
            },
        },
        created () {
            this.getUsers().then((response) => {
                this.drawUserDataTable(response.users)
            })
            this.listenDeleteButtonClick()
            this.listenEditButtonClick()
            this.listenUserStatusSwitchClick()
            this.onGetRoles()
        },
        mounted () {
            this.dataTable = $('#user-table').DataTable({
                'columnDefs': [
                    { 'orderable': false, 'targets': [5, 6] },
                ],
                'language': {
                    'search': '',
                    'sSearch': '',
                },
                'preDrawCallback': () => {
                    customSearchFiledDataTable()
                },
            })
        },
    }
</script>

<style lang="scss">
    .dataTables_wrapper {
        .dataTables_paginate {
            .paginate_button.current {
                color: white !important;
            }
        }
    }
</style>
