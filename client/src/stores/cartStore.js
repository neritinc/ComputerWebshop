import { defineStore } from "pinia";
import { useToastStore } from "@/stores/toastStore";

export const useCartStore = defineStore("cart", {
  state: () => ({
    items: JSON.parse(localStorage.getItem("cart_items") || "[]"),
  }),
  getters: {
    itemCount(state) {
      return state.items.reduce((sum, item) => sum + Number(item.qty || 0), 0);
    },
    totalPrice(state) {
      return state.items.reduce(
        (sum, item) => sum + Number(item.qty || 0) * Number(item.price || 0),
        0,
      );
    },
  },
  actions: {
    persist() {
      localStorage.setItem("cart_items", JSON.stringify(this.items));
    },
    async addToCart(product, qty = 1) {
      const productId = Number(product?.id);
      if (!productId) return;
      const existing = this.items.find((i) => Number(i.productId) === productId);
      if (existing) {
        existing.qty += Number(qty) || 1;
      } else {
        this.items.push({
          productId,
          qty: Number(qty) || 1,
          name: product?.name || "Product",
          price: Number(product?.price || 0),
        });
      }
      this.persist();
      const toast = useToastStore();
      toast.messages.push("Added to cart.");
      toast.show("Success");
    },
    removeFromCart(productId) {
      const id = Number(productId);
      this.items = this.items.filter((item) => Number(item.productId) !== id);
      this.persist();
    },
    setQty(productId, qty) {
      const id = Number(productId);
      const target = this.items.find((item) => Number(item.productId) === id);
      if (!target) return;
      const value = Number(qty);
      if (!Number.isFinite(value) || value <= 0) {
        this.removeFromCart(id);
        return;
      }
      target.qty = Math.floor(value);
      this.persist();
    },
    clearCart() {
      this.items = [];
      this.persist();
    },
  },
});
