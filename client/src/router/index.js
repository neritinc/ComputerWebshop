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
      path: "/adatok",
      name: "adatok",
      component: () => import("@/views/EmptyWrapperView.vue"),
      meta: {
        breadcrumb: "Adatok",
        disabled: true,
        roles: [1, 2],
      },
      children: [
        {
          path: "sport",
          name: "sport",
          component: () => import("@/views/SportView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: {
            title: () => "Kategoriak",
            breadcrumb: "Kategoriak",
            roles: [1],
          },
        },
        {
          path: "schoolclass",
          name: "schoolclass",
          component: () => import("@/views/SchoolClasssView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: {
            title: () => "Gyartok",
            breadcrumb: "Gyartok",
            roles: [1],
          },
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
