import BaseService from "@/services/BaseService";
import axios from "@/plugins/axios";

import {useAlertStore} from "@/stores";
import {getResponseError} from "@/helpers/api";

import {useGlobalStateStore} from "@/stores";

export default abstract class ModelService extends BaseService {

    constructor() {
        super();
        this.setupAPI(axios.defaults.baseURL + '/api');
    }

    public create() {
        return this.get(this.url + `/create`, {});
    }

    public find(object_id) {
        return this.get(this.url + `/${object_id}`, {});
    }

    public edit(object_id) {
        return this.get(this.url + `/${object_id}/edit`, {});
    }

    public storeCustom(object_id, payload) {
        let data = this.transformPayloadForSubmission(payload);
        return this.post(this.url + `/${object_id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
        });
    }
    public store(payload) {
        let data = this.transformPayloadForSubmission(payload);
        return this.post(this.url, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
        })
    }

    public update(object_id, payload) {
        let data = this.transformPayloadForSubmission(payload);
        return this.patch(this.url + `/${object_id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
        });
    }

    public delete(object_id) {
        return super.delete(this.url + `/${object_id}`, {});
    }

    public index(params = {}) {
        let path = this.url;
        let query = new URLSearchParams(params).toString();
        if (query) {
            path += '?' + query
        }
        return this.get(path, {});
    }
    public indexDetail(subUrl, params = {}) {
        let path = this.url + subUrl;
        let query = new URLSearchParams(params).toString();
        if (query) {
            path += '?' + query
        }
        return this.get(path, {});
    }

    public handleUpdate(ui_element_id, object_id, data) {
        const alertStore = useAlertStore();
        const globalUserState = useGlobalStateStore();
        globalUserState.loadingElements[ui_element_id] = true;
        return this.update(object_id, data).then((response) => {
            let answer = response.data;
            alertStore.success(answer.message);
            return response;
        }).catch((error) => {
            alertStore.error(getResponseError(error), error.response.status);
        }).finally(() => {
            globalUserState.loadingElements[ui_element_id] = false;
        })
    }

    public handleCreate(ui_element_id, data) {
        const alertStore = useAlertStore();
        const globalUserState = useGlobalStateStore();
        globalUserState.setElementLoading(ui_element_id, true);
        return this.store(data).then((response) => {
            let answer = response.data;
            alertStore.success(answer.message);
        }).catch((error) => {
            alertStore.error(getResponseError(error), error.response.status);
        }).finally(() => {
            globalUserState.setElementLoading(ui_element_id, false);
        })
    }

    public handleCreateCustom(ui_element_id,object_id, data) {
        const alertStore = useAlertStore();
        const globalUserState = useGlobalStateStore();
        globalUserState.loadingElements[ui_element_id] = true;
        return this.storeCustom(object_id, data).then((response) => {
            let answer = response.data;
            alertStore.success(answer.message);
        }).catch((error) => {
            alertStore.error(getResponseError(error), error.response.status);
        }).finally(() => {
            globalUserState.loadingElements[ui_element_id] = false;
        })
    }

    public transformPayloadForSubmission(payload) {

        let data = new FormData();

        const appendToFormData = (data, key, val) => {
            if (Array.isArray(val)) {
                for (let i = 0; i < val.length; i++) {
                    if(val[i] instanceof File) {
                        data.append(`${key}[${i}]`, val[i]);
                    } else if (typeof val[i] === 'object' && val[i] !== null) {
                        for(let subKey in val[i]) {
                            if(val[i].hasOwnProperty(subKey)) {
                                appendToFormData(data, `${key}[${i}][${subKey}]`, val[i][subKey]);
                            }
                        }
                    } else {
                        data.append(`${key}[${i}]`, val[i]);
                    }
                }
            } else if(val !== null && val !== undefined) {
                data.append(key, val);
            }
        }

        for (let key in payload) {
            if (payload.hasOwnProperty(key)) {
                appendToFormData(data, key, payload[key]);
            }
        }

        return data;
    }

    // public transformPayloadForSubmission(payload) {
    //     let data = new FormData();
    //     for (let key in payload) {
    //         let val = payload[key];
    //         if (Array.isArray(val)) {
    //             for (let index in val) {
    //                 if (typeof val[index] === 'object' && val[index] !== null && val[index] instanceof File == false) {
    //                     const array = this.objectToArray(val[index]);
    //                     array.forEach(function(number) {
    //                         data.append(key + `[${index}][${number.key}]`, number.value);
    //                     });
    //                 } else if(val != null) {
    //                     data.append(key + `[${index}]`, val[index]);
    //                 }
    //             }
    //         } else if(val != null) {
    //             data.append(key, val);
    //         }
    //     }
    //     return data;
    // }

    public objectToArray(obj) {
        const result = [];

        for (const key in obj) {
            if (obj.hasOwnProperty(key)) {
                result.push({ key: key, value: obj[key] });
            }
        }

        return result;
    }
}
