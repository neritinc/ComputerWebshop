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
                @click="onCatalogToggleClick"
              >
                Catalog
              </a>
              <div
                class="dropdown-menu nav-dropdown"
                @mousedown.stop
                @click.stop
              >
                <div class="catalog-grid">
                  <section class="catalog-section manage-panel">
                    <h6 class="catalog-title">Manage</h6>
                    <button
                      v-if="canOpenCategories"
                      type="button"
                      class="dropdown-item quick-item text-start manage-item"
                      :class="{ active: catalogPanel === 'categories' }"
                      @click.stop="openCatalogPanel('categories')"
                    >
                      Categories
                    </button>
                    <button
                      v-if="canOpenBrands"
                      type="button"
                      class="dropdown-item quick-item text-start manage-item"
                      :class="{ active: catalogPanel === 'brands' }"
                      @click.stop="openCatalogPanel('brands')"
                    >
                      Brands
                    </button>
                    <RouterLink
                      v-if="catalogPanel"
                      class="btn btn-outline-primary btn-sm mt-2"
                      :to="catalogPanel === 'brands' ? '/adatok/brands' : '/adatok/categories'"
                    >
                      Open {{ catalogPanel === "brands" ? "Brands" : "Categories" }} Page
                    </RouterLink>
                  </section>

                  <section class="catalog-section content-panel">
                    <h6 class="catalog-title">{{ catalogPanelTitle }}</h6>

                    <template v-if="catalogPanel === 'categories'">
                      <div
                        v-for="section in categorySections"
                        :key="section.key"
                        class="content-subsection"
                      >
                        <h6 class="catalog-subtitle">{{ section.title }}</h6>
                        <div v-if="categoriesLoading" class="dropdown-item-text px-2 py-1">Loading categories...</div>
                        <div v-else-if="section.items.length === 0" class="dropdown-item-text px-2 py-1">{{ section.emptyText }}</div>
                        <ul v-else class="catalog-list">
                          <li v-for="category in section.items" :key="`${section.key}-${category.id}`">
                            <RouterLink class="dropdown-item category-item-clean" :to="{ path: '/adatok/categories', query: { category: category.id } }">
                              <i class="bi me-2" :class="categoryIconClass(category.categoryName)"></i>
                              {{ category.categoryName }}
                            </RouterLink>
                          </li>
                        </ul>
                      </div>
                    </template>

                    <template v-else-if="catalogPanel === 'brands'">
                      <div v-if="brandsLoading" class="dropdown-item-text px-2 py-1">Loading brands...</div>
                      <div v-else-if="sortedBrands.length === 0" class="dropdown-item-text px-2 py-1">No brands found</div>
                      <ul v-else class="catalog-list">
                        <li v-for="brand in sortedBrands" :key="`brand-${brand.id}`">
                          <RouterLink class="dropdown-item brand-item-clean" :to="{ path: '/adatok/brands', query: { brand: brand.id } }">
                            {{ brand.brandName }}
                          </RouterLink>
                        </li>
                      </ul>
                    </template>

                    <div v-else class="dropdown-item-text px-2 py-1">
                      Select Categories or Brands on the left.
                    </div>
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
                  @click="onClickLogout()"
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
import productService from "@/api/productService";
import {
  ACCESSORY_CATEGORY_KEYS,
  COMPONENT_CATEGORY_KEYS,
  normalizeCategoryKey,
  resolveCategoryIconClass,
} from "@/utils/categoryMeta";

