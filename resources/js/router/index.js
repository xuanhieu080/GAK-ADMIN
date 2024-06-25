import {createWebHistory, createRouter} from "vue-router";

import routes from "@/router/routes";

import {useAuthStore} from "@/stores/auth";
import {useAlertStore} from "@/stores";

const router = createRouter({
    history: createWebHistory(),
    linkActiveClass: 'active',
    routes,
})

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const errorStore = useAlertStore();
    errorStore.clear();
    const requiresAbility = to.meta.requiresAbility;
    const requiresAuth = to.meta.requiresAuth;
    const belongsToOwnerOnly = to.meta.isOwner;

    if (!authStore.user) {
        await authStore.getCurrentUser();
    }
    if (!authStore.user && requiresAuth) {
        router.push({name: 'login'})
    }
    if (!authStore.user) {
        authStore.clearBrowserData();
    }
    if (to.name == 'login' && authStore.user) {
        router.push({name: 'dashboard'})
    }
    if (requiresAbility && requiresAuth) {
        if (authStore.hasAbilities(requiresAbility)) {
            next()
        } else {
            next({
                name: 'profile'
            })
        }
    } else if (belongsToOwnerOnly) {
        if (authStore.user.is_owner) {
            next()
        } else {
            next({name: 'home'})
        }
    } else {
        next()
    }
})

export default router;
