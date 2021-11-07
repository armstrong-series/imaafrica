if (window.Vue) {
new Vue({
    el: '#media',
    delimiters: ['@{{', '}}'],
    // delimiters: ['${', '}'],
    

    data: {
        isLoading: false,
        isUpload: true,

        video: {
            name: "",
            contributor: "",
            file: ""
        },

        filelist: [],

      
        
        videos: [],

        url: {
           video:{
                create: "",
                update: "",
                delete: ""
           }
        },

    },
   

    mounted() {
        this.url.video.create = $("#uploadVideo").val();

        

    },

  

    methods: {
        onChange() {
            this.filelist = [...this.$refs.file.files];
          },
          remove(i) {
            this.filelist.splice(i, 1);
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
            formData.append('video', this.filelist);
            formData.append('_token', $('input[name=_token]').val());
            axios.post(this.url.video.create, formData,{
                maxContentLength: 100000000,
                maxBodyLength: 1000000000
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
                $('#videoUpload').modal('hide');
                let data = response.data;
                window.location.href = data.url

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
       },
        downloadVideo(file) {
            axios.get('text-to-translate/download/' + file, { responseType: 'arraybuffer' }).then(response => {
                let blob = new Blob([response.data], { type: 'text/vtt' })
                const link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)
                link.download = file
                link.click();
            });
        },

   
});

}