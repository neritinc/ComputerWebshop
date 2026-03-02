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

      <div class="table-responsive" v-if="filteredProducts.length > 0">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Product</th>
              <th>Brand</th>
              <th>Price</th>
              <th>Stock</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in filteredProducts" :key="product.id">
              <td>{{ product.id }}</td>
              <td>{{ product.name }}</td>
              <td>{{ product?.company?.company_name || "-" }}</td>
              <td>{{ product.price }}</td>
              <td>{{ product.pcs }}</td>
            </tr>
          </tbody>
        </table>
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

export default {
  name: "CategoriesView",
  data() {
    return {
      categoriesLoading: false,
      categories: [],
      productsLoading: false,
      products: [],
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
        this.selectedCategoryName = "";
        return;
      }

      try {
        this.productsLoading = true;
        const response = await productService.getAll({ category_id: categoryId });
        this.products = response.data || [];
        this.selectedCategoryName = this.products[0]?.category?.category_name || "";
      } catch (error) {
        this.products = [];
        this.selectedCategoryName = "";
      } finally {
        this.productsLoading = false;
      }
    },
    openCategory(category) {
      this.$router.push({ path: "/adatok/categories", query: { category: category.id } });
    },
    goToAllCategories() {
      this.$router.push({ path: "/adatok/categories" });
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
</style>
