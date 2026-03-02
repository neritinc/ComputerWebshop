<template>
  <div class="home-page">
    <section class="home-hero">
      <p class="hero-kicker">Tech Market Watch</p>
      <h1>Latest PC Hardware News</h1>
      <p class="hero-copy">
        Follow current trends in components, pricing, and availability before
        your next build.
      </p>
    </section>

    <section class="news-section">
      <div class="section-head">
        <h2>Featured News</h2>
        <span class="news-count">4 updates</span>
      </div>

      <div class="news-grid">
        <article class="news-card">
          <div class="news-head">
            <p class="news-tag">Memory</p>
            <i class="bi bi-memory news-icon"></i>
          </div>
          <h3>DDR5 memory prices show fresh upward pressure</h3>
          <img
            src="@/assets/mempriceincrease.png"
            alt="DDR5 memory price increase chart"
            class="news-image"
          />
          <p>
            Suppliers report tighter stock on popular 32GB and 64GB kits, with
            retail pricing trending higher this month.
          </p>
        </article>

        <article class="news-card">
          <div class="news-head">
            <p class="news-tag">Storage</p>
            <i class="bi bi-device-ssd news-icon"></i>
          </div>
          <h3>NVMe SSD prices rise as demand grows</h3>
          <img
            src="@/assets/ssdprices.png"
            alt="SSD price increase chart"
            class="news-image"
          />
          <p>
            Mid-range Gen4 SSDs are seeing noticeable price increases, while
            high-capacity models remain limited in several regions.
          </p>
        </article>

        <article class="news-card">
          <div class="news-head">
            <p class="news-tag">Graphics</p>
            <i class="bi bi-gpu-card news-icon"></i>
          </div>
          <h3>GPU stock stabilizes, but premium cards stay expensive</h3>
          <img
            src="@/assets/graphics-news.png"
            alt="NVIDIA GeForce RTX graphics cards"
            class="news-image"
          />
          <p>
            Availability has improved for mainstream graphics cards, though top
            tier models continue to carry elevated prices.
          </p>
        </article>

        <article class="news-card">
          <div class="news-head">
            <p class="news-tag">Processors</p>
            <i class="bi bi-cpu news-icon"></i>
          </div>
          <h3>CPU market shifts as new generation launches approach</h3>
          <img
            src="@/assets/cpu-news.jpg"
            alt="AMD Ryzen CPU close-up"
            class="news-image"
          />
          <p>
            Buyers are balancing current discounts against expected next-gen
            releases, creating mixed movement across performance segments.
          </p>
        </article>
      </div>
    </section>

    <section class="top-categories">
      <div class="section-head">
        <h2>Top Categories</h2>
        <div class="carousel-controls">
          <button class="carousel-btn" @click="scrollCategories(-1)">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button class="carousel-btn" @click="scrollCategories(1)">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>

      <div class="category-carousel" ref="categoryCarousel">
        <button
          v-for="category in topCategories"
          :key="category.id"
          class="category-pill"
          @click="goToCategory(category.id)"
        >
          {{ category.categoryName }}
        </button>
      </div>
    </section>
  </div>
</template>

<script>
import categoryService from "@/api/categoryService";

export default {
  data() {
    return {
      categories: [],
    };
  },
  computed: {
    topCategories() {
      const preferred = [
        "processor",
        "graphics card",
        "memory module",
        "motherboard",
        "storage",
        "monitor",
        "power supply",
        "cooling",
      ];

      const byName = new Map(
        this.categories.map((c) => [String(c.categoryName || "").toLowerCase(), c]),
      );
      return preferred.map((name) => byName.get(name)).filter(Boolean);
    },
  },
  methods: {
    async loadCategories() {
      try {
        const response = await categoryService.getAll();
        this.categories = response.data || [];
      } catch (error) {
        this.categories = [];
      }
    },
    goToCategory(categoryId) {
      this.$router.push({
        path: "/adatok/categories",
        query: { category: categoryId },
      });
    },
    scrollCategories(direction) {
      const container = this.$refs.categoryCarousel;
      if (!container) return;
      container.scrollBy({
        left: direction * 260,
        behavior: "smooth",
      });
    },
  },
  async mounted() {
    await this.loadCategories();
  },
};
</script>

<style scoped>
.home-page {
  display: grid;
  gap: 18px;
}

.home-hero {
  border: 1px solid #dce8f8;
  border-radius: 14px;
  padding: 18px;
  background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
}

.hero-kicker {
  margin: 0 0 6px;
  font-size: 0.78rem;
  font-weight: 700;
  color: #2563eb;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.home-hero h1 {
  margin: 0;
  font-size: clamp(1.4rem, 3vw, 2rem);
  color: #0f172a;
}

.hero-copy {
  margin: 10px 0 0;
  color: #334155;
  max-width: 720px;
}

.news-section {
  border: 1px solid #dbe8fb;
  border-radius: 16px;
  padding: 18px;
  background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  box-shadow: 0 10px 26px rgba(37, 99, 235, 0.08);
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.section-head h2 {
  margin: 0;
  font-size: 1.2rem;
  color: #0f172a;
}

.news-count {
  font-size: 0.82rem;
  color: #64748b;
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.news-card {
  border: 1px solid #dde8f9;
  border-radius: 14px;
  padding: 14px;
  background: #ffffff;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.news-card:hover {
  transform: translateY(-2px);
  border-color: #bfd4f3;
  box-shadow: 0 12px 24px rgba(37, 99, 235, 0.12);
}

.news-card h3 {
  margin: 6px 0 10px;
  font-size: 1.05rem;
  color: #0f172a;
  line-height: 1.25;
}

.news-card p {
  margin: 0;
  color: #425466;
  line-height: 1.5;
}

.news-image {
  width: 100%;
  height: 170px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #d7e4f7;
  margin: 4px 0 12px;
}

.news-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.news-icon {
  font-size: 1.05rem;
  color: #1d4ed8;
  background: #eaf2ff;
  border: 1px solid #d5e4fb;
  border-radius: 999px;
  width: 30px;
  height: 30px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.news-tag {
  font-size: 0.74rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #1e40af;
  background: #eaf2ff;
  border: 1px solid #d5e4fb;
  border-radius: 999px;
  padding: 3px 10px;
  display: inline-flex;
}

@media (max-width: 768px) {
  .news-grid {
    grid-template-columns: 1fr;
  }

  .news-image {
    height: 190px;
  }
}

.top-categories {
  border: 1px solid #dbe8fb;
  border-radius: 18px;
  padding: 20px;
  background: #ffffff;
  box-shadow: 0 14px 30px rgba(37, 99, 235, 0.12);
}

.top-categories .section-head h2 {
  font-size: 1.35rem;
}

.carousel-controls {
  display: flex;
  gap: 6px;
}

.carousel-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 1px solid #d0def3;
  background: #f4f8ff;
  color: #1d4ed8;
}

.category-carousel {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 6px 2px 8px;
  scrollbar-width: thin;
}

.category-pill {
  border: 1px solid #c9dcf8;
  background: #eaf2ff;
  color: #1e40af;
  border-radius: 999px;
  padding: 10px 18px;
  font-size: 1rem;
  font-weight: 700;
  white-space: nowrap;
  box-shadow: 0 6px 14px rgba(37, 99, 235, 0.12);
}
</style>

