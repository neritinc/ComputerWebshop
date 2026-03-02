<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler">
      <div class="mb-4 row pt-2">
        <label for="categoryName" class="col-form-label col-auto pt-1 pe-0">Category Name:</label>
        <div class="col">
          <input
            id="categoryName"
            v-model="formItem.categoryName"
            type="text"
            class="form-control"
            @input="clearError('categoryName')"
            required
          />
          <div v-if="!serverErrors.categoryName" class="invalid-feedback position-absolute">
            Category name is required
          </div>
          <div v-if="serverErrors.categoryName" class="invalid-feedback position-absolute d-block">
            {{ serverErrors.categoryName[0] }}
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";

export default {
  emits: ["yesEventForm"],
  name: "FormCategory",
  components: { Modal },
  props: {
    title: { type: String, default: "Create Category" },
    item: { type: Object },
  },
  data() {
    return {
      formItem: { ...this.item },
      serverErrors: {},
    };
  },
  watch: {
    item(value) {
      this.formItem = { ...value };
    },
  },
  methods: {
    show() {
      this.serverErrors = {};
      this.$refs.modal.show();
    },
    hide() {
      this.$refs.modal.hide();
    },
    setServerErrors(errors) {
      this.serverErrors = errors;
    },
    clearError(field) {
      if (this.serverErrors[field]) {
        delete this.serverErrors[field];
      }
    },
    yesEventHandler(done) {
      this.$emit("yesEventForm", { item: this.formItem, done });
    },
  },
};
</script>
