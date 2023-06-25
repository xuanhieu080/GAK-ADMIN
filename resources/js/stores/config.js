import {acceptHMRUpdate, defineStore} from 'pinia';

export const useConfig = defineStore('config', {
    state: () => ({
        configs: {
            items: [],
            search: '',
            limit: 12,
            logo: '/assets/images/logo.png',
            name: 'CMS',
            address: 'Địa chỉ',
            phone: '09999999999',
        }
    }),
    actions: {
        async getConfig() {
            try {
                const response = await axios.get(route('user.configs.index'), {
                    params: {
                        limit: this.configs.limit,
                        search: this.configs.search,
                    }
                });
                this.configs.items = response.data.data;
                const logoIndex = response.data.data.findIndex(it => it.code === 'logo')
                this.configs.logo = response.data.data[logoIndex].value
                const nameIndex = response.data.data.findIndex(it => it.code === 'name')
                this.configs.name = response.data.data[nameIndex].value
                const addressIndex = response.data.data.findIndex(it => it.code === 'address')
                this.configs.address = response.data.data[addressIndex].value
                const phoneIndex = response.data.data.findIndex(it => it.code === 'phone')
                this.configs.phone = response.data.data[phoneIndex].value

            } catch (error) {
                console.error(error);
            }
        },
    }
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useConfig, import.meta.hot))
}
