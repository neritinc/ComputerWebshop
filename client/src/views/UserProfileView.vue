<template>
  <div class="profile-page">
    <section class="profile-hero">
      <div>
        <p class="profile-kicker">Account</p>
        <h1>{{ userName || "User" }}</h1>
        <p class="profile-subtitle">{{ userEmail || "-" }}</p>
      </div>
      <span class="role-pill">{{ roleLabel }}</span>
    </section>

    <section v-if="isAdmin" class="admin-card">
      <h5 class="mb-2">Admin Section</h5>
      <p class="text-secondary mb-3">Manage products and moderate comments from this page.</p>
      <div class="admin-links">
        <RouterLink class="btn btn-outline-primary" to="/userprofil/products">Products</RouterLink>
        <RouterLink class="btn btn-outline-primary" to="/userprofil/comments">Comments</RouterLink>
      </div>
    </section>

    <section class="panel-card">
      <div class="panel-head">
        <h5 class="m-0">My Cart</h5>
        <RouterLink class="btn btn-sm btn-outline-primary" to="/cart">Open cart</RouterLink>
      </div>
      <div v-if="cartItems.length" class="simple-list">
        <article class="list-row" v-for="item in cartItems" :key="item.productId">
          <div>
            <strong>{{ item.name }}</strong>
            <p class="m-0 text-secondary">Qty: {{ item.qty }}</p>
          </div>
          <strong><UsdPrice :value="Number(item.price || 0) * Number(item.qty || 0)" /></strong>
        </article>
      </div>
      <p v-else class="text-secondary m-0">No items in your cart.</p>
    </section>

    <section class="panel-card">
      <div class="panel-head">
        <h5 class="m-0">My Comments</h5>
        <span class="text-secondary small">{{ myComments.length }} comments</span>
      </div>
      <div v-if="commentsLoading" class="text-secondary">Loading comments...</div>
      <div v-else-if="myComments.length" class="simple-list">
        <article class="list-row" v-for="comment in myComments" :key="comment.id">
          <div>
            <strong>{{ comment?.product?.name || "Product" }}</strong>
            <p class="m-0 text-secondary">{{ comment.comment }}</p>
          </div>
        </article>
      </div>
      <p v-else class="text-secondary m-0">You have no comments yet.</p>
    </section>

    <RouterView />
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useCartStore } from "@/stores/cartStore";
import commentService from "@/api/commentService";
import UsdPrice from "@/components/Common/UsdPrice.vue";

export default {
  name: "UserProfileView",
  components: { UsdPrice },
  data() {
    return {
      commentsLoading: false,
      allComments: [],
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["item", "userName", "role"]),
    ...mapState(useCartStore, ["items"]),
    userEmail() {
      return this.item?.email || "";
    },
    cartItems() {
      return this.items || [];
    },
    myComments() {
      const currentUserId = Number(this.item?.id);
      if (!Number.isFinite(currentUserId)) return [];
      return (this.allComments || []).filter((row) => Number(row?.user_id) === currentUserId);
    },
    isAdmin() {
      return Number(this.role) === 1;
    },
    roleLabel() {
      if (Number(this.role) === 1) return "Admin";
      if (Number(this.role) === 2) return "Teacher";
      if (Number(this.role) === 3) return "Student";
      return "User";
    },
  },
  methods: {
    async loadMyComments() {
      this.commentsLoading = true;
      try {
        const response = await commentService.getAll();
        this.allComments = response.data || [];
      } catch (error) {
        this.allComments = [];
      } finally {
        this.commentsLoading = false;
      }
    },
  },
  async mounted() {
    await this.loadMyComments();
  },
};
</script>

<style scoped>
.profile-page {
  display: grid;
  gap: 14px;
}

.profile-hero {
  border: 1px solid #dbe8fb;
  border-radius: 16px;
  padding: 16px;
  background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  box-shadow: 0 10px 26px rgba(37, 99, 235, 0.08);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  flex-wrap: wrap;
}

.profile-kicker {
  margin: 0 0 6px;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #2563eb;
  font-weight: 700;
}

.profile-subtitle {
  margin: 6px 0 0;
  color: #475569;
}

.role-pill {
  border-radius: 999px;
  border: 1px solid #bfdbfe;
  background: #eff6ff;
  color: #1d4ed8;
  font-weight: 700;
  padding: 6px 12px;
}

.admin-card {
  border: 1px solid #dbe8fb;
  border-radius: 12px;
  padding: 14px;
  background: #ffffff;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.admin-links {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.panel-card {
  border: 1px solid #dbe8fb;
  border-radius: 12px;
  padding: 14px;
  background: #ffffff;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.panel-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.simple-list {
  display: grid;
  gap: 8px;
}

.list-row {
  border: 1px solid #e5edf9;
  border-radius: 10px;
  padding: 10px;
  display: flex;
  justify-content: space-between;
  gap: 10px;
}
</style>
