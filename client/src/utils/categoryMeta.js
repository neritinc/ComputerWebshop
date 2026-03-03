const CATEGORY_ICON_MAP = {
  processor: "bi-cpu",
  "memory module": "bi-memory",
  memory: "bi-memory",
  motherboard: "bi-motherboard",
  "graphics card": "bi-gpu-card",
  storage: "bi-device-ssd",
  "external storage": "bi-device-hdd",
  "power supply": "bi-plug",
  cooling: "bi-fan",
  cooler: "bi-snow",
  case: "bi-pc",
  "computer case": "bi-pc-display",
  "case fan": "bi-fan",
  "optical drive": "bi-disc",
  "network card": "bi-router",
  "sound card": "bi-music-note-beamed",
  "usb hub": "bi-usb-symbol",
  monitor: "bi-display",
  keyboard: "bi-keyboard",
  mouse: "bi-mouse",
  headset: "bi-headphones",
  speaker: "bi-speaker",
  webcam: "bi-camera-video",
  microphone: "bi-mic",
};

export const COMPONENT_CATEGORY_KEYS = [
  "processor",
  "memory module",
  "motherboard",
  "graphics card",
  "storage",
  "power supply",
  "cooling",
  "case",
  "optical drive",
  "network card",
  "sound card",
  "usb hub",
  "case fan",
  "memory",
  "cooler",
  "computer case",
];

export const ACCESSORY_CATEGORY_KEYS = [
  "monitor",
  "keyboard",
  "mouse",
  "headset",
  "speaker",
  "webcam",
  "microphone",
  "external storage",
];

export function normalizeCategoryKey(value) {
  return String(value || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .trim();
}

export function resolveCategoryIconClass(categoryName) {
  return CATEGORY_ICON_MAP[normalizeCategoryKey(categoryName)] || "bi-tag";
}
