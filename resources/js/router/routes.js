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

import {default as PagePostIndex} from "@/views/pages/private/posts/Index.vue";
import {default as PagePostCreate} from "@/views/pages/private/posts/Create";
import {default as PagePostEdit} from "@/views/pages/private/posts/Edit.vue";

import {default as PagePostGroupIndex} from "@/views/pages/private/post-groups/Index.vue";
import {default as PagePostGroupCreate} from "@/views/pages/private/post-groups/Create";
import {default as PagePostGroupEdit} from "@/views/pages/private/post-groups/Edit.vue";

import {default as PageGroupIndex} from "@/views/pages/private/page-groups/Index.vue";
import {default as PageGroupCreate} from "@/views/pages/private/page-groups/Create";
import {default as PageGroupEdit} from "@/views/pages/private/page-groups/Edit.vue";

import {default as PageIndex} from "@/views/pages/private/pages/Index.vue";
import {default as PageCreate} from "@/views/pages/private/pages/Create";
import {default as PageEdit} from "@/views/pages/private/pages/Edit.vue";

import {default as PageSupportIndex} from "@/views/pages/private/supports/Index.vue";
import {default as PageSupportCreate} from "@/views/pages/private/supports/Create";
import {default as PageSupportEdit} from "@/views/pages/private/supports/Edit.vue";

import {default as PageAttributeGroupIndex} from "@/views/pages/private/attribute-groups/Index.vue";
import {default as PageAttributeGroupCreate} from "@/views/pages/private/attribute-groups/Create";
import {default as PageAttributeGroupEdit} from "@/views/pages/private/attribute-groups/Edit.vue";

import {default as PageAttributeIndex} from "@/views/pages/private/attributes/Index.vue";
import {default as PageAttributeCreate} from "@/views/pages/private/attributes/Create";
import {default as PageAttributeEdit} from "@/views/pages/private/attributes/Edit.vue";

import {default as SeoContentIndex} from "@/views/pages/private/seo-contents/Index.vue";
import {default as SeoContentCreate} from "@/views/pages/private/seo-contents/Create";
import {default as SeoContentEdit} from "@/views/pages/private/seo-contents/Edit.vue";

import {default as PageConfigIndex} from "@/views/pages/private/configs/Index.vue";
import {default as PageConfigEdit} from "@/views/pages/private/configs/Edit.vue";

import {default as PageOrderIndex} from "@/views/pages/private/orders/Index.vue";
import {default as PageOrderEdit} from "@/views/pages/private/orders/Edit.vue";

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
        path: "/post-groups",
        children: [
            {
                name: "post_groups.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PagePostGroupIndex,
            },
            {
                name: "post_groups.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PagePostGroupCreate,
            },
            {
                name: "post_groups.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PagePostGroupEdit,
            }
        ]
    },
    {
        path: "/page-groups",
        children: [
            {
                name: "page_groups.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageGroupIndex,
            },
            {
                name: "page_groups.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageGroupCreate,
            },
            {
                name: "page_groups.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageGroupEdit,
            }
        ]
    },
    {
        path: "/pages",
        children: [
            {
                name: "pages.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageIndex,
            },
            {
                name: "pages.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageCreate,
            },
            {
                name: "pages.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageEdit,
            }
        ]
    },
    {
        path: "/posts",
        children: [
            {
                name: "posts.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PagePostIndex,
            },
            {
                name: "posts.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PagePostCreate,
            },
            {
                name: "posts.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PagePostEdit,
            }
        ]
    },
    {
        path: "/supports",
        children: [
            {
                name: "supports.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageSupportIndex,
            },
            {
                name: "supports.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageSupportCreate,
            },
            {
                name: "supports.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageSupportEdit,
            }
        ]
    },
    {
        path: "/attribute-groups",
        children: [
            {
                name: "attribute_group.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageAttributeGroupIndex,
            },
            {
                name: "attribute_group.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageAttributeGroupCreate,
            },
            {
                name: "attribute_group.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageAttributeGroupEdit,
            }
        ]
    },
    {
        path: "/attributes",
        children: [
            {
                name: "attribute.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageAttributeIndex,
            },
            {
                name: "attribute.create",
                path: "create",
                meta: {requiresAuth: true},
                component: PageAttributeCreate,
            },
            {
                name: "attribute.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageAttributeEdit,
            }
        ]
    },
    {
        path: "/seo-contents",
        children: [
            {
                name: "seo_contents.list",
                path: "list",
                meta: {requiresAuth: true},
                component: SeoContentIndex,
            },
            {
                name: "seo_contents.create",
                path: "create",
                meta: {requiresAuth: true},
                component: SeoContentCreate,
            },
            {
                name: "seo_contents.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: SeoContentEdit,
            }
        ]
    },
    {
        path: "/configs",
        children: [
            {
                name: "configs.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageConfigIndex,
            },
            {
                name: "configs.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageConfigEdit,
            }
        ]
    },
    {
        path: "/orders",
        children: [
            {
                name: "orders.list",
                path: "list",
                meta: {requiresAuth: true},
                component: PageOrderIndex,
            },
            {
                name: "orders.edit",
                path: ":id/edit",
                meta: {requiresAuth: true},
                component: PageOrderEdit,
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
