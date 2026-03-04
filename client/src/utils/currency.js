export function formatUsd(value) {
  const n = Number(value);
  if (Number.isNaN(n)) return String(value ?? "-");
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    maximumFractionDigits: 2,
  }).format(n);
}
