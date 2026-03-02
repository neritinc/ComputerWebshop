<template>
  <div>
    <div class="d-flex align-items-center m-0 mb-2">
      <h1>{{ pageTitle }}</h1>
      <div class="d-flex align-items-center m-0 ms-2">
        <i
          v-if="loading"
          class="bi bi-hourglass-split fs-3 col-auto p-0 pe-1"
        ></i>
        <ButtonsCrudCreate v-if="!loading" @create="createHandler" />
        <p class="m-0 ms-2">({{ getItemsLength }})</p>
      </div>
    </div>

    <GenericTable
      :items="items"
      :columns="tableColumns"
      :useCollectionStore="useCollectionStore"
      @delete="deleteHandler"
      @update="updateHandler"
      @create="createHandler"
      @sort="sortHandler"
      v-if="items.length > 0"
    />
    <div v-else style="width: 100px" class="m-auto">Nincs talalat</div>

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
import GenericTable from "@/components/Table/GenericTable.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import ButtonsCrudCreate from "@/components/Table/ButtonsCrudCreate.vue";
import FormBrand from "@/components/Forms/FormBrand.vue";

export default {
  name: "BrandsView",
  components: {
    GenericTable,
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
      pageTitle: "Gyartok",
      tableColumns: [
        { key: "id", label: "ID", debug: import.meta.env.VITE_DEBUG_MODE },
        { key: "brandName", label: "Brand Name", debug: 2 },
      ],
      useCollectionStore: useBrandStore,
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