export default {
  data() {
    return {
      searchWordInput: "",
      dbCategories: [],
      categoriesLoading: false,
      categoryIdsWithProducts: new Set(),
      dbBrands: [],
      brandsLoading: false,
      catalogPanel: null,
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
    canOpenCategories() {
      return this.hasMenuAccess("/adatok/categories");
    },
    canOpenBrands() {
      return this.hasMenuAccess("/adatok/brands");
    },
    categoriesWithProducts() {
      return this.dbCategories.filter((category) =>
        this.categoryIdsWithProducts.has(Number(category.id)),
      );
    },
    hardwareCategories() {
      return this.categoriesWithProducts.filter((category) =>
        COMPONENT_CATEGORY_KEYS.includes(normalizeCategoryKey(category.categoryName)),
      );
    },
    accessoryCategories() {
      const accessory = this.categoriesWithProducts.filter((category) =>
        ACCESSORY_CATEGORY_KEYS.includes(normalizeCategoryKey(category.categoryName)),
      );

      const mappedIds = new Set([
        ...this.hardwareCategories.map((c) => c.id),
        ...accessory.map((c) => c.id),
      ]);
      const unmapped = this.categoriesWithProducts.filter((category) => !mappedIds.has(category.id));
      return [...accessory, ...unmapped];
    },
    categorySections() {
      return [
        {
          key: "hw",
          title: "PC Components",
          emptyText: "No component categories",
          items: this.hardwareCategories,
        },
        {
          key: "acc",
          title: "Monitors & Accessories",
          emptyText: "No accessory categories",
          items: this.accessoryCategories,
        },
      ];
    },
    sortedBrands() {
      return [...this.dbBrands]
        .sort((a, b) => String(a.brandName).localeCompare(String(b.brandName), "en"))
        .slice(0, 200);
    },
    catalogPanelTitle() {
      if (this.catalogPanel === "brands") return "Manufacturers";
      if (this.catalogPanel === "categories") return "Catalog Categories";
      return "Catalog";
    },
  },
  methods: {
    ...mapActions(useSearchStore, ["resetSearchWord", "setSearchWord"]),
    onCatalogToggleClick() {
      this.catalogPanel = null;
    },
    async openCatalogPanel(panel) {
      this.catalogPanel = panel;
      if (panel === "categories") {
        await this.loadDbCategories();
      } else if (panel === "brands" && this.canOpenBrands) {
        await this.loadDbBrands();
      }
    },
    onClickSearchButton() {
      this.setSearchWord(this.searchWordInput);
    },
    async loadDbCategories() {
      if (this.categoriesLoading) return;
      try {
        this.categoriesLoading = true;
        const [categoriesResponse, productsResponse] = await Promise.all([
          categoryService.getAll(),
          productService.getAll(),
        ]);
        this.dbCategories = categoriesResponse.data || [];
        const products = productsResponse.data || [];
        this.categoryIdsWithProducts = new Set(
          products
            .map((product) => Number(product?.category_id))
            .filter((id) => Number.isFinite(id)),
        );
      } catch (error) {
        this.dbCategories = [];
        this.categoryIdsWithProducts = new Set();
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
    categoryIconClass(categoryName) {
      return resolveCategoryIconClass(categoryName);
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
    async onClickLogout() {
      try {
        await this.logout();
        this.$router.push("/");
      } catch (error) {
        console.log("Logout error");
      }
    },
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
  width: min(760px, calc(100vw - 24px));
  min-width: 0;
  max-width: calc(100vw - 24px);
  padding: 12px;
  background: #ffffff;
  left: 0;
  right: auto;
  user-select: text;
}

.catalog-grid {
  display: grid;
  grid-template-columns: 250px minmax(0, 1fr);
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

.catalog-subtitle {
  margin: 0 0 8px;
  font-size: 0.76rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
  font-weight: 700;
}

.manage-panel {
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

.manage-item {
  border: 1px solid #e2e8f0;
  background: #ffffff;
  font-weight: 600;
  margin-bottom: 6px;
}

.manage-item:hover {
  background: #eef4ff;
}

.manage-item.active {
  background: #e6efff;
  border-color: #bfd4f3;
  color: #1d4ed8;
}

.content-panel {
  min-height: 320px;
}

.content-subsection + .content-subsection {
  margin-top: 12px;
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
  margin-bottom: 0;
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
  font-size: 1.6rem;
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
    width: 100%;
    max-width: 100%;
    left: 0;
    right: auto;
  }

  .catalog-grid {
    grid-template-columns: 1fr;
  }
}
</style>
