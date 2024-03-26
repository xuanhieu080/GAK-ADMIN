import axios from "@/plugins/axios";
class CustomUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('upload', this.loader.file);
            axios.get("/sanctum/csrf-cookie");
            console.log('d')
            axios.post('/your-upload-endpoint', data)
                .then(response => {
                    resolve({
                        default: response.data.url
                    });
                })
                .catch(error => {
                    reject(error);
                });
        });
    }

    abort() {
        // Handle the abort upload action
    }
}

export default CustomUploadAdapter;