import ModelService from "@/services/ModelService";

export default class ProductService extends ModelService {

    constructor() {
        super();
        this.url = '/products';
    }
}
