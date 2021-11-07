
if (window.Vue) {
    new Vue({
        el: '#playListTrack',
        data: {
            isLoading: false,

            audio: {
                name: "",
                category: "",
                file: ""
            },
            audioEdit: {
                name: "",
                category: "",

            },


            audioFile: null,
            file: "",



            audios: [],

            url: {
                audio: {
                    create: "",
                    update: "",
                    delete: "",
                    download: ""
                }
            },

        },




        mounted() {
            console.log('content', this.audios)
            this.audios = JSON.parse($('#playlists').val());
            this.url.audio.create = $('#createAudio').val();
            this.url.audio.update= $('#updateAudio').val();
            this.url.audio.delete = $('#deletePlaylist').val();
            this.url.audio.download = $('#download').val();
        },



        methods: {
            previewAudio() {
                let audio = document.getElementById('audio-preview');
                let reader = new FileReader();

                reader.readAsDataURL(this.file);
                reader.addEventListener('load', function () {
                    audio.src = reader.result;
                });
            },

            handleFileUpload(event) {
                console.log('audio....', this.file)
                this.file = event.target.files[0];
                this.previewAudio();
            },

            showDialogInfo(index) {
                this.audioEdit = {
                    ...this.audioEdit,
                    id: this.audios[index].id,
                    name: this.audios[index].name,
                    category: this.audios[index].category,
                }

            },

            updateTrackDetails() {
                this.isLoading = true;
                const formData = new FormData();
                formData.append('_token', $('input[name=_token]').val());
                for (let key in this.audioEdit) {
                      let value = this.audioEdit[key];
                    formData.append(key, value);
                }
                this.isLoading = true;

                axios.post(this.url.audio.update, formData)
                    .then((response) => {
                        $('#edit-track').modal('hide');
                        this.$toastr.Add({
                            msg: response.data.message,
                            clickClose: false,
                            timeout: 2000,
                            position: "toast-top-right",
                            type: "success",
                            preventDuplicates: true,
                            progressbar: false,
                            style: {backgroundColor: "#347C17"}
                        });
                        this.isLoading = false;
                        
                        var editedAudio = response.data.track;
                        this.audios = this.audios.map((track) => {
                            if (track.id === editedAudio.id) {
                                track = Object.assign({}, editedAudio);
                            }
                            return track;
                        });
                    })
                    .catch((error) => {
                        this.$toastr.Add({
                            msg: error.response.data.message,
                            clickClose: false,
                            timeout: 2000,
                            position: "toast-top-right",
                            type: "error",
                            preventDuplicates: true,
                            progressbar: false,
                            style: { backgroundColor: "red" }
                        });
                        this.isLoading = false;



                    });
            },

            sendAudio() {
                this.isLoading = true;
                const formData = new FormData();
                formData.append('audio_track', this.file)
                formData.append('name', this.audio.name);
                formData.append('category', this.audio.category);
                formData.append('_token', $('input[name=_token]').val());
                axios.post(this.url.audio.create, formData, {
                    headers: { 'Content-Type': 'multipart/formdata' }
                }).then((response) => {
                        this.isLoading = false;
                        this.$toastr.Add({
                            msg: response.data.message,
                            clickClose: false,
                            timeout: 2000,
                            position: "toast-top-right",
                            type: "success",
                            preventDuplicates: true,
                            progressbar: false,
                            style: { backgroundColor: "#347C17" }
                        });
                        $('#audioUpload').modal('hide');
                        this.audios.push(Object.assign({}, response.data.track, {}));
                       

                    }).catch((error) => {
                        this.isLoading = false
                        this.$toastr.Add({
                            msg: error.response.data.message,
                            clickClose: false,
                            timeout: 2000,
                            position: "toast-top-right",
                            type: "error",
                            preventDuplicates: true,
                            progressbar: false,
                            style: { backgroundColor: "red" }
                        });

                    });
            },

            deletePlaylist(index) {
                this.Loading = true;
                let audio = Object.assign({}, this.audios[index]);
                audio._token = $('input[name=_token]').val();

                const customAlert = swal({
                    title: 'Warning',
                    text: `Are you sure you want to delete Record? This action is Permanent`,
                    icon: 'warning',
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: "cancel",
                            visible: true,
                            className: "",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Confirm",
                            value: 'delete',
                            visible: true,
                            className: "btn-danger",
                        }
                    }
                });

                customAlert.then(value => {
                    if (value == 'delete') {
                        this.isLoading = true;

                        axios.delete(this.url.audio.delete, { data: audio })
                            .then(response => {
                                $('#deleteTrack').modal('hide');
                                console.log('delete', response.data);
                                this.isLoading = false;
                                this.audios.splice(index, 1);
                                this.$toastr.Add({
                                    msg: response.data.message,
                                    clickClose: false,
                                    timeout: 2000,
                                    position: "toast-top-right",
                                    type: "success",
                                    preventDuplicates: true,
                                    progressbar: false,
                                    style: { backgroundColor: "#347C17" }
                                });

                            }).catch((error) => {
                                this.isLoading = false;
                                if (error.response) {
                                    this.$toastr.Add({
                                        msg: error.response.data.message,
                                        clickClose: false,
                                        timeout: 2000,
                                        position: "toast-top-right",
                                        type: "error",
                                        preventDuplicates: true,
                                        progressbar: false,
                                        style: { backgroundColor: "red" }
                                    });
                                } else {
                                    this.$notify.error({
                                        title: 'Error',
                                        message: 'oops! Unable to complete request.'
                                    });
                                }
                            });
                    }
                });

            },

            downloadTrack(file) {
                axios.get(this.url.audio.download + file, { responseType: 'arraybuffer' }).then(response => {
                    let track = new Blob([response.data], { type: 'audio/*' })
                    const link = document.createElement('a')
                    link.href = window.URL.createObjectURL(track)
                    link.download = file
                    link.click();
                });
            },





        }

    });
}