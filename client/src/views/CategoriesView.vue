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
          <article class="product-card h-100" @click="openProduct(product)">
            <div class="product-image-wrap">
              <img
                v-if="currentProductImage(product) && !imageFailedFor(product.id)"
                class="product-image"
                :src="currentProductImage(product)"
                :alt="product.name"
                @error="onProductImageError(product)"
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

            <div class="product-bottom-row">
              <div class="product-price">
                <UsdPrice :value="product.price" />
              </div>
              <button class="product-add-btn" @click.stop="onAddToCart(product)">
                <i class="bi bi-bag-plus me-1"></i>
                Add to cart
              </button>
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
import { useCartStore } from "@/stores/cartStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";
import categoryService from "@/api/categoryService";
import productService from "@/api/productService";
import { resolveCategoryIconClass } from "@/utils/categoryMeta";
import { getProductImageCandidates } from "@/utils/image";
import UsdPrice from "@/components/Common/UsdPrice.vue";

export default {
  name: "CategoriesView",
  components: { UsdPrice },
  data() {
    return {
      categoriesLoading: false,
      categories: [],
      productsLoading: false,
      products: [],
      failedImageProductIds: {},
      productImageIndexes: {},
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
        this.resetProductModeState();
        return;
      }

      try {
        this.productsLoading = true;
        const response = await productService.getAll({ category_id: categoryId });
        this.products = response.data || [];
        this.failedImageProductIds = {};
        this.productImageIndexes = {};
        this.selectedCategoryName = this.products[0]?.category?.category_name || "";
      } catch (error) {
        this.resetProductModeState();
      } finally {
        this.productsLoading = false;
      }
    },
    resetProductModeState() {
      this.products = [];
      this.failedImageProductIds = {};
      this.productImageIndexes = {};
      this.selectedCategoryName = "";
    },
    firstPicPath(product) {
      const pics = Array.isArray(product?.pics) ? product.pics : [];
      const sorted = [...pics].sort((a, b) => {
        const aPath = String(a?.image_path || "");
        const bPath = String(b?.image_path || "");
        const aPrimary = /_1\.[a-z0-9]+$/i.test(aPath) ? 0 : 1;
        const bPrimary = /_1\.[a-z0-9]+$/i.test(bPath) ? 0 : 1;
        if (aPrimary !== bPrimary) return aPrimary - bPrimary;
        return aPath.localeCompare(bPath);
      });
      return sorted[0]?.image_path || "";
    },
    productImageCandidates(product) {
      const primary = product?.primary_image_url ? getProductImageCandidates(String(product.primary_image_url)) : [];
      const primaryPath = product?.primary_image_path ? getProductImageCandidates(product.primary_image_path) : [];
      const resolved = Array.isArray(product?.resolved_image_urls)
        ? product.resolved_image_urls.flatMap((u) => getProductImageCandidates(String(u)))
        : [];
      const fromPics = Array.isArray(product?.pics)
        ? product.pics.flatMap((p) => getProductImageCandidates(p?.image_path))
        : [];
      const firstPic = this.firstPicPath(product);
      const fallback = firstPic ? getProductImageCandidates(firstPic) : [];
      return [...new Set([...primary, ...primaryPath, ...resolved, ...fromPics, ...fallback].filter(Boolean))];
    },
    currentProductImage(product) {
      const id = Number(product?.id);
      const candidates = this.productImageCandidates(product);
      if (!candidates.length) return "";
      const index = Number(this.productImageIndexes[id] || 0);
      if (index < 0 || index >= candidates.length) {
        this.productImageIndexes[id] = 0;
        return candidates[0];
      }
      return candidates[index];
    },
    onProductImageError(product) {
      const id = Number(product?.id);
      const candidates = this.productImageCandidates(product);
      const currentIndex = Number(this.productImageIndexes[id] || 0);
      if (currentIndex + 1 < candidates.length) {
        this.productImageIndexes[id] = currentIndex + 1;
        return;
      }
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
    openProduct(product) {
      this.$router.push({
        name: "product-detail",
        params: { id: product.id },
        query: { category: this.$route.query.category },
      });
    },
    categoryIconClass(categoryName) {
      return resolveCategoryIconClass(categoryName);
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
    async onAddToCart(product) {
      const userStore = useUserLoginLogoutStore();
      const toast = useToastStore();

      if (!userStore.isLoggedIn) {
        toast.messages.push("Please sign in before adding items to cart.");
        toast.show("Error");
        this.$router.push("/login");
        return;
      }

      try {
        await useCartStore().addToCart(product, 1);
      } catch (error) {
        if (error?.response?.status === 403) {
          toast.messages.push("Cart actions are allowed only for customer accounts.");
          toast.show("Error");
          return;
        }
        throw error;
      }
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
  background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  padding: 14px;
  box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
  gap: 8px;
  cursor: pointer;
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}

.product-card:hover {
  transform: translateY(-2px);
  border-color: #bfd4f3;
  box-shadow: 0 16px 30px rgba(37, 99, 235, 0.12);
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
  color: #1d4ed8;
  font-weight: 700;
  font-size: 1.05rem;
}

.product-bottom-row {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.product-add-btn {
  border: 1px solid #1d4ed8;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #fff;
  font-weight: 700;
  border-radius: 999px;
  padding: 0.38rem 0.8rem;
  font-size: 0.82rem;
  line-height: 1.1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
  box-shadow: 0 8px 16px rgba(37, 99, 235, 0.24);
  transition: transform 0.16s ease, box-shadow 0.16s ease, filter 0.16s ease;
}

.product-add-btn:hover {
  filter: brightness(1.03);
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
}

.product-add-btn:active {
  transform: translateY(0);
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
