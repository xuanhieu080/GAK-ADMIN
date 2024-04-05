import ModelService from "@/services/ModelService";

export default class PageService extends ModelService {

    constructor() {
        super();
        this.url = '/pages';
    }
}
