import apiClient from "./axiosClient";

const route = "/users";

export default {
  async getAllSortSearch() {
    return await apiClient.get(route);
  },

  async getAll() {
    return await apiClient.get(route);
  },

  async getById(id) {
    return await apiClient.get(`${route}/${id}`);
  },

  async create(data) {
    delete data.id;
    return await apiClient.post(route, data);
  },

  async update(id, data) {
    delete data.id;
    return await apiClient.patch(`${route}/${id}`, data);
  },

  async delete(id) {
    return await apiClient.delete(`${route}/${id}`);
  },
};
