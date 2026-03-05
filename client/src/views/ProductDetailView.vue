<template>
  <div class="container py-2">
    <div class="d-flex justify-content-end mb-2">
      <button class="btn btn-outline-secondary btn-sm" @click="goBackToCategory">
        Back to category
      </button>
    </div>

    <ProductDetailSkeleton v-if="loading" />
    <div v-else-if="!product" class="alert alert-warning">Product not found.</div>

    <div v-else class="row g-4 align-items-start product-layout">
      <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm p-3 p-md-4 image-card">
          <div class="slider-wrap">
            <button
              v-if="galleryImages.length > 1"
              class="slider-nav prev"
              type="button"
              aria-label="Previous image"
              @click.stop="showPrevImage"
            >
              <i class="bi bi-chevron-left"></i>
            </button>

            <button class="image-btn w-100" @click="openGallery(currentImageIndex)">
            <img
              :src="activeImage"
              class="img-fluid rounded border w-100 main-image"
              :alt="product.name"
              @error="onActiveImageError"
            />
            </button>

            <button
              v-if="galleryImages.length > 1"
              class="slider-nav next"
              type="button"
              aria-label="Next image"
              @click.stop="showNextImage"
            >
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>

          <div class="thumb-row mt-3" v-if="galleryImages.length">
            <button
              v-for="(img, idx) in galleryImages"
              :key="`${img}-${idx}`"
              class="thumb-btn"
              :class="{ active: img === activeImage }"
              @click="activeImage = img"
              @dblclick="openGallery(idx)"
            >
              <img :src="img" class="thumb-img" :alt="product.name" @error="markImageBroken(img)" />
            </button>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm p-3 p-md-4 info-card">
          <h2 class="mb-2 product-title">{{ product.name }}</h2>
          <p class="text-secondary mb-1">Category: {{ product.category?.category_name || "-" }}</p>
          <p class="text-secondary mb-3">Brand: {{ product.company?.company_name || "-" }}</p>

          <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <p class="fs-3 fw-bold m-0"><UsdPrice :value="product.price" /></p>
            <span class="badge rounded-pill text-bg-light border">Stock: {{ product.pcs }} pcs</span>
          </div>

          <button class="btn btn-primary btn-lg w-100 w-md-auto mb-3" @click="onAddToCart">Add to cart</button>

          <h5 class="mb-2">Description</h5>
          <p class="text-secondary mb-3">{{ product.description || "No description." }}</p>

          <div v-if="product.parameters?.length">
            <h5 class="mb-2">Specifications</h5>
            <div class="spec-wrap">
              <ul class="list-group spec-list">
                <li class="list-group-item d-flex justify-content-between" v-for="param in product.parameters" :key="param.id">
                  <span>{{ param.parameter_name }}</span>
                  <strong>{{ param.pivot?.value }} {{ param.unit?.unit_name || param.unit?.name || "" }}</strong>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm p-3 p-md-4">
          <h5 class="mb-2">Comments</h5>
          <div v-if="backendComments.length" class="comments-wrap">
            <ul class="list-group comments-list">
              <li class="list-group-item" v-for="comment in backendComments" :key="comment.id">
                <div class="comment-row">
                  <div class="comment-avatar" aria-hidden="true"></div>
                  <div>
                    <div class="fw-semibold">{{ comment.user?.name || "Anonymous" }}</div>
                    <div class="text-secondary">{{ comment.comment || "-" }}</div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <p v-else class="text-secondary m-0">No comments yet.</p>
        </div>
      </div>
    </div>

    <ProductImageCarouselModal
      v-model="showGallery"
      :images="galleryImages"
      :start-index="galleryIndex"
      :title="product?.name || 'Product gallery'"
    />
  </div>
</template>

<script>
import productService from "@/api/productService";
import { useCartStore } from "@/stores/cartStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";
import { getProductImageCandidates } from "@/utils/image";
import ProductImageCarouselModal from "@/components/Product/ProductImageCarouselModal.vue";
import ProductDetailSkeleton from "@/components/Product/ProductDetailSkeleton.vue";
import UsdPrice from "@/components/Common/UsdPrice.vue";

