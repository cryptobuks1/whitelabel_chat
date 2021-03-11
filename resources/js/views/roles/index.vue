<template>
    <main class="main">
        <div class="page-container">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="pull-left page__heading">
                                    {{$t('roles.roles')}}
                                </div>
                                <button type="button" class="pull-right btn btn-primary"
                                        @click="openAddEditRoleModal()">{{$t('roles.newRole')}}
                                </button>
                            </div>
                            <div class="card-body">
                                <table id="role-table" class="table table-striped table-bordered nowrap" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th>{{$t('roles.name')}}</th>
                                        <th class="dt-action-btn">{{$t('roles.action')}}</th>
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

        <b-modal :title="isEditRole?$t('roles.editRole'):$t('roles.newRole')" centered v-model="showAddEditRoleModal"
                 size="md"
                 hide-footer default="false">
            <div class="add-edit-role">
                <form autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="role-name">{{$t('roles.name')}}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="role-name"
                                       aria-describedby="Role name" :placeholder="$t('roles.name')" name="name"
                                       v-validate="{ required: true, min: 3 }" autocomplete="name"
                                       v-model="roleForm.name"
                                       :class="errors.has('name')?'br-danger':''">
                                <div class="error-msg-no-mt" v-if="errors.has('name')">
                                    {{ errors.first('name') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <b-button variant="primary" class="btn btn-primary mr-2" @click.stop="save()">
                            {{$t('roles.save')}}
                        </b-button>
                        <b-button variant="secondary" class="d-inline mt-0"
                                  @click.prevent="closeModal">{{$t('roles.cancel')}}
                        </b-button>
                    </div>
                </form>
            </div>
        </b-modal>
    </main>
</template>

<script>
    import * as types from '../../store/types'
    import { mapActions, mapGetters } from 'vuex'
    import { successMsgToast } from '../../utils/toasts'
    import { customSearchFiledDataTable } from '../../utils/custom-search-field-datatable'

    const roleForm = {
        name: '',
    }
    export default {
        name: 'Roles',
        data: () => {
            return {
                dataTable: null,
                showAddEditRoleModal: false,
                roleForm: Object.assign({}, roleForm),
                isEditRole: false,
            }
        },
        computed: {
            ...mapGetters({
                roles: types.ROLE_LIST,
            }),
        },
        methods: {
            ...mapActions({
                getRoles: types.GET_ROLE_LIST,
                addRole: types.ADD_ROLE,
                editRole: types.EDIT_ROLE,
                deleteRole: types.DELETE_ROLE,
            }),
            save () {
                this.$validator.validateAll()
                this.$validator.validate().then(valid => {
                    if (valid) {
                        if (!this.isEditRole) {
                            this.addRole(this.roleForm).then(() => {
                                this.showAddEditRoleModal = false
                                this.drawRoleDataTable(this.roles)
                                successMsgToast('Role added successfully')
                            })
                        } else {
                            this.editRole(this.roleForm).then(() => {
                                this.showAddEditRoleModal = false
                                this.drawRoleDataTable(this.roles)
                                successMsgToast('Role updated successfully')
                            })
                        }
                    }
                })
            },
            closeModal () {
                this.showAddEditRoleModal = false
            },
            drawRoleDataTable (roles) {
                this.dataTable.clear().draw()
                roles.forEach(role => {
                    this.dataTable.row.add([
                        role.name,
                        !role.is_default ? '<span data-id="' + role.id +
                            '" class="index__btn btn btn-ghost-success btn-sm edit-btn edit-button"\n' +
                            'title="Edit">\n' +
                            '<i data-id="' + role.id + '" class="cui-pencil action-icon"></i>\n' +
                            '</span>\n' +
                            '<span data-id="' + role.id +
                            '" class="index__btn btn btn-ghost-danger btn-sm delete-btn"\n' +
                            'title="Delete">\n' +
                            '<i data-id="' + role.id + '" class="cui-trash action-icon"></i>\n' +
                            '</span></div>' : '---',
                    ]).draw(false)
                })
            },
            openAddEditRoleModal (role = roleForm, isEdit = false) {
                this.roleForm = {
                    name: '',
                }
                this.showAddEditRoleModal = true
                this.isEditRole = isEdit
                if (this.isEditRole) {
                    this.roleForm = Object.assign(this.roleForm, role)
                }
            },
            onDeleteRole (id) {
                this.deleteRole(id).then(() => {
                    this.drawRoleDataTable(this.roles)
                    successMsgToast('Role delete successfully')
                })
            },
            listenEditButtonClick () {
                $('body').on('click', '#role-table tbody tr .edit-button', (e) => {
                    e.preventDefault()
                    const rolesId = $(e.target).data('id')
                    const index = this.roles.findIndex(r => +r.id === +rolesId)
                    if (index >= 0) {
                        this.openAddEditRoleModal(this.roles[index], true)
                    }
                })
            },
            listenDeleteButtonClick () {
                $('body').off('click').on('click', '#role-table tbody tr .delete-btn', (e) => {
                    e.preventDefault()
                    if (confirm('Are you sure want to delete this role?')) {
                        const rolesId = $(e.target).data('id')
                        this.onDeleteRole(rolesId)
                    }
                })
            },
        },
        created () {
            this.getRoles().then((response) => {
                if (response) {
                    this.drawRoleDataTable(response)
                }
            })
            this.listenDeleteButtonClick()
            this.listenEditButtonClick()
        },
        mounted () {
            this.dataTable = $('#role-table').DataTable({
                'columnDefs': [
                    { 'orderable': false, 'targets': 1 },
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
