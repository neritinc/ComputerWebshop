<template>
  <span class="usd-price">
    <span v-if="parts.sign">{{ parts.sign }}</span><span class="usd-symbol">{{ parts.currency }}</span>{{ parts.amount }}
  </span>
</template>

<script>
import { formatUsdParts } from "@/utils/currency";

export default {
  name: "UsdPrice",
  props: {
    value: {
      type: [Number, String],
      required: true,
    },
  },
  computed: {
    parts() {
      return formatUsdParts(this.value);
    },
  },
};
</script>

<style scoped>
.usd-price {
  display: inline-flex;
  align-items: baseline;
  gap: 0.03em;
}

.usd-symbol {
  font-family: "PricedownDollar", sans-serif;
  font-size: 1.22em;
  line-height: 0.9;
  display: inline-block;
  transform: translateY(0.06em);
}
</style>
