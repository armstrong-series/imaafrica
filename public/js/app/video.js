const { default: axios } = require("axios");

new Vue({
    el: '#video',
    data: {
        isLoading: false,


        video: {
            name: "",
            contributor: ""
        },

        
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
      


    },

  

    methods: {
     
       uploadVideo(){
        this.isLoading = true;
        const formData = new FormData();
        formData.append('video', )
        formData.append('contributor', this.video.contributor);
        formData.append('_token', $('input[name=_token]').val());
        axios.post(this.url.video.create, formData)
        .then((response) => {
            this.isLoading = false;
            let data = response.data;
         
            this.$notify({
                title: 'Success',
                message: 'Generated',
                type: 'success'
            });
            window.location.href = data.url

        })

       },
        downloadSRT(file) {
            axios.get('text-to-translate/download/' + file, { responseType: 'arraybuffer' }).then(response => {
                let blob = new Blob([response.data], { type: 'text/vtt' })
                const link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)
                link.download = file
                link.click();
            });
        },

   
     



    }
})