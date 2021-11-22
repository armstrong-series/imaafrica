
if (window.Vue) {
    new Vue({
        el: '#playListTrack',
        data: {
            isLoading: false,

            audio: {
                name: "",
                title: "",
                category: "",
                file: ""
            },
            audioEdit: {
                name: "",
                title: "",
                category: "",

            },


            audioFile: null,
            file: "",

            results: [],

            audios: [],


            filterTracks: '',

            originalFile: '',
            imageFile: null,
            input: null,
            isImageUploading: false,


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
          
            this.audios = JSON.parse($('#playlists').val());
            this.url.audio.create = $('#createAudio').val();
            this.url.audio.update= $('#updateAudio').val();
            this.url.audio.delete = $('#delete').val();
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

            clearImage() {
                this.imageFile = null;
                this.input = null;
            },


            audioCoverPreview(event) {
               
                this.input = event.target;
                if (this.input.files && this.input.files[0]) {
                    this.originalFile = this.input.files[0]
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageFile = e.target.result;
                    };
    
                    reader.readAsDataURL(this.input.files[0]);
                }
            },
    

            handleFileUpload(event) {
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

            searchTrack() {
                this.results = this.filterTracks.trim() == '' ? [] :
                this.audios.filter(each => each.category.toLowerCase().includes(this.filterTracks.toLowerCase()))
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
                formData.append('audio_track', this.file);
                formData.append('img_cover', this.originalFile)
                formData.append('title', this.audio.title);
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
                    text: `This action is Permanent. Are you sure?`,
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
                        axios.post(this.url.audio.delete, {'audio_id': audio.id })
                            .then(response => {
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
                                    this.$toastr.Add({
                                        msg: "Unable to process",
                                        clickClose: false,
                                        timeout: 2000,
                                        position: "toast-top-right",
                                        type: "error",
                                        preventDuplicates: true,
                                        progressbar: false,
                                        style: { backgroundColor: "red" }
                                    });
                                }
                            });
                    }
                });

            },

            downloadTrack(file) {
                axios.get('/audio/download/' + file, { responseType: 'arraybuffer' }).then(response => {
                    let track = new Blob([response.data], { type: 'audio/mpeg' })
                    const link = document.createElement('a')
                    link.href = window.URL.createObjectURL(track)
                    link.download = file
                    link.click();
                });
            },





        }

    });
}