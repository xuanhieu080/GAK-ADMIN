import axios from 'axios';
import { ClassicEditor, Essentials, Paragraph, Image, ImageUpload } from 'ckeditor5';

class CustomUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    // Starts the upload process.
    upload() {
        return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                this._initListeners(resolve, reject, file);
                this._sendRequest(file, resolve, reject);
            }));
    }

    // Aborts the upload process.
    abort() {
        if (this.cancelTokenSource) {
            this.cancelTokenSource.cancel('Upload aborted.');
        }
    }

    // Initializes XMLHttpRequest listeners.
    _initListeners(resolve, reject, file) {
        const loader = this.loader;
        const genericErrorText = `Couldn't upload file: ${file.name}.`;

        this.cancelTokenSource = axios.CancelToken.source();

        axios.interceptors.response.use(
            response => response,
            error => {
                if (axios.isCancel(error)) {
                    reject('Upload aborted.');
                } else {
                    reject(error.response?.data?.message || genericErrorText);
                }
                return Promise.reject(error);
            }
        );
    }

    // Prepares the data and sends the request.
    _sendRequest(file, resolve, reject) {
        const data = new FormData();
        data.append('image', file);

        // Fetch CSRF token and then upload the file
        axios.get('/sanctum/csrf-cookie')
            .then(() => {
                return axios.post('/api/upload-image', data, {
                    headers: {
                        'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
                    },
                    cancelToken: this.cancelTokenSource.token
                });
            })
            .then(response => {
                console.log(response.data);
                if (response.data.error) {
                    reject(response.data.error.message);
                } else {
                    resolve({
                        default: response.data.link
                    });
                }
            })
            .catch(error => {
                reject(error.message);
            });
    }

    getCookie(name) {
        let cookie = {};
        document.cookie.split(';').forEach(function(el) {
            let [k, v] = el.split('=');
            cookie[k.trim()] = v;
        });
        return cookie[name];
    }
}

export default CustomUploadAdapter;
