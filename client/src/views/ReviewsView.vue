<template>
  <div class="reviews-page">
    <section class="reviews-hero">
      <p class="hero-kicker">Community</p>
      <h1>Customer Builds & Reviews</h1>
      <p>
        Real setups from satisfied DOOMSHOP customers. Clean cable management,
        strong airflow and high-performance components.
      </p>
    </section>

    <section class="reviews-grid-wrap">
      <article v-for="item in reviewItems" :key="item.id" class="review-card">
        <img
          class="review-image"
          :src="item.image"
          :alt="item.title"
          loading="lazy"
        />
        <div class="review-content">
          <h3>{{ item.title }}</h3>
          <p>{{ item.text }}</p>
          <div class="rating">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <span>5.0</span>
          </div>
        </div>
      </article>
    </section>
  </div>
</template>

<script>
export default {
  name: "ReviewsView",
  data() {
    return {
      reviewItems: [
        {
          id: 1,
          image: "/images/reviews/review-1.jpg",
          title: "RTX Build with RGB Theme",
          text: "Fast delivery and every part arrived in perfect condition.",
        },
        {
          id: 2,
          image: "/images/reviews/review-2.jpg",
          title: "High Airflow Gaming Setup",
          text: "Great support during the build, temperatures are excellent.",
        },
        {
          id: 3,
          image: "/images/reviews/review-3.jpg",
          title: "Balanced Mid-Tower PC",
          text: "Good value and smooth assembly thanks to accurate product info.",
        },
        {
          id: 4,
          image: "/images/reviews/review-4.jpg",
          title: "Clean Black Edition",
          text: "Exactly what I ordered, with quick and safe shipping.",
        },
        {
          id: 5,
          image: "/images/reviews/review-5.jpg",
          title: "Custom Gaming Rig",
          text: "Excellent packaging and easy cable management after assembly.",
        },
        {
          id: 6,
          image: "/images/reviews/review-6.jpg",
          title: "Performance Focused Build",
          text: "Strong airflow and stable performance in long gaming sessions.",
        },
        {
          id: 7,
          image: "/images/reviews/review-7.jpg",
          title: "Minimalist Setup",
          text: "Great compatibility between parts, setup was straightforward.",
        },
        {
          id: 8,
          image: "/images/reviews/review-8.jpg",
          title: "RGB Showcase",
          text: "Exactly the look I wanted, plus fast shipping and support.",
        },
      ],
    };
  },
};
</script>

<style scoped>
.reviews-page {
  display: grid;
  gap: 14px;
  padding-bottom: 20px;
}

.reviews-hero {
  border: 1px solid #dbe8fb;
  border-radius: 16px;
  padding: 18px;
  background:
    radial-gradient(circle at 88% 20%, rgba(59, 130, 246, 0.18) 0%, rgba(59, 130, 246, 0) 38%),
    linear-gradient(140deg, #ffffff 0%, #f4f8ff 58%, #ecf4ff 100%);
  box-shadow: 0 12px 28px rgba(37, 99, 235, 0.1);
}

.hero-kicker {
  margin: 0 0 6px;
  font-size: 0.75rem;
  letter-spacing: 0.11em;
  text-transform: uppercase;
  color: #2563eb;
  font-weight: 800;
}

.reviews-hero h1 {
  margin: 0;
  color: #0f172a;
  font-size: clamp(1.4rem, 3vw, 2rem);
}

.reviews-hero p {
  margin: 10px 0 0;
  color: #475569;
  max-width: 70ch;
}

.reviews-grid-wrap {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 300px));
  gap: 14px;
  justify-content: center;
}

.review-card {
  border: 1px solid #dde8f9;
  border-radius: 14px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.review-card:hover {
  transform: translateY(-2px);
  border-color: #bfd4f3;
  box-shadow: 0 14px 28px rgba(37, 99, 235, 0.13);
}

.review-image {
  width: 100%;
  height: 260px;
  object-fit: cover;
  object-position: center;
  border-bottom: 1px solid #e2e8f0;
}

.review-content {
  padding: 12px;
  display: grid;
  gap: 8px;
}

.review-content h3 {
  margin: 0;
  font-size: 1.05rem;
  color: #0f172a;
}

.review-content p {
  margin: 0;
  color: #475569;
}

.rating {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  color: #eab308;
  font-size: 0.86rem;
  font-weight: 700;
}

.rating span {
  margin-left: 4px;
  color: #334155;
}

@media (max-width: 900px) {
  .reviews-grid-wrap {
    grid-template-columns: 1fr;
  }

  .review-image {
    height: 240px;
  }
}
</style>
