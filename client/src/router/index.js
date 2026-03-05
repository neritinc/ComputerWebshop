import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/views/HomeView.vue";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";

function checkIfNotLogged() {
  const storeAuth = useUserLoginLogoutStore();
  if (!storeAuth.isLoggedIn) {
    return "/login";
  }
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
      meta: {
        title: () => "Fooldal",
        breadcrumb: "Fooldal",
      },
    },
    {
      path: "/about",
      name: "about",
      component: () => import("@/views/AboutView.vue"),
      meta: {
        title: () => "Rolunk",
        breadcrumb: "Rolunk",
      },
    },
    {
      path: "/reviews",
      name: "reviews",
      component: () => import("@/views/ReviewsView.vue"),
      meta: {
        title: () => "Reviews",
        breadcrumb: "Reviews",
      },
    },
    {
      path: "/adatok",
      name: "adatok",
      component: () => import("@/views/EmptyWrapperView.vue"),
      meta: {
        breadcrumb: "Adatok",
        disabled: true,
      },
      children: [
        {
          path: "categories",
          name: "categories",
          component: () => import("@/views/CategoriesView.vue"),
          meta: {
            title: () => "Categories",
            breadcrumb: "Categories",
          },
        },
        {
          path: "brands",
          name: "brands",
          component: () => import("@/views/BrandsView.vue"),
          meta: {
            title: () => "Brands",
            breadcrumb: "Brands",
          },
        },
        {
          path: "sport",
          redirect: "/adatok/categories",
        },
        {
          path: "schoolclass",
          redirect: "/adatok/brands",
        },
        {
          path: "users",
          name: "users",
          component: () => import("@/views/UsersView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: {
            title: () => "Felhasznalok",
            breadcrumb: "Felhasznalok",
            roles: [1],
          },
        },
        {
          path: "products",
          name: "products-admin",
          redirect: "/userprofil/products",
          beforeEnter: [checkIfNotLogged],
          meta: {
            title: () => "Products",
            breadcrumb: "Products",
            roles: [1],
          },
        },
        {
          path: "comments",
          name: "comments-admin",
          redirect: "/userprofil/comments",
          beforeEnter: [checkIfNotLogged],
          meta: {
            title: () => "Comments",
            breadcrumb: "Comments",
            roles: [1],
          },
        },
      ],
    },
    {
      path: "/login",
      name: "login",
      component: () => import("@/views/LoginView.vue"),
      meta: {
        title: () => "Login",
        breadcrumb: "Login",
      },
    },
    {
      path: "/registration",
      name: "registration",
      component: () => import("@/views/RegistrationView.vue"),
      meta: {
        title: () => "Regisztracio",
        breadcrumb: "Regisztracio",
      },
    },
    {
      path: "/userprofil",
      name: "user-profile",
      component: () => import("@/views/UserProfileView.vue"),
      beforeEnter: [checkIfNotLogged],
      meta: {
        title: () => "User Profile",
        breadcrumb: "User Profile",
        roles: [1, 2, 3],
      },
      children: [
        {
          path: "products",
          name: "user-profile-products-admin",
          component: () => import("@/views/ProductsAdminView.vue"),
          meta: {
            title: () => "Admin Products",
            breadcrumb: "Products",
            roles: [1],
          },
        },
        {
          path: "comments",
          name: "user-profile-comments-admin",
          component: () => import("@/views/CommentsModerationView.vue"),
          meta: {
            title: () => "Admin Comments",
            breadcrumb: "Comments",
            roles: [1],
          },
        },
      ],
    },
    {
      path: "/cart",
      name: "cart",
      component: () => import("@/views/CartView.vue"),
      beforeEnter: [checkIfNotLogged],
      meta: {
        title: () => "Cart",
        breadcrumb: "Cart",
        roles: [1, 2, 3],
      },
    },
    {
      path: "/products/:id",
      name: "product-detail",
      component: () => import("@/views/ProductDetailView.vue"),
      meta: {
        title: () => "Product Details",
        breadcrumb: "Product",
      },
    },
    {
      path: "/:pathMatch(.*)*",
      name: "NotFound",
      component: () => import("@/views/404.vue"),
      meta: {
        title: () => "404",
        breadcrumb: "",
      },
    },
  ],
});

router.beforeEach((to, from, next) => {
  document.title = "Webshop - " + to.meta.title(to);

  const requiredRoles = to.meta.roles;
  const userStore = useUserLoginLogoutStore();

  if (userStore.canAccess(requiredRoles)) {
    next();
  } else {
    if (!userStore.isLoggedIn) {
      next({ path: "/login" });
    } else {
      useToastStore().messages.push("Ehhez az oldalhoz nincs jogod!");
      useToastStore().show("Error");
      next("/");
    }
  }
});

export default router;
