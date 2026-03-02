<template>
  <div>
    <nav class="navbar navbar-expand-md bg-primary" data-bs-theme="dark">
      <div class="container-fluid">
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <RouterLink class="nav-link" to="/">Fooldal</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/about">Rolunk</RouterLink>
            </li>
            <li class="nav-item dropdown" v-if="hasMenuAccess('/adatok')">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                Adatok
              </a>
              <ul class="dropdown-menu">
                <li v-if="hasMenuAccess('/adatok/sport')">
                  <RouterLink class="dropdown-item" to="/adatok/sport"
                    >Kategoriak</RouterLink
                  >
                </li>
                <li v-if="hasMenuAccess('/adatok/schoolclass')">
                  <RouterLink class="dropdown-item" to="/adatok/schoolclass"
                    >Gyartok</RouterLink
                  >
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li v-if="hasMenuAccess('/adatok/users')">
                  <RouterLink class="dropdown-item" to="/adatok/users"
                    >Felhasznalok</RouterLink
                  >
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/login" v-if="!isLoggedIn">
                Login
              </RouterLink>
              <div v-if="isLoggedIn" class="d-flex align-items-center">
                <RouterLink class="nav-link" to="/userprofil">
                  <i class="bi bi-person"></i>
                  {{ userNameWithRole }}
                </RouterLink>

                <i
                  class="bi bi-box-arrow-right ms-2 my-pointer tight-icon"
                  style="font-size: 2rem"
                  @click="onClickLogut()"
                ></i>
              </div>
            </li>
          </ul>
          <form class="d-flex" role="search">
            <input
              id="search"
              class="form-control me-2"
              type="search"
              placeholder="Search"
              aria-label="Search"
              v-model="searchWordInput"
            />

            <label for="search" class="form-label m-0">
              <i
                @click="onClickSearchButton"
                class="bi bi-search fs-4 my-pointer"
              ></i>
            </label>
          </form>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useSearchStore } from "@/stores/searchStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  data() {
    return {
      searchWordInput: "",
      timeout: null,
    };
  },
  watch: {
    searchWordInput(value) {
      if (!value) {
        this.resetSearchWord();
      }
    },
    searchWord(value) {
      this.searchWordInput = value;
    },
  },
  computed: {
    ...mapState(useSearchStore, ["searchWord"]),
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "userNameWithRole"]),
  },
  methods: {
    ...mapActions(useSearchStore, ["resetSearchWord", "setSearchWord"]),
    onClickSearchButton() {
      this.setSearchWord(this.searchWordInput);
    },
    ...mapActions(useUserLoginLogoutStore, ["logout"]),
    hasMenuAccess(targetPath) {
      const userStore = useUserLoginLogoutStore();
      const resolved = this.$router.resolve(targetPath);

      if (!resolved || !resolved.matched.length) return false;

      return resolved.matched.every((route) => {
        const requiredRoles = route.meta?.roles;
        return userStore.canAccess(requiredRoles);
      });
    },
    async onClickLogut() {
      try {
        await this.logout();
        this.$router.push("/");
      } catch (error) {
        console.log("Kijelentkezesi hiba!");
      }
    },
  },
};
</script>

<style scoped>
.nav-link.active,
.nav-link.router-link-exact-active {
  color: #ffff00 !important;
  font-weight: bold;
  border-bottom: 2px solid yellow;
}

.nav-item:has(.dropdown-item.router-link-active) .nav-link.dropdown-toggle {
  color: #ffff00 !important;
  font-weight: bold;
  border-bottom: 2px solid yellow;
}

.dropdown-item.router-link-active {
  background-color: transparent !important;
  color: #ffff00 !important;
  font-weight: bold;
}

.tight-icon {
  line-height: 1 !important;
  display: inline-flex;
  vertical-align: middle;
}

.navbar {
  position: relative;
  z-index: 1060 !important;
}

.dropdown-menu {
  z-index: 1060 !important;
}
</style>
