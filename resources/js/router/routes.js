import {default as PageLogin} from "@/views/pages/auth/login/Main";
import {default as PageRegister} from "@/views/pages/auth/register/Main";
import {default as PageResetPassword} from "@/views/pages/auth/reset-password/Main";
import {default as PageForgotPassword} from "@/views/pages/auth/forgot-password/Main";
import {default as PageNotFound} from "@/views/pages/shared/404/Main";

import {default as PageDashboard} from "@/views/pages/private/dashboard/Main";
import {default as PageProfile} from "@/views/pages/private/profile/Main";

import {default as PageUsers} from "@/views/pages/private/users/Index";
import {default as PageUsersCreate} from "@/views/pages/private/users/Create";
import {default as PageUsersEdit} from "@/views/pages/private/users/Edit";

import {default as PageCustomer} from "@/views/pages/private/customers/Index";
import {default as PageCustomerCreate} from "@/views/pages/private/customers/Create";
import {default as PageCustomerEdit} from "@/views/pages/private/customers/Edit";
import {default as PageCustomerRecharge} from "@/views/pages/private/customers/Recharge.vue";

import {default as PageProduct} from "@/views/pages/private/products/Index";
import {default as PageProductCreate} from "@/views/pages/private/products/Create";
import {default as PageProductEdit} from "@/views/pages/private/products/Edit";

// Products
import {default as PageCategory} from "@/views/pages/private/categories/Index.vue";
import {default as PageCategoryEdit} from "@/views/pages/private/categories/Edit.vue";
import {default as PageCategoryCreate} from "@/views/pages/private/categories/Create.vue";

import abilities from "@/stub/abilities";

const routes = [
    {
        name: "home",
        path: "/",
        meta: {requiresAuth: true},
        component: PageDashboard
    },
    {
        path: "/login",
        name: "login",
        meta: {requiresAuth: false},
        component: PageLogin,
    },
    {
        path: "/register",
        name: "register",
        meta: {requiresAuth: false},
        component: PageRegister,
    },
    {
        path: "/reset-password",
        name: "resetPassword",
        meta: {requiresAuth: false},
        component: PageResetPassword,
    },
    {
        path: "/forgot-password",
        name: "forgotPassword",
        meta: {requiresAuth: false},
        component: PageForgotPassword,
    },
    {
        name: "dashboard",
        path: "/dashboard",
        meta: {requiresAuth: true},
        component: PageDashboard,
    },
    {
        name: "profile",
        path: "/profile",
        meta: {requiresAuth: true, isOwner: true},
        component: PageProfile,
    },
    {
        path: "/users",
        children: [
            {
                name: "users.list",
                path: "list",
                meta: {requiresAuth: true, requiresAbility: abilities.LIST_USER},
                component: PageUsers,
            },
            {
                name: "users.create",
                path: "create",
                meta: {requiresAuth: true, requiresAbility: abilities.CREATE_USER},
                component: PageUsersCreate,
            },
            {
                name: "users.edit",
                path: ":id/edit",
                meta: {requiresAuth: true, requiresAbility: abilities.EDIT_USER},
                component: PageUsersEdit,
            },
        ]
    },
    {
        path: "/customers",
        children: [
            {
                name: "customers.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageCustomer,
            },
            {
                name: "customers.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageCustomerCreate,
            },
            {
                name: "customers.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageCustomerEdit,
            },
            {
                name: "customers.recharge",
                path: ":id/recharge",
                meta: {requiresAuth: true},
                component: PageCustomerRecharge,
            },
        ]
    },
    {
        path: "/categories",
        children: [
            {
                name: "categories.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageCategory,
            },
            {
                name: "categories.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageCategoryCreate,
            },
            {
                name: "categories.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageCategoryEdit,
            }
        ]
    },
    {
        path: "/products",
        children: [
            {
                name: "products.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageProduct,
            },
            {
                name: "products.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageProductCreate,
            },
            {
                name: "products.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageProductEdit,
            }
        ]
    },
    {
        path: "/:catchAll(.*)",
        name: "notFound",
        meta: {requiresAuth: false},
        component: PageNotFound,
    },
]

export default routes;
