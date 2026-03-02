<template>
  <div>
    <nav class="navbar navbar-expand-md nav-modern" data-bs-theme="light">
      <div class="container-fluid">
        <button
          class="navbar-toggler nav-toggler"
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
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-links-wrap">
            <li class="nav-item">
              <RouterLink class="nav-link" to="/">Home</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/about">About Us</RouterLink>
            </li>
            <li class="nav-item dropdown" v-if="hasMenuAccess('/adatok')">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                aria-expanded="false"
                @click="loadCatalogData"
              >
                Catalog
              </a>
              <div class="dropdown-menu nav-dropdown">
                <div class="catalog-grid">
                  <section class="catalog-section">
                    <h6 class="catalog-title">Manage</h6>
                    <RouterLink v-if="hasMenuAccess('/adatok/categories')" class="dropdown-item quick-item" to="/adatok/categories">
                      Categories
                    </RouterLink>
                    <RouterLink v-if="hasMenuAccess('/adatok/brands')" class="dropdown-item quick-item" to="/adatok/brands">
                      Brands
                    </RouterLink>
                  </section>

                  <section class="catalog-section" v-if="catalogMode === 'categories'">
                    <h6 class="catalog-title">PC Components</h6>
                    <div v-if="categoriesLoading" class="dropdown-item-text px-2 py-1">Loading categories...</div>
                    <div v-else-if="hardwareCategories.length === 0" class="dropdown-item-text px-2 py-1">No component categories</div>
                    <ul v-else class="catalog-list">
                      <li v-for="category in hardwareCategories" :key="`hw-${category.id}`">
                        <RouterLink class="dropdown-item category-item-clean" :to="{ path: '/adatok/categories', query: { category: category.id } }">
                          <i class="bi me-2" :class="categoryIconClass(category.categoryName)"></i>
                          {{ category.categoryName }}
                        </RouterLink>
                      </li>
                    </ul>
                  </section>

                  <section class="catalog-section" v-if="catalogMode === 'categories'">
                    <h6 class="catalog-title">Monitors & Accessories</h6>
                    <div v-if="categoriesLoading" class="dropdown-item-text px-2 py-1">Loading categories...</div>
                    <div v-else-if="accessoryCategories.length === 0" class="dropdown-item-text px-2 py-1">No accessory categories</div>
                    <ul v-else class="catalog-list">
                      <li v-for="category in accessoryCategories" :key="`acc-${category.id}`">
                        <RouterLink class="dropdown-item category-item-clean" :to="{ path: '/adatok/categories', query: { category: category.id } }">
                          <i class="bi me-2" :class="categoryIconClass(category.categoryName)"></i>
                          {{ category.categoryName }}
                        </RouterLink>
                      </li>
                    </ul>
                  </section>

                  <section class="catalog-section" v-if="catalogMode === 'brands'">
                    <h6 class="catalog-title">Manufacturers</h6>
                    <div v-if="brandsLoading" class="dropdown-item-text px-2 py-1">Loading brands...</div>
                    <div v-else-if="sortedBrands.length === 0" class="dropdown-item-text px-2 py-1">No brands found</div>
                    <ul v-else class="catalog-list">
                      <li v-for="brand in sortedBrands" :key="`brand-${brand.id}`">
                        <RouterLink class="dropdown-item brand-item-clean" :to="{ path: '/adatok/brands', query: { brand: brand.id } }">
                          {{ brand.brandName }}
                        </RouterLink>
                      </li>
                    </ul>
                  </section>

                  <section class="catalog-section">
                    <h6 class="catalog-title">Quick Help</h6>
                    <div class="dropdown-item-text px-2 py-1">Choose a category to browse products.</div>
                    <div class="dropdown-item-text px-2 py-1">Open Brands to filter by manufacturer.</div>
                    <div class="dropdown-item-text px-2 py-1">Use Search to find items faster.</div>
                  </section>
                </div>
              </div>
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
                  style="font-size: 1.6rem"
                  @click="onClickLogut()"
                ></i>
              </div>
            </li>
          </ul>
          <form class="d-flex nav-search" role="search">
            <input
              id="search"
              class="form-control me-2 nav-search-input"
              type="search"
              placeholder="Search"
              aria-label="Search"
              v-model="searchWordInput"
            />

            <label for="search" class="form-label m-0">
              <i
                @click="onClickSearchButton"
                class="bi bi-search fs-5 my-pointer nav-search-icon"
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
import categoryService from "@/api/categoryService";
import brandService from "@/api/brandService";

