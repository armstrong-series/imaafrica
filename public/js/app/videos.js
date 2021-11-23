if (window.Vue) {
new Vue({
    el: '#media',

    data: {
        isLoading: false,
       
        video: {
            name: "",
            contributor: "",
            file: ""
        },

        videoFile: "",

        videoEdit: {
            contributor: "",
            category: "",
            title: "",
            id: "",
            uuid: ""
        },


        gif_path: "",
        watermark_path: "",
        
        videos: [],

        url: {
           video:{
                create: "",
                update: "",
                delete: "",
                edit: ``,  
           }
        },

    },
   

    mounted() {
        this.videos = JSON.parse($('#videos').val());
        this.url.video.update = $("#updateDetails").val();
        this.url.video.create = $("#uploadVideo").val();
        this.url.video.delete = $("#deletVideo").val();
        this.url.video.edit = $("#edit-video").val() + '/';
        let uuid = window.location.href.split("/").reverse()[0];
        this.videoEdit.uuid = uuid;
    //    console.log('uuid...',uuid);
    },

  

    methods: {
        onChange() {
           let input = document.getElementById("assetsFieldHandle") 
            this.videoFile = this.$refs.file.files[0];
            console.log(this.videoFile)
             input.type = "button";
             input.style.display = "none";   
          },


          remove() {
            this.videoFile = "";
            let input = document.getElementById("assetsFieldHandle");
            input.type = "file";
          },

          dragover(event) {
            event.preventDefault();
            if (!event.currentTarget.classList.contains('bg-green-300')) {
              event.currentTarget.classList.remove('dropzone');
              event.currentTarget.classList.add('bg-green-300');
            }
          },


          dragleave(event) {
            event.currentTarget.classList.add('dropzone');
            event.currentTarget.classList.remove('bg-green-300');
          },

          drop(event) {
            event.preventDefault();
            this.$refs.file.files = event.dataTransfer.files;
            this.onChange(); 
            event.currentTarget.classList.add('dropzone');
            event.currentTarget.classList.remove('bg-green-300');
          },








     
        uploadVideo(){
            this.isLoading = true;
            const formData = new FormData();
            formData.append('video',  this.videoFile);
            formData.append('_token', $('input[name=_token]').val());
            axios.post(this.url.video.create, formData).then((response) => {
                this.isLoading = false;
                this.$toastr.Add({
                    msg: response.data.message, 
                    clickClose: false, 
                    timeout: 2000,
                    position: "toast-top-right", 
                    type: "success", 
                    preventDuplicates: true, 
                    progressbar: false,
                    style: {backgroundColor: "green"}
                });
                $('#videoUpload').modal('hide');
                this.videos.push(Object.assign({}, response.data.video, {}));
                window.location.href = response.data.url

            }).catch((error) => {
                console('error', error)
                this.isLoading = false
                if (error.response) {
                    this.$toastr.Add({
                        msg: error.response.data.message, 
                        clickClose: false, 
                        timeout: 2000,
                        position: "toast-top-right", 
                        type: "error", 
                        preventDuplicates: true, 
                        progressbar: false,
                        style: {backgroundColor: "red"}
                    });
                    
                    }
                })
            },

            updateVideoDetails(){
                // console.log('edit..', this.videoEdit);

                this.isLoading = true;
                axios.post(this.url.video.update, {
                    uuid: this.videoEdit.uuid,
                    title: this.videoEdit.title,
                    contributor: this.videoEdit.contributor,
                    category: this.videoEdit.category,
                    _token: $('input[name=_token]').val()
                })
                .then((response) => {
                    this.isLoading = false;
                    this.$toastr.Add({
                        msg: response.data.message, 
                        clickClose: false, 
                        timeout: 2000,
                        position: "toast-top-right", 
                        type: "success", 
                        preventDuplicates: true, 
                        progressbar: false,
                        style: {backgroundColor: "green"}
                    });
                    this.isLoading = false;
                    
                    window.location.reload();
                        
                
                }).catch((error) => {
                    this.isLoading = false
                    if (error.response) {
                        this.$toastr.Add({
                            msg: error.response.data.message, 
                            clickClose: false, 
                            timeout: 2000,
                            position: "toast-top-right", 
                            type: "error", 
                            preventDuplicates: true, 
                            progressbar: false,
                            style: {backgroundColor: "red"}
                        });
                        
                        }
                    })
                },
    


       deleteVideo(index) {
        this.Loading = true;
        let video = Object.assign({}, this.videos[index]);
        video._token = $('input[name=_token]').val();

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
                axios.post(this.url.video.delete, {'video_id': video.id })
                    .then(response => {
                        console.log('delete', response.data);
                        this.isLoading = false;
                        this.videos.splice(index, 1);
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

        downloadVideo(file) {
            axios.get('/video/download/' + file, { responseType: 'arraybuffer' }).then(response => {
                let blob = new Blob([response.data], { type: 'video/mp4' })
                const link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)
                link.download = file
                link.click();
            });
        },
       },



        

   
});

}