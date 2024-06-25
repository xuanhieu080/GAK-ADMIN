import ModelService from "@/services/ModelService";

export default class PostGroupService extends ModelService {

    constructor() {
        super();
        this.url = '/post-groups';
    }
}
