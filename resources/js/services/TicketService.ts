import ModelService from "@/services/ModelService";

export default class TicketService extends ModelService {

    constructor() {
        super();
        this.url = '/tickets';
    }
}
