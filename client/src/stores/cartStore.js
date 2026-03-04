import { defineStore } from "pinia";
import { useToastStore } from "@/stores/toastStore";

export const useCartStore = defineStore("cart", {
  state: () => ({
    items: JSON.parse(localStorage.getItem("cart_items") || "[]"),
  }),
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
  },
});
