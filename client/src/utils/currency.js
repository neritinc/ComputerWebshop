const usdFormatter = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD",
  maximumFractionDigits: 2,
});

export function formatUsd(value) {
  const n = Number(value);
  if (Number.isNaN(n)) return String(value ?? "-");
  return usdFormatter.format(n);
}

export function formatUsdParts(value) {
  const n = Number(value);
  if (Number.isNaN(n)) {
    return { sign: "", currency: "$", amount: String(value ?? "-") };
  }

  const parts = usdFormatter.formatToParts(n);
  const sign = parts
    .filter((part) => part.type === "minusSign" || part.type === "plusSign")
    .map((part) => part.value)
    .join("");
  const currency = parts.find((part) => part.type === "currency")?.value || "$";
  const amount = parts
    .filter((part) => !["minusSign", "plusSign", "currency"].includes(part.type))
    .map((part) => part.value)
    .join("")
    .trim();

  return { sign, currency, amount };
}
