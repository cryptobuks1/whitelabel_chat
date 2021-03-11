<template>
    <div>
        <video ref="videoPlayer" class="video-js vjs-big-play-centered vjs-default-skin" controls>
            <source :src="options.sources.src" type="video/mp4">
            <source :src="options.sources.src" type="video/avi">
            <source :src="options.sources.src" type="video/mkv">
            Your browser does not support the video tag, Kindly update your browser
        </video>
    </div>
</template>

<script>
    import videojs from 'video.js'

    export default {
        name: 'VideoPlayer',
        props: {
            options: {
                type: Object,
                default () {
                    return {}
                },
            },
        },
        data () {
            return {
                player: null,
            }
        },
        mounted () {
            // if you want you video.js
            // this.player = videojs(this.$refs.videoPlayer, this.options, () => {
            // })
        },
        beforeDestroy () {
            // do if video.js used for video
            // if (this.player) {
            //     this.player.dispose()
            // }
        },
    }
</script>

<style>
    .video-msg {
        object-fit: cover;
    }

    .video-js {
        width: 250px;
        height: 250px;
    }
</style>
