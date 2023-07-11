import ModelService from "@/services/ModelService";

export default class CustomerService extends ModelService {

    constructor() {
        super();
        this.url = '/customers';
    }
}
