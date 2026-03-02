import apiClient from "./axiosClient";

const route = "/categories";

function toClientItem(item) {
  return {
    ...item,
    sportNev: item.category_name,
  };
}

function toServerItem(data) {
  return {
    category_name: data.sportNev,
  };
}

export default {
  async getAllSortSearch() {
    const response = await apiClient.get(route);
    response.data = response.data.map(toClientItem);
    return response;
  },

  async getAll() {
    const response = await apiClient.get(route);
    response.data = response.data.map(toClientItem);
    return response;
  },

  async getById(id) {
    const response = await apiClient.get(`${route}/${id}`);
    response.data = toClientItem(response.data);
    return response;
  },

  async create(data) {
    const payload = toServerItem(data);
    return await apiClient.post(route, payload);
  },

  async update(id, data) {
    const payload = toServerItem(data);
    return await apiClient.patch(`${route}/${id}`, payload);
  },

  async delete(id) {
    return await apiClient.delete(`${route}/${id}`);
  },
};
