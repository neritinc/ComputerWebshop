function normalizeImageFile(raw) {
  const cleaned = String(raw || "").trim().replace(/\\/g, "/");
  if (!cleaned) return "";

  if (/^https?:\/\//i.test(cleaned)) {
    try {
      const u = new URL(cleaned);
      const m = u.pathname.match(/\/images\/products\/(.+)$/i);
      return m?.[1] ? m[1] : "";
    } catch {
      return "";
    }
  }

  return cleaned.replace(/^\/+/, "").replace(/^images\/products\//i, "");
}

export function getProductImageCandidates(imagePath) {
  const raw = String(imagePath || "").trim();
  if (!raw) return [];

  const apiBase = String(import.meta.env.VITE_API_URL || "").replace(/\/api\/?$/, "");
  const fromEnv = apiBase ? [apiBase] : [];
  const fromRuntimeApi = typeof window !== "undefined" && window.__API_ORIGIN
    ? [String(window.__API_ORIGIN)]
    : [];
  const fromWindow = typeof window !== "undefined" ? [window.location.origin] : [];
  const localhostFallbacks = ["http://localhost:8000", "http://127.0.0.1:8000"];

  const hosts = [...new Set([...fromEnv, ...fromRuntimeApi, ...fromWindow, ...localhostFallbacks].filter(Boolean))];
  const candidates = [];

  if (/^https?:\/\//i.test(raw)) {
    candidates.push(raw);
  }

  const normalized = normalizeImageFile(raw);
  if (!normalized) return [...new Set(candidates)];

  const encodedPath = normalized
    .split("/")
    .map((part) => encodeURIComponent(part))
    .join("/");

  for (const host of hosts) {
    candidates.push(`${host}/images/products/${encodedPath}`);
  }

  // Relative fallback
  candidates.push(`/images/products/${encodedPath}`);

  return [...new Set(candidates.filter(Boolean))];
}

export function getProductImageUrl(imagePath) {
  return getProductImageCandidates(imagePath)[0] || "";
}
