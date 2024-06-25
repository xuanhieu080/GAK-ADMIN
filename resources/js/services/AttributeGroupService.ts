import ModelService from "@/services/ModelService";

export default class AttributeGroupService extends ModelService {

    constructor() {
        super();
        this.url = '/attribute-groups';
    }
}
