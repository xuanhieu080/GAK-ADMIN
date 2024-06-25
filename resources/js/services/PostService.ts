import ModelService from "@/services/ModelService";

export default class PostService extends ModelService {

    constructor() {
        super();
        this.url = '/posts';
    }
}
