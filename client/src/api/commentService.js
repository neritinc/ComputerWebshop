import apiClient from "./axiosClient";

const route = "/comments";

export default {
  async getAll() {
    return await apiClient.get(route);
  },
  async getById(id) {
    return await apiClient.get(`${route}/${id}`);
  },
  async update(id, data) {
    return await apiClient.patch(`${route}/${id}`, data);
  },
  async delete(id) {
    return await apiClient.delete(`${route}/${id}`);
  },
};
