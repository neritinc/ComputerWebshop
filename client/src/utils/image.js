export function getProductImageUrl(imagePath) {
  const raw = String(imagePath || "").trim();
  if (!raw) return "";
  if (/^https?:\/\//i.test(raw)) return raw;

  const normalized = raw
    .replace(/\\/g, "/")
    .replace(/^\/+/, "")
    .replace(/^images\/products\//i, "");
  if (!normalized) return "";

  const encodedPath = normalized
    .split("/")
    .map((part) => encodeURIComponent(part))
    .join("/");

  const apiBase = String(import.meta.env.VITE_API_URL || "").replace(/\/api\/?$/, "");
  return `${apiBase}/images/products/${encodedPath}`;
}
