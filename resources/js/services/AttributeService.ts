import ModelService from "@/services/ModelService";

export default class AttributeService extends ModelService {

    constructor() {
        super();
        this.url = '/attributes';
    }
}