export default {
  data() {
    return {
      searchWordInput: "",
      timeout: null,
      dbCategories: [],
      categoriesLoading: false,
      dbBrands: [],
      brandsLoading: false,
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
    hardwareCategories() {
      const normalize = (value) =>
        String(value || "")
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .trim();

      // Explicit grouping based on current DB category list.
      const componentNames = [
        "processor",
        "memory module",
        "motherboard",
        "graphics card",
        "storage",
        "power supply",
        "cooling",
        "case",
        "optical drive",
        "network card",
        "sound card",
        "usb hub",
        "case fan",
        "memory",
        "cooler",
        "computer case",
      ];

      return this.dbCategories.filter((category) => {
        const name = normalize(category.categoryName);
        return componentNames.includes(name);
      });
    },
    accessoryCategories() {
      const normalize = (value) =>
        String(value || "")
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .trim();

      const accessoryNames = [
        "monitor",
        "keyboard",
        "mouse",
        "headset",
        "speaker",
        "webcam",
        "microphone",
        "external storage",
      ];

      const accessory = this.dbCategories.filter((category) =>
        accessoryNames.includes(normalize(category.categoryName)),
      );

      // If a new category appears in DB and is not yet mapped, show it here by default.
      const mappedIds = new Set([
        ...this.hardwareCategories.map((c) => c.id),
        ...accessory.map((c) => c.id),
      ]);
      const unmapped = this.dbCategories.filter((category) => !mappedIds.has(category.id));
      return [...accessory, ...unmapped];
    },
    sortedBrands() {
      return [...this.dbBrands]
        .sort((a, b) => String(a.brandName).localeCompare(String(b.brandName), "en"))
        .slice(0, 200);
    },
    catalogMode() {
      if (this.$route?.name === "brands") {
        return "brands";
      }
      return "categories";
    },
  },
  methods: {
    ...mapActions(useSearchStore, ["resetSearchWord", "setSearchWord"]),
    onClickSearchButton() {
      this.setSearchWord(this.searchWordInput);
    },
    async loadDbCategories() {
      if (this.categoriesLoading) return;
      try {
        this.categoriesLoading = true;
        const response = await categoryService.getAll();
        this.dbCategories = response.data || [];
      } catch (error) {
        this.dbCategories = [];
      } finally {
        this.categoriesLoading = false;
      }
    },
    async loadDbBrands() {
      if (this.brandsLoading) return;
      try {
        this.brandsLoading = true;
        const response = await brandService.getAll();
        this.dbBrands = response.data || [];
      } catch (error) {
        this.dbBrands = [];
      } finally {
        this.brandsLoading = false;
      }
    },
    async loadCatalogData() {
      await Promise.all([this.loadDbCategories(), this.loadDbBrands()]);
    },
    categoryIconClass(categoryName) {
      const key = String(categoryName || "").toLowerCase();
      const iconMap = {
        processor: "bi-cpu",
        "memory module": "bi-memory",
        memory: "bi-memory",
        motherboard: "bi-motherboard",
        "graphics card": "bi-gpu-card",
        storage: "bi-device-ssd",
        "external storage": "bi-device-hdd",
        "power supply": "bi-plug",
        cooling: "bi-fan",
        cooler: "bi-snow",
        case: "bi-pc",
        "computer case": "bi-pc-display",
        "case fan": "bi-fan",
        "optical drive": "bi-disc",
        "network card": "bi-router",
        "sound card": "bi-music-note-beamed",
        "usb hub": "bi-usb-symbol",
        monitor: "bi-display",
        keyboard: "bi-keyboard",
        mouse: "bi-mouse",
        headset: "bi-headphones",
        speaker: "bi-speaker",
        webcam: "bi-camera-video",
        microphone: "bi-mic",
      };

      return iconMap[key] || "bi-tag";
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
        console.log("Logout error");
      }
    },
  },
  async mounted() {
    await this.loadCatalogData();
  },
};
</script>

<style scoped>
.nav-modern {
  border-radius: 14px;
  margin: 6px 0 10px;
  padding: 8px 10px;
  background: linear-gradient(135deg, #ffffff 0%, #f3f8ff 100%);
  border: 1px solid #d8e5f7;
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.08);
}

.nav-links-wrap .nav-link {
  color: #0f172a;
  font-weight: 600;
  border-radius: 10px;
  padding: 8px 12px !important;
  transition: all 0.2s ease;
}

.nav-links-wrap .nav-link:hover {
  background: #eaf2ff;
  color: #1d4ed8;
}

.nav-link.active,
.nav-link.router-link-exact-active {
  color: #1d4ed8 !important;
  font-weight: 700;
  background: #e6efff;
  border-bottom: none;
}

.nav-item:has(.dropdown-item.router-link-active) .nav-link.dropdown-toggle {
  color: #1d4ed8 !important;
  font-weight: 700;
  background: #e6efff;
  border-bottom: none;
}

.dropdown-item.router-link-active {
  background-color: #eaf2ff !important;
  color: #1d4ed8 !important;
  font-weight: 700;
}

.nav-dropdown {
  border-radius: 14px;
  border: 1px solid #d8e5f7;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
  min-width: 760px;
  padding: 12px;
  background: #ffffff;
}

.catalog-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px 18px;
}

.catalog-section {
  border: 1px solid #e8eef9;
  border-radius: 10px;
  padding: 10px;
  background: #fcfdff;
}

.catalog-title {
  margin: 0 0 8px;
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #475569;
  font-weight: 700;
}

.catalog-list {
  margin: 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 3px;
  max-height: 220px;
  overflow: auto;
}

.category-item-clean {
  border-radius: 8px;
  padding: 7px 10px;
  font-size: 0.92rem;
  color: #0f172a;
}

.category-item-clean:hover {
  background: #eef4ff;
  color: #1d4ed8;
}

.brand-item-clean {
  border-radius: 8px;
  padding: 7px 10px;
  font-size: 0.92rem;
  color: #0f172a;
}

.brand-item-clean:hover {
  background: #eef4ff;
  color: #1d4ed8;
}

.quick-item {
  border-radius: 8px;
  margin-bottom: 3px;
  font-size: 0.92rem;
}

.nav-search {
  align-items: center;
}

.nav-search-input {
  border-radius: 999px;
  border: 1px solid #c9daef;
  background: #f8fbff;
  min-width: 210px;
}

.nav-search-input:focus {
  border-color: #60a5fa;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.18);
}

.nav-search-icon {
  color: #2563eb;
}

.nav-toggler {
  border: 1px solid #c9daef;
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

@media (max-width: 767px) {
  .nav-search-input {
    min-width: 100%;
  }

  .nav-dropdown {
    min-width: 100%;
  }

  .catalog-grid {
    grid-template-columns: 1fr;
  }
}
</style>
