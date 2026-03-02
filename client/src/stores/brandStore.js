import { defineStore } from "pinia";
import { useSearchStore } from "./searchStore";
import service from "@/api/brandService";

class Item {
  constructor(id = 0, brandName = "") {
    this.id = id;
    this.brandName = brandName;
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
    const result = String(av ?? "").localeCompare(String(bv ?? ""), "hu", {
      numeric: true,
      sensitivity: "base",
    });
    return direction === "asc" ? result : -result;
  });
}

export const useBrandStore = defineStore("brands", {
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

    async getAllAlphabetical() {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getAllAlphabetical();
        this.items = response.data;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
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
      return await this.getAllSortSearch(this.sortColumn, this.sortDirection);
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
        await this.getAllSortSearch(this.sortColumn, this.sortDirection);
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
        await this.getAllSortSearch(this.sortColumn, this.sortDirection);
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
        await this.getAllSortSearch(this.sortColumn, this.sortDirection);
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

