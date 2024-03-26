import ModelService from "@/services/ModelService";

export default class CommentService extends ModelService {

    constructor() {
        super();
        this.url = '/comments';
    }
}
