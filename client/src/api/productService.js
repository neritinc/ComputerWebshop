import apiClient from "./axiosClient";

const route = "/products";

export default {
  async getAll(params = {}) {
    return await apiClient.get(route, { params });
  },
};

