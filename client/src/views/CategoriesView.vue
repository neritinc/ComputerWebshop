<template>
  <div>
    <div v-if="isProductMode">
      <div class="d-flex align-items-center justify-content-between m-0 mb-2">
        <div class="d-flex align-items-center">
          <h1>{{ productModeTitle }}</h1>
          <div class="d-flex align-items-center m-0 ms-2">
            <i
              v-if="productsLoading"
              class="bi bi-hourglass-split fs-3 col-auto p-0 pe-1"
            ></i>
            <p class="m-0 ms-2">({{ filteredProducts.length }})</p>
          </div>
        </div>
        <button class="btn btn-outline-primary btn-sm" @click="goToAllCategories">
          Back to Categories
        </button>
      </div>

      <div v-if="filteredProducts.length > 0" class="row g-3">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="col-12 col-md-6 col-xl-4"
        >
          <article class="product-card h-100">
            <div class="product-image-wrap">
              <img
                v-if="productImageUrl(product) && !imageFailedFor(product.id)"
                class="product-image"
                :src="productImageUrl(product)"
                :alt="product.name"
                @error="markImageFailed(product.id)"
              />
              <div v-else class="product-image-placeholder">
                <i class="bi bi-image"></i>
              </div>
            </div>

            <div class="product-card-top">
              <span class="product-id">#{{ product.id }}</span>
              <span class="stock-pill" :class="stockClass(product.pcs)">
                {{ stockLabel(product.pcs) }}
              </span>
            </div>

            <h5 class="product-name">{{ product.name }}</h5>

            <p class="product-brand m-0">
              {{ product?.company?.company_name || "Unknown brand" }}
            </p>

            <div class="product-price">
              {{ formatPrice(product.price) }}
            </div>
          </article>
        </div>
      </div>
      <div v-else class="m-auto" style="width: 180px">No products found</div>
    </div>

    <div v-else>
      <div class="d-flex align-items-center m-0 mb-2">
        <h1>Categories</h1>
        <div class="d-flex align-items-center m-0 ms-2">
          <i
            v-if="categoriesLoading"
            class="bi bi-hourglass-split fs-3 col-auto p-0 pe-1"
          ></i>
          <p class="m-0 ms-2">({{ filteredCategories.length }})</p>
        </div>
      </div>

      <div v-if="filteredCategories.length > 0" class="row g-2">
        <div
          v-for="category in filteredCategories"
          :key="category.id"
          class="col-12 col-md-6 col-lg-4"
        >
          <button class="category-tile" @click="openCategory(category)">
            <i class="bi fs-5" :class="categoryIconClass(category.categoryName)"></i>
            <span>{{ category.categoryName }}</span>
          </button>
        </div>
      </div>
      <div v-else class="m-auto" style="width: 150px">No categories found</div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useSearchStore } from "@/stores/searchStore";
import categoryService from "@/api/categoryService";
import productService from "@/api/productService";

