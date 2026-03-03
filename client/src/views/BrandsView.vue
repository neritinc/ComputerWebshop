<template>
  <div class="brands-page">
    <section class="brands-hero">
      <div>
        <p class="brands-kicker">Catalog Management</p>
        <h1>{{ pageTitle }}</h1>
        <p class="brands-subtitle">
          Maintain manufacturer records used across products and filters.
        </p>
      </div>
      <div class="brands-toolbar">
        <div v-if="loading" class="loading-wrap">
          <i class="bi bi-hourglass-split fs-4"></i>
          <span>Loading...</span>
        </div>
        <ButtonsCrudCreate v-else-if="isAdmin" @create="createHandler" />
        <span class="brands-count">{{ getItemsLength }} items</span>
      </div>
    </section>

    <section v-if="items.length > 0" class="brands-grid">
      <article v-for="brand in items" :key="brand.id" class="brand-card">
        <div class="brand-head">
          <p class="brand-id">#{{ brand.id }}</p>
          <div v-if="isAdmin" class="brand-actions">
            <button class="btn btn-sm btn-outline-primary" @click="updateHandler(brand.id)">
              Edit
            </button>
            <button class="btn btn-sm btn-outline-danger" @click="deleteHandler(brand.id)">
              Delete
            </button>
          </div>
        </div>
        <h3 class="brand-name">{{ brand.brandName }}</h3>
      </article>
    </section>
    <div v-else class="brands-empty">No brands found</div>

    <FormBrand
      ref="form"
      :title="title"
      :item="item"
      @yesEventForm="yesEventFormHandler"
    />

    <ConfirmModal
      :isOpenConfirmModal="isOpenConfirmModal"
      @cancel="cancelHandler"
      @confirm="confirmHandler"
    />
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useBrandStore } from "@/stores/brandStore";
import { useSearchStore } from "@/stores/searchStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import ButtonsCrudCreate from "@/components/Table/ButtonsCrudCreate.vue";
import FormBrand from "@/components/Forms/FormBrand.vue";

export default {
  name: "BrandsView",
  components: {
    ConfirmModal,
    ButtonsCrudCreate,
    FormBrand,
  },
  watch: {
    searchWord() {
      this.getAllSortSearch(this.sortColumn, this.sortDirection);
    },
  },
  data() {
    return {
      pageTitle: "Manufacturers",
      isOpenConfirmModal: false,
      toDeleteId: null,
      state: "r",
      title: "",
    };
  },
  computed: {
    ...mapState(useBrandStore, [
      "item",
      "items",
      "loading",
      "sortColumn",
      "sortDirection",
      "getItemsLength",
    ]),
    ...mapState(useSearchStore, ["searchWord"]),
    ...mapState(useUserLoginLogoutStore, ["role"]),
    isAdmin() {
      return Number(this.role) === 1;
    },
  },
  methods: {
    ...mapActions(useBrandStore, [
      "getAll",
      "getAllSortSearch",
      "getById",
      "create",
      "update",
      "delete",
      "clearItem",
    ]),
    ...mapActions(useSearchStore, ["resetSearchWord"]),
    deleteHandler(id) {
      this.state = "d";
      this.isOpenConfirmModal = true;
      this.toDeleteId = id;
    },
    updateHandler(id) {
      this.state = "u";
      this.title = "Adatmodositas";
      this.getById(id);
      this.$refs.form.show();
    },
    createHandler() {
      this.state = "c";
      this.title = "Uj adatbevitel";
      this.clearItem();
      this.$refs.form.show();
    },
    sortHandler(column) {
      this.getAllSortSearch(column);
    },
    cancelHandler() {
      this.isOpenConfirmModal = false;
      this.state = "r";
    },
    async confirmHandler() {
      try {
        await this.delete(this.toDeleteId);
      } catch (error) {}
      this.isOpenConfirmModal = false;
      this.state = "r";
    },
    async yesEventFormHandler({ item, done }) {
      try {
        if (this.state == "c") {
          await this.create(item);
        } else {
          await this.update(item.id, item);
        }
        this.state = "r";
        done(true);
      } catch (err) {
        if (err.response && err.response.status === 422) {
          const errors = { ...(err.response.data.errors || {}) };
          if (errors.company_name && !errors.brandName) {
            errors.brandName = errors.company_name;
          }
          this.$refs.form.setServerErrors(errors);
          done(false);
        } else {
          done(false);
        }
      }
    },
  },
  async mounted() {
    this.resetSearchWord();
    await this.getAllSortSearch();
  },
};
</script>

<style scoped>
.brands-page {
  display: grid;
  gap: 16px;
}

.brands-hero {
  border: 1px solid #dbe8fb;
  border-radius: 16px;
  padding: 16px;
  background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  box-shadow: 0 10px 26px rgba(37, 99, 235, 0.08);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 14px;
  flex-wrap: wrap;
}

.brands-kicker {
  margin: 0 0 6px;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #2563eb;
  font-weight: 700;
}

.brands-hero h1 {
  margin: 0;
  color: #0f172a;
}

.brands-subtitle {
  margin: 6px 0 0;
  color: #475569;
}

.brands-toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
}

.brands-count {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 600;
}

.loading-wrap {
  display: inline-flex;
  gap: 8px;
  align-items: center;
  color: #334155;
}

.brands-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 12px;
}

.brand-card {
  border: 1px solid #dbe8fb;
  border-radius: 12px;
  padding: 12px;
  background: #ffffff;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
  display: grid;
  gap: 8px;
}

.brand-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}

.brand-id {
  margin: 0;
  color: #64748b;
  font-size: 0.82rem;
  font-weight: 600;
}

.brand-actions {
  display: inline-flex;
  gap: 6px;
}

.brand-name {
  margin: 0;
  color: #0f172a;
  font-size: 1.02rem;
}

.brands-empty {
  margin: 0 auto;
  color: #64748b;
}

@media (max-width: 768px) {
  .brands-hero {
    padding: 14px;
  }
}
</style>

