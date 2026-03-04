<template>
  <div v-if="modelValue" class="gallery-backdrop" @click.self="close">
    <div class="gallery-modal card border-0 shadow">
      <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
        <strong class="px-1">{{ title }}</strong>
        <button class="btn btn-sm btn-outline-secondary" @click="close">Close</button>
      </div>
      <div class="gallery-body">
        <button class="nav-btn" @click="prev" aria-label="Previous" v-if="images.length > 1">
          <i class="bi bi-chevron-left"></i>
        </button>
        <img :src="images[current]" class="gallery-image" alt="Product image" @error="next" />
        <button class="nav-btn" @click="next" aria-label="Next" v-if="images.length > 1">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
      <div class="p-2 text-center text-muted small">{{ current + 1 }} / {{ images.length }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductImageCarouselModal",
  props: {
    modelValue: Boolean,
    images: { type: Array, default: () => [] },
    startIndex: { type: Number, default: 0 },
    title: { type: String, default: "Product gallery" },
  },
  emits: ["update:modelValue"],
  data() {
    return { current: 0 };
  },
  watch: {
    modelValue(v) {
      if (v) this.current = this.clampIndex(this.startIndex);
    },
    startIndex(v) {
      this.current = this.clampIndex(v);
    },
  },
  methods: {
    clampIndex(index) {
      return Math.min(Math.max(0, Number(index) || 0), this.images.length - 1);
    },
    close() {
      this.$emit("update:modelValue", false);
    },
    prev() {
      if (!this.images.length) return;
      this.current = this.current <= 0 ? this.images.length - 1 : this.current - 1;
    },
    next() {
      if (!this.images.length) return;
      this.current = this.current >= this.images.length - 1 ? 0 : this.current + 1;
    },
  },
};
</script>

<style scoped>
.gallery-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(2, 6, 23, 0.72);
  display: grid;
  place-items: center;
  z-index: 1200;
  padding: 12px;
}

.gallery-modal {
  width: min(100%, 960px);
  border-radius: 12px;
}

.gallery-body {
  display: grid;
  grid-template-columns: 44px 1fr 44px;
  align-items: center;
  gap: 8px;
  padding: 12px;
}

.gallery-image {
  width: 100%;
  max-height: 72vh;
  object-fit: contain;
}

.nav-btn {
  border: none;
  background: #0f172a;
  color: #fff;
  width: 36px;
  height: 36px;
  border-radius: 999px;
}
</style>
