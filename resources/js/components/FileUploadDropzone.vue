<template>
    <div id="mediaUploadZone">
        <vue-dropzone ref="vueDropzone" id="dropzone" :options="dropzoneOptions"></vue-dropzone>

        <div class="d-flex mt-3 float-right" v-if="noOfAcceptedFiles>0">
            <button id="submit-all" class="upload-file-btn btn btn-primary mr-2" @click="onUploadSuccess">Upload
            </button>
            <button type="reset" id="cancel-upload-file" class="upload-file-btn btn btn-secondary"
                    @click="onCloseDropZone">Cancel
            </button>
        </div>
    </div>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import { errorMsgToast } from '../utils/toasts'
    import axiosWithFormData from '../api/axios-with-form-data'

    export default {
        name: 'FileUploadDropzone',
        components: {
            vueDropzone: vue2Dropzone,
        },
        data: function () {
            return {
                dropzoneOptions: {
                    url: 'api/file-upload',
                    // thumbnailWidth: 150,
                    maxFiles: 10,
                    acceptedFiles: 'image/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.mp4,.mkv,.avi,.txt',
                    autoProcessQueue: false,
                    addRemoveLinks: true,
                    maxFilesize: 100,
                    error: this.onUploadError,
                    uploadMultiple: true,
                },
                noOfAcceptedFiles: 0,
            }
        },
        methods: {
            onUploadSuccess () {
                const files = this.$refs.vueDropzone.getQueuedFiles()
                if (files.length > 0) {
                    const formData = new FormData()
                    files.forEach((file) => {
                        formData.append('file[]', file)
                    })
                    axiosWithFormData.post('file-upload', formData).then((response) => {
                        response.data.forEach((f) => {
                            this.$emit('fileUploaded', f)
                        })
                    })
                }
            },
            onUploadError (event, message) {
                if (!event.accepted) {
                    errorMsgToast(message)
                } else if (message.message) {
                    errorMsgToast(message.message)
                }
            },
            countNoOFAcceptedFiles () {
                this.$refs.vueDropzone.dropzone.on('addedfile', () => {
                    this.noOfAcceptedFiles = this.$refs.vueDropzone.dropzone.files.length
                })

                this.$refs.vueDropzone.dropzone.on('removedfile', () => {
                    this.noOfAcceptedFiles = this.$refs.vueDropzone.dropzone.files.length
                })
            },
            onCloseDropZone () {
                this.$emit('onClose')
            },
        },
        mounted () {
            this.noOfAcceptedFiles = this.$refs.vueDropzone.dropzone.files.length
            this.countNoOFAcceptedFiles()
        },
    }
</script>

<style lang="scss">
    #mediaUploadZone {
        .vue-dropzone {
            height: 400px;
            overflow: auto;
        }

        .dz-progress {
            display: none !important;
        }

        .dz-remove {
            width: 60%;
            margin-left: 20%;
            margin-right: 20%;
            padding: 5px;
            font-size: 10px
        }
    }
</style>