export default {
  components: {
    ProductImageCarouselModal,
    ProductDetailSkeleton,
    UsdPrice,
  },
  data() {
    return {
      loading: false,
      product: null,
      activeImage: "",
      showGallery: false,
      galleryImages: [],
      galleryIndex: 0,
      brokenImages: {},
    };
  },
  computed: {
    currentImageIndex() {
      return Math.max(0, this.galleryImages.findIndex((img) => img === this.activeImage));
    },
    backendComments() {
      return Array.isArray(this.product?.comments) ? this.product.comments : [];
    },
  },
  methods: {
    async loadProduct() {
      this.loading = true;
      try {
        const response = await productService.getById(this.$route.params.id);
        this.product = response.data || null;

        const primary = this.product?.primary_image_url
          ? getProductImageCandidates(this.product.primary_image_url)
          : [];
        const primaryPath = this.product?.primary_image_path
          ? getProductImageCandidates(this.product.primary_image_path)
          : [];
        const resolved = Array.isArray(this.product?.resolved_image_urls)
          ? this.product.resolved_image_urls.flatMap((u) => getProductImageCandidates(u))
          : [];
        const fromPics = Array.isArray(this.product?.pics)
          ? this.product.pics.flatMap((p) => getProductImageCandidates(p?.image_path))
          : [];

        this.galleryImages = [...new Set([...primary, ...primaryPath, ...resolved, ...fromPics].filter(Boolean))];
        this.brokenImages = {};
        this.activeImage = this.galleryImages[0] || "";
      } finally {
        this.loading = false;
      }
    },
    markImageBroken(imageUrl) {
      const key = String(imageUrl || "");
      if (!key) return;
      this.brokenImages[key] = true;
      this.galleryImages = this.galleryImages.filter((img) => !this.brokenImages[img]);
      if (this.activeImage === key) {
        this.activeImage = this.galleryImages[0] || "";
      }
    },
    onActiveImageError() {
      this.markImageBroken(this.activeImage);
    },
    openGallery(startIndex = 0) {
      if (!this.galleryImages.length) return;
      this.galleryIndex = startIndex;
      this.showGallery = true;
    },
    showPrevImage() {
      if (this.galleryImages.length <= 1) return;
      const current = this.currentImageIndex;
      const nextIndex = current <= 0 ? this.galleryImages.length - 1 : current - 1;
      this.activeImage = this.galleryImages[nextIndex] || this.activeImage;
    },
    showNextImage() {
      if (this.galleryImages.length <= 1) return;
      const current = this.currentImageIndex;
      const nextIndex = current >= this.galleryImages.length - 1 ? 0 : current + 1;
      this.activeImage = this.galleryImages[nextIndex] || this.activeImage;
    },
    goBackToCategory() {
      const routeCategory = Number(this.$route.query.category);
      const productCategory = Number(this.product?.category_id);
      const categoryId = routeCategory || productCategory || null;

      if (categoryId) {
        this.$router.push({ path: "/adatok/categories", query: { category: categoryId } });
        return;
      }
      this.$router.push({ path: "/adatok/categories" });
    },
    async onAddToCart() {
      const userStore = useUserLoginLogoutStore();
      const toast = useToastStore();
      if (!userStore.isLoggedIn) {
        toast.messages.push("Please sign in before adding items to cart.");
        toast.show("Error");
        this.$router.push("/login");
        return;
      }
      try {
        await useCartStore().addToCart(this.product, 1);
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
  watch: {
    "$route.params.id": {
      immediate: true,
      async handler() {
        await this.loadProduct();
      },
    },
  },
};
</script>

<style scoped>
.image-card,
.info-card {
  min-height: 100%;
}

.product-title {
  line-height: 1.2;
}

.image-btn {
  border: 0;
  padding: 0;
  background: transparent;
}

.slider-wrap {
  position: relative;
}

.slider-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 38px;
  height: 38px;
  border: 0;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.78);
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.slider-nav.prev {
  left: 10px;
}

.slider-nav.next {
  right: 10px;
}

.main-image {
  height: clamp(280px, 42vw, 460px);
  max-height: 460px;
  object-fit: contain;
  object-position: center;
  background: radial-gradient(circle at center, #ffffff 35%, #eef3f8 100%);
  padding: 0.35rem;
}

.thumb-row {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(64px, 1fr));
  gap: 0.55rem;
}

.thumb-btn {
  border: 1px solid #d7e3f5;
  border-radius: 0.55rem;
  padding: 0;
  background: #fff;
  overflow: hidden;
}

.thumb-btn.active {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.18);
}

.thumb-img {
  width: 100%;
  height: 60px;
  object-fit: contain;
  background: #fff;
}

.spec-wrap {
  max-height: 320px;
  overflow: auto;
}

.spec-list .list-group-item {
  font-size: 0.95rem;
}

.comments-wrap {
  max-height: 280px;
  overflow: auto;
}

.comment-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.comment-avatar {
  width: 36px;
  height: 36px;
  border-radius: 999px;
  flex: 0 0 36px;
  background: #b8c9e6;
  position: relative;
}

.comment-avatar::before {
  content: "";
  position: absolute;
  top: 7px;
  left: 50%;
  transform: translateX(-50%);
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #4f8fd1;
}

.comment-avatar::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: 5px;
  transform: translateX(-50%);
  width: 22px;
  height: 12px;
  border-radius: 12px 12px 6px 6px;
  background: #4f8fd1;
}

@media (min-width: 1200px) {
  .info-card {
    position: sticky;
    top: 1rem;
  }
}
</style>
