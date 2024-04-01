import BaseService from "@/services/BaseService";
import axios from "axios";

export default class SearchService extends BaseService {

    constructor(entity) {
        super();
        this.url = '/api/' + entity;
        this.setupAPI(axios.defaults.baseURL);
    }

    public begin(phrase, page, perPage, params = null) {
        if (params) {
            return this.get(this.url + `/?search=${phrase}&per_page=${perPage}&page=${page}${params}`)
        }
        return this.get(this.url + `/?search=${phrase}&per_page=${perPage}&page=${page}`)
    }

}

