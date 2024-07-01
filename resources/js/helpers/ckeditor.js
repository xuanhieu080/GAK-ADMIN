import axios from "@/plugins/axios";
class CustomUploadAdapter {
    constructor(loader) {
        this.loader = loader;
        console.log('aaaa')
    }

    upload() {
        return new Promise((resolve, reject) => {

            console.log('bbbb')
            this.loader.file.then(file => {
                const data = new FormData();
                data.append('image', file);
                axios.get("/sanctum/csrf-cookie").then(() => {
                    axios.post('/api/upload-image', data)
                        .then(response => {
                            resolve({
                                default: response.data.url
                            });
                        })
                        .catch(error => {
                            reject(error.toString());
                        });
                });
            });
        });
    }

    abort() {

        console.log('cccc')
        // Handle the abort upload action
    }
}

export default CustomUploadAdapter;