<template>
  <div class="cart-page">
    <section class="cart-hero">
      <div>
        <p class="kicker">Shopping</p>
        <h1>Your Cart</h1>
      </div>
      <span class="count">{{ itemCount }} items</span>
    </section>

    <section class="cart-card" v-if="items.length">
      <div class="table-responsive">
        <table class="table align-middle m-0">
          <thead>
            <tr>
              <th>Product</th>
              <th style="width: 120px">Qty</th>
              <th>Unit Price</th>
              <th>Total</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.productId">
              <td>{{ item.name }}</td>
              <td>
                <input
                  class="form-control form-control-sm"
                  type="number"
                  min="1"
                  :value="item.qty"
                  @change="onQtyChange(item.productId, $event)"
                />
              </td>
              <td><UsdPrice :value="item.price" /></td>
              <td><UsdPrice :value="Number(item.price || 0) * Number(item.qty || 0)" /></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-danger" @click="removeFromCart(item.productId)">
                  Remove
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="cart-footer">
        <strong>Total: <UsdPrice :value="totalPrice" /></strong>
        <button class="btn btn-outline-secondary btn-sm" @click="clearCart">Clear cart</button>
      </div>
    </section>

    <section v-else class="cart-empty">
      <i class="bi bi-cart3"></i>
      <p class="m-0">Your cart is empty.</p>
    </section>
  </div>
</template>

<script>
import { mapState, mapActions } from "pinia";
import { useCartStore } from "@/stores/cartStore";
import UsdPrice from "@/components/Common/UsdPrice.vue";

export default {
  name: "CartView",
  components: { UsdPrice },
  computed: {
    ...mapState(useCartStore, ["items", "itemCount", "totalPrice"]),
  },
  methods: {
    ...mapActions(useCartStore, ["removeFromCart", "setQty", "clearCart"]),
    onQtyChange(productId, event) {
      this.setQty(productId, Number(event?.target?.value || 1));
    },
  },
};
</script>

<style scoped>
.cart-page {
  display: grid;
  gap: 14px;
}
.cart-hero {
  border: 1px solid #dbe8fb;
  border-radius: 16px;
  padding: 16px;
  background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  box-shadow: 0 10px 26px rgba(37, 99, 235, 0.08);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 10px;
}
.kicker {
  margin: 0 0 6px;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #2563eb;
  font-weight: 700;
}
.count {
  color: #64748b;
  font-weight: 700;
}
.cart-card,
.cart-empty {
  border: 1px solid #dbe8fb;
  border-radius: 12px;
  padding: 14px;
  background: #fff;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}
.cart-footer {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.cart-empty {
  display: grid;
  place-items: center;
  gap: 8px;
  color: #64748b;
}
.cart-empty i {
  font-size: 1.5rem;
}
</style>
