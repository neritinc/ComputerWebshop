<template>
  <div class="comments-admin">
    <section class="hero">
      <div>
        <p class="kicker">Admin</p>
        <h1>Comments Moderation</h1>
        <p class="subtitle">Review, edit or remove product comments.</p>
      </div>
      <span class="count">{{ filteredComments.length }} comments</span>
    </section>

    <section class="card-box">
      <div class="table-responsive" v-if="!loading && filteredComments.length">
        <table class="table align-middle table-hover m-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>User</th>
              <th>Product</th>
              <th>Comment</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in filteredComments" :key="row.id">
              <td>#{{ row.id }}</td>
              <td>{{ row?.user?.name || "Unknown user" }}</td>
              <td>{{ row?.product?.name || "-" }}</td>
              <td>
                <div class="comment-cell">{{ row.comment }}</div>
              </td>
              <td class="text-end">
                <div class="actions">
                  <button class="btn btn-sm btn-outline-primary" @click="startEdit(row)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="askDelete(row)">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else-if="loading" class="state-wrap">Loading comments...</div>
      <div v-else class="state-wrap">No comments found.</div>
    </section>

    <section class="card-box mt-3" v-if="editItem">
      <h5 class="mb-2">Edit comment #{{ editItem.id }}</h5>
      <p class="text-secondary mb-2">
        {{ editItem?.user?.name || "Unknown user" }} on {{ editItem?.product?.name || "-" }}
      </p>
      <textarea v-model.trim="editText" class="form-control mb-3" rows="4" />
      <div class="d-flex gap-2">
        <button class="btn btn-primary" :disabled="saving" @click="saveEdit">
          {{ saving ? "Saving..." : "Save changes" }}
        </button>
        <button class="btn btn-outline-secondary" :disabled="saving" @click="cancelEdit">Cancel</button>
      </div>
    </section>

    <ConfirmModal
      :isOpenConfirmModal="showDeleteConfirm"
      title="Delete comment"
      message="Delete selected comment?"
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
import commentService from "@/api/commentService";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";

export default {
  name: "CommentsModerationView",
  components: { ConfirmModal },
  data() {
    return {
      loading: false,
      saving: false,
      comments: [],
      editItem: null,
      editText: "",
      showDeleteConfirm: false,
      pendingDelete: null,
    };
  },
  computed: {
    ...mapState(useSearchStore, ["searchWord"]),
    filteredComments() {
      const term = String(this.searchWord || "").trim().toLowerCase();
      if (!term) return this.comments;

      return this.comments.filter((row) =>
        [row.id, row.comment, row?.user?.name, row?.product?.name]
          .map((v) => String(v ?? "").toLowerCase())
          .some((v) => v.includes(term)),
      );
    },
  },
  methods: {
    async loadComments() {
      this.loading = true;
      try {
        const response = await commentService.getAll();
        this.comments = response.data || [];
      } finally {
        this.loading = false;
      }
    },
    startEdit(row) {
      this.editItem = row;
      this.editText = row.comment || "";
      window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });
    },
    cancelEdit() {
      this.editItem = null;
      this.editText = "";
    },
    async saveEdit() {
      if (!this.editItem?.id) return;
      this.saving = true;
      try {
        await commentService.update(this.editItem.id, { comment: this.editText });
        await this.loadComments();
        this.cancelEdit();
      } finally {
        this.saving = false;
      }
    },
    askDelete(row) {
      this.pendingDelete = row;
      this.showDeleteConfirm = true;
    },
    async confirmDelete() {
      if (!this.pendingDelete?.id) return;
      await commentService.delete(this.pendingDelete.id);
      this.showDeleteConfirm = false;
      if (this.editItem?.id === this.pendingDelete.id) {
        this.cancelEdit();
      }
      this.pendingDelete = null;
      await this.loadComments();
    },
  },
  async mounted() {
    await this.loadComments();
  },
};
</script>

<style scoped>
.comments-admin {
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

.comment-cell {
  max-width: 460px;
  white-space: pre-wrap;
}

.state-wrap {
  color: #64748b;
}
</style>
