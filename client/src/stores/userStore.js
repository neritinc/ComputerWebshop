import { defineStore } from "pinia";
import { useSearchStore } from "./searchStore";
import { useToastStore } from "./toastStore";
import service from "@/api/userService";

class Item {
  constructor(id = 0, name = "", email = "", role = 3) {
    this.id = id;
    this.name = name;
    this.email = email;
    this.role = role;
  }
}

function sortAndFilter(items, column, direction, searchWord) {
  const search = (searchWord || "").trim().toLowerCase();

  let filtered = items;
  if (search) {
    filtered = items.filter((item) =>
      Object.values(item).some((value) =>
        String(value ?? "").toLowerCase().includes(search),
      ),
    );
  }

  return [...filtered].sort((a, b) => {
    const av = a?.[column];
    const bv = b?.[column];

    if (av == null && bv == null) return 0;
    if (av == null) return direction === "asc" ? -1 : 1;
    if (bv == null) return direction === "asc" ? 1 : -1;

    if (typeof av === "number" && typeof bv === "number") {
      return direction === "asc" ? av - bv : bv - av;
    }

    const result = String(av).localeCompare(String(bv), "hu", {
      numeric: true,
      sensitivity: "base",
    });
    return direction === "asc" ? result : -result;
  });
}

export const useUserStore = defineStore("user", {
  state: () => ({
    item: new Item(),
    items: [new Item()],
    loading: false,
    error: null,
    sortColumn: "id",
    sortDirection: "asc",
    searchStore: useSearchStore(),
  }),
  getters: {
    getItemsLength() {
      return this.items.length;
    },
  },
  actions: {
    clearItem() {
      this.item = new Item();
    },

    async getAllSortSearch(column = "id", direction = null) {
      this.loading = true;
      this.error = null;
      this.sortColumn = column;

      if (!direction) {
        direction =
          this.sortColumn === column && this.sortDirection === "asc"
            ? "desc"
            : "asc";
      }
      this.sortDirection = direction;

      try {
        const response = await service.getAllSortSearch();
        this.items = sortAndFilter(
          response.data,
          this.sortColumn,
          this.sortDirection,
          this.searchStore.searchWord,
        );
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async getAll() {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getAll();
        this.items = sortAndFilter(
          response.data,
          this.sortColumn,
          this.sortDirection,
          this.searchStore.searchWord,
        );
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async getById(id) {
      this.loading = true;
      this.error = null;

      try {
        const response = await service.getById(id);
        this.item = response.data;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      this.loading = true;
      this.error = null;
      try {
        await service.create(data);
        await this.getAll();
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async createUser(data) {
      this.loading = true;
      this.error = null;
      try {
        await service.create(data);
        const toast = useToastStore();
        toast.messages.push("User sikeresen letrehozva!");
        toast.show("Success");
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, updateData) {
      this.loading = true;
      this.error = null;
      try {
        await service.update(id, updateData);
        await this.getAll();
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async delete(id) {
      this.loading = true;
      this.error = null;
      try {
        await service.delete(id);
        await this.getAll();
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