const CATEGORY_ICON_MAP = {
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

export default {
  name: "CategoriesView",
  data() {
    return {
      categoriesLoading: false,
      categories: [],
      productsLoading: false,
      products: [],
      failedImageProductIds: {},
      selectedCategoryName: "",
    };
  },
  watch: {
    "$route.query.category": {
      immediate: true,
      async handler() {
        await this.loadProductsByRouteCategory();
      },
    },
  },
  computed: {
    ...mapState(useSearchStore, ["searchWord"]),
    isProductMode() {
      return Boolean(this.$route.query.category);
    },
    productModeTitle() {
      return this.selectedCategoryName
        ? `${this.selectedCategoryName} Products`
        : "Category Products";
    },
    filteredCategories() {
      const search = String(this.searchWord || "").trim().toLowerCase();
      if (!search) return this.categories;
      return this.categories.filter((c) =>
        String(c.categoryName || "").toLowerCase().includes(search),
      );
    },
    filteredProducts() {
      const search = String(this.searchWord || "").trim().toLowerCase();
      if (!search) return this.products;
      return this.products.filter((item) => {
        return [item.name, item?.company?.company_name, item.price, item.pcs]
          .map((v) => String(v ?? "").toLowerCase())
          .some((v) => v.includes(search));
      });
    },
  },
  methods: {
    ...mapActions(useSearchStore, ["resetSearchWord"]),
    async loadCategories() {
      if (this.categoriesLoading) return;
      try {
        this.categoriesLoading = true;
        const response = await categoryService.getAll();
        this.categories = response.data || [];
      } catch (error) {
        this.categories = [];
      } finally {
        this.categoriesLoading = false;
      }
    },
    async loadProductsByRouteCategory() {
      const categoryId = this.$route.query.category;
      if (!categoryId) {
        this.products = [];
        this.failedImageProductIds = {};
        this.selectedCategoryName = "";
        return;
      }

      try {
        this.productsLoading = true;
        const response = await productService.getAll({ category_id: categoryId });
        this.products = response.data || [];
        this.failedImageProductIds = {};
        this.selectedCategoryName = this.products[0]?.category?.category_name || "";
      } catch (error) {
        this.products = [];
        this.failedImageProductIds = {};
        this.selectedCategoryName = "";
      } finally {
        this.productsLoading = false;
      }
    },
    productImageUrl(product) {
      if (product?.primary_image_url) {
        return String(product.primary_image_url);
      }

      if (product?.primary_image_path) {
        const apiBase = String(import.meta.env.VITE_API_URL || "").replace(/\/api\/?$/, "");
        return `${apiBase}/images/products/${product.primary_image_path}`;
      }

      const pics = Array.isArray(product?.pics) ? product.pics : [];
      const sorted = [...pics].sort((a, b) => {
        const aPath = String(a?.image_path || "");
        const bPath = String(b?.image_path || "");
        const aPrimary = /_1\.[a-z0-9]+$/i.test(aPath) ? 0 : 1;
        const bPrimary = /_1\.[a-z0-9]+$/i.test(bPath) ? 0 : 1;
        if (aPrimary !== bPrimary) return aPrimary - bPrimary;
        return aPath.localeCompare(bPath);
      });
      const fileName = sorted[0]?.image_path || "";
      if (!fileName) return "";
      const apiBase = String(import.meta.env.VITE_API_URL || "").replace(/\/api\/?$/, "");
      return `${apiBase}/images/products/${fileName}`;
    },
    markImageFailed(productId) {
      const id = Number(productId);
      this.failedImageProductIds[id] = true;
    },
    imageFailedFor(productId) {
      return Boolean(this.failedImageProductIds[Number(productId)]);
    },
    openCategory(category) {
      this.$router.push({ path: "/adatok/categories", query: { category: category.id } });
    },
    goToAllCategories() {
      this.$router.push({ path: "/adatok/categories" });
    },
    categoryIconClass(categoryName) {
      return CATEGORY_ICON_MAP[String(categoryName || "").toLowerCase()] || "bi-tag";
    },
    formatPrice(value) {
      const numeric = Number(value);
      if (Number.isNaN(numeric)) return String(value ?? "-");
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        maximumFractionDigits: 2,
      }).format(numeric);
    },
    stockMeta(stockValue) {
      const stock = Number(stockValue);
      if (stock <= 0) return { className: "stock-out", label: "Out of stock" };
      if (stock < 5) return { className: "stock-low", label: `Low stock (${stock})` };
      return { className: "stock-ok", label: `In stock (${stock})` };
    },
    stockClass(stockValue) {
      return this.stockMeta(stockValue).className;
    },
    stockLabel(stockValue) {
      return this.stockMeta(stockValue).label;
    },
  },
  async mounted() {
    this.resetSearchWord();
    await this.loadCategories();
  },
};
</script>

<style scoped>
.category-tile {
  width: 100%;
  border: 1px solid #d8e5f7;
  border-radius: 10px;
  background: #f8fbff;
  color: #0f172a;
  padding: 10px 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  text-align: left;
}

.category-tile:hover {
  background: #eef4ff;
  border-color: #bfd4f3;
}

.product-card {
  border: 1px solid #d8e5f7;
  border-radius: 12px;
  background: #ffffff;
  padding: 14px;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.product-image-wrap {
  width: 100%;
  aspect-ratio: 16 / 10;
  border-radius: 10px;
  overflow: hidden;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  /* transform: scale(1.3AOC 27G4X); */
  display: block;
}

.product-image-placeholder {
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
  color: #94a3b8;
  font-size: 1.6rem;
}

.product-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.product-id {
  color: #64748b;
  font-size: 0.82rem;
  font-weight: 600;
}

.product-name {
  margin: 0;
  color: #0f172a;
  font-size: 1.02rem;
  line-height: 1.35;
}

.product-brand {
  color: #475569;
  font-size: 0.9rem;
}

.product-price {
  margin-top: auto;
  color: #1d4ed8;
  font-weight: 700;
  font-size: 1.05rem;
}

.stock-pill {
  border-radius: 999px;
  padding: 3px 10px;
  font-size: 0.76rem;
  font-weight: 700;
}

.stock-ok {
  background: #dcfce7;
  color: #166534;
}

.stock-low {
  background: #fef3c7;
  color: #92400e;
}

.stock-out {
  background: #fee2e2;
  color: #991b1b;
}
</style>
