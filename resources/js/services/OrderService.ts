import ModelService from "@/services/ModelService";

export default class OrderService extends ModelService {

    constructor() {
        super();
        this.url = '/orders';
    }
}
