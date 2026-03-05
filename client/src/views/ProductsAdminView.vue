<template>
  <div class="products-admin">
    <section class="hero">
      <div>
        <p class="kicker">Admin</p>
        <h1>Products</h1>
        <p class="subtitle">Create, edit and delete catalog products.</p>
      </div>
      <div class="hero-right">
        <button class="btn btn-primary" @click="startCreate">New product</button>
        <span class="count">{{ filteredProducts.length }} items</span>
      </div>
    </section>

    <section class="card-box">
      <div class="table-responsive" v-if="!loading && filteredProducts.length">
        <table class="table align-middle table-hover m-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Category</th>
              <th>Brand</th>
              <th>Stock</th>
              <th>Price</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in filteredProducts" :key="product.id">
              <td>#{{ product.id }}</td>
              <td>{{ product.name }}</td>
              <td>{{ product?.category?.category_name || "-" }}</td>
              <td>{{ product?.company?.company_name || "-" }}</td>
              <td>{{ product.pcs }}</td>
              <td><UsdPrice :value="product.price" /></td>
              <td class="text-end">
                <div class="actions">
                  <button class="btn btn-sm btn-outline-primary" @click="startEdit(product)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="askDelete(product)">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else-if="loading" class="state-wrap">Loading products...</div>
      <div v-else class="state-wrap">No products found.</div>
    </section>

    <section class="card-box mt-3">
      <h5 class="mb-3">{{ formMode === "create" ? "Create product" : "Edit product" }}</h5>
      <form @submit.prevent="saveProduct" class="row g-3">
        <div class="col-12 col-xl-6">
          <label class="form-label">Name</label>
          <input v-model.trim="form.name" type="text" class="form-control" required maxlength="150" />
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <label class="form-label">Category</label>
          <select v-model.number="form.category_id" class="form-select" required>
            <option :value="null" disabled>Select category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.categoryName }}</option>
          </select>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <label class="form-label">Brand</label>
          <select v-model.number="form.company_id" class="form-select" required>
            <option :value="null" disabled>Select brand</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.brandName }}</option>
          </select>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <label class="form-label">Stock</label>
          <input v-model.number="form.pcs" type="number" min="1" class="form-control" required />
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <label class="form-label">Price (USD)</label>
          <input v-model.number="form.price" type="number" min="0" step="0.01" class="form-control" required />
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea v-model.trim="form.description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary" type="submit" :disabled="saving">
            {{ saving ? "Saving..." : (formMode === "create" ? "Create" : "Save changes") }}
          </button>
          <button class="btn btn-outline-secondary" type="button" @click="resetForm">Reset</button>
        </div>
      </form>
    </section>

    <ConfirmModal
      :isOpenConfirmModal="showDeleteConfirm"
      title="Delete product"
      message="Delete selected product?"
      cancel="Cancel"
      confirm="Delete"
      @cancel="showDeleteConfirm = false"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useSearchStore } from "@/stores/searchStore";
import productService from "@/api/productService";
import categoryService from "@/api/categoryService";
import brandService from "@/api/brandService";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import UsdPrice from "@/components/Common/UsdPrice.vue";

function initialForm() {
  return {
    id: null,
    name: "",
    category_id: null,
    company_id: null,
    pcs: 1,
    price: 0,
    description: "",
  };
}

export default {
  name: "ProductsAdminView",
  components: { ConfirmModal, UsdPrice },
  data() {
    return {
      loading: false,
      saving: false,
      products: [],
      categories: [],
      brands: [],
      form: initialForm(),
      formMode: "create",
      showDeleteConfirm: false,
      pendingDelete: null,
    };
  },
  computed: {
    ...mapState(useSearchStore, ["searchWord"]),
    filteredProducts() {
      const term = String(this.searchWord || "").trim().toLowerCase();
      if (!term) return this.products;

      return this.products.filter((product) =>
        [product.id, product.name, product?.category?.category_name, product?.company?.company_name, product.price]
          .map((v) => String(v ?? "").toLowerCase())
          .some((v) => v.includes(term)),
      );
    },
  },
  methods: {
    async loadData() {
      this.loading = true;
      try {
        const [productsRes, categoriesRes, brandsRes] = await Promise.all([
          productService.getAll(),
          categoryService.getAll(),
          brandService.getAll(),
        ]);
        this.products = productsRes.data || [];
        this.categories = categoriesRes.data || [];
        this.brands = brandsRes.data || [];
      } finally {
        this.loading = false;
      }
    },
    startCreate() {
      this.formMode = "create";
      this.resetForm();
    },
    startEdit(product) {
      this.formMode = "edit";
      this.form = {
        id: product.id,
        name: product.name || "",
        category_id: Number(product.category_id) || Number(product?.category?.id) || null,
        company_id: Number(product.company_id) || Number(product?.company?.id) || null,
        pcs: Number(product.pcs) || 1,
        price: Number(product.price) || 0,
        description: product.description || "",
      };
      window.scrollTo({ top: 0, behavior: "smooth" });
    },
    resetForm() {
      this.form = initialForm();
    },
    async saveProduct() {
      const payload = {
        name: this.form.name,
        category_id: this.form.category_id,
        company_id: this.form.company_id,
        pcs: this.form.pcs,
        price: this.form.price,
        description: this.form.description,
      };

      this.saving = true;
      try {
        if (this.formMode === "create") {
          await productService.create(payload);
        } else {
          await productService.update(this.form.id, payload);
        }
        await this.loadData();
        this.startCreate();
      } finally {
        this.saving = false;
      }
    },
    askDelete(product) {
      this.pendingDelete = product;
      this.showDeleteConfirm = true;
    },
    async confirmDelete() {
      if (!this.pendingDelete?.id) return;
      await productService.delete(this.pendingDelete.id);
      this.showDeleteConfirm = false;
      this.pendingDelete = null;
      await this.loadData();
      if (this.formMode === "edit" && this.form.id) {
        this.startCreate();
      }
    },
  },
  async mounted() {
    await this.loadData();
  },
};
</script>

<style scoped>
.products-admin {
  display: grid;
  gap: 14px;
}

.hero {
  border: 1px solid #dbe8fb;
  border-radius: 16px;
  padding: 16px;
  background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  box-shadow: 0 10px 26px rgba(37, 99, 235, 0.08);
  display: flex;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.kicker {
  margin: 0 0 6px;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #2563eb;
  font-weight: 700;
}

.subtitle {
  margin: 6px 0 0;
  color: #475569;
}

.hero-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.count {
  color: #64748b;
  font-weight: 600;
}

.card-box {
  border: 1px solid #dbe8fb;
  border-radius: 12px;
  padding: 14px;
  background: #fff;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.actions {
  display: inline-flex;
  gap: 6px;
}

.state-wrap {
  color: #64748b;
}
</style>
