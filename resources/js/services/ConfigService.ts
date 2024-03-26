import ModelService from "@/services/ModelService";

export default class ConfigService extends ModelService {

    constructor() {
        super();
        this.url = '/configs';
    }
}
