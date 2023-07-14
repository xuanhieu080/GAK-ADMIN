import ModelService from "@/services/ModelService";

export default class SupportService extends ModelService {

    constructor() {
        super();
        this.url = '/supports';
    }
}
