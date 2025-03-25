<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const showForm = ref(false);
const selectedUser = ref(null);

const token = localStorage.getItem('token');

// Lấy danh sách người dùng
const fetchUsers = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/users', {
      headers: { Authorization: `Bearer ${token}` }
    });
    users.value = response.data;
  } catch (error) {
    console.error('Lỗi khi tải danh sách người dùng:', error);
  }
};

// Xóa người dùng
const deleteUser = async (id) => {
  if (!confirm("Bạn có chắc chắn muốn xóa người dùng này?")) return;
  try {
    await axios.delete(`http://127.0.0.1:8000/api/users/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    users.value = users.value.filter(user => user.id !== id);
  } catch (error) {
    console.error('Lỗi khi xóa người dùng:', error);
  }
};

// Mở form thêm người dùng
const addUser = () => {
  selectedUser.value = { name: '', email: '', password: '' };
  showForm.value = true;
};

// Mở form sửa người dùng
const editUser = (user) => {
  selectedUser.value = { ...user, password: '' };
  showForm.value = true;
};

// Lưu người dùng (Thêm hoặc Cập nhật)
const saveUser = async () => {
  try {
    const payload = {
      name: selectedUser.value.name,
      email: selectedUser.value.email,
    };

    // Nếu thêm mới thì cần có password
    if (!selectedUser.value.id) {
      payload.password = selectedUser.value.password;
    }

    if (selectedUser.value.id) {
      await axios.put(
        `http://127.0.0.1:8000/api/users/${selectedUser.value.id}`,
        payload,
        { headers: { Authorization: `Bearer ${token}` } }
      );
    } else {
      await axios.post(
        'http://127.0.0.1:8000/api/users',
        payload,
        { headers: { Authorization: `Bearer ${token}` } }
      );
    }

    alert("Lưu thành công!");
    fetchUsers();
    showForm.value = false;
  } catch (error) {
    console.error('Lỗi khi lưu người dùng:', error.response?.data || error);
    alert(`Lỗi: ${error.response?.data?.message || 'Không thể lưu người dùng'}`);
  }
};

onMounted(fetchUsers);
</script>

<template>
  <div class="container">
    <h1 class="text-center text-xl font-bold mb-4">Quản lý Người Dùng</h1>

    <button @click="addUser" class="btn btn-success mb-4">Thêm người dùng</button>

    <table class="table table-hover text-center">
      <thead>
        <tr class="bg-gray-200">
          <th class="border p-2">Tên</th>
          <th class="border p-2">Email</th>
          <th class="border p-2">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td class="border p-2">{{ user.name }}</td>
          <td class="border p-2">{{ user.email }}</td>
          <td class="border p-2">
            <button @click="editUser(user)" class="btn btn-warning m-2">Sửa</button>
            <button @click="deleteUser(user.id)" class="btn btn-danger m-2">Xóa</button>
          </td>
        </tr>
      </tbody>
    </table>
    <a href="./Dashboard">   <button  class="btn btn-success mt-2">Back</button></a>
    <!-- Form Thêm & Sửa -->
    <div v-if="showForm" class="mt-4 p-4 bg-white shadow rounded">
      <h2 class="text-lg font-bold">{{ selectedUser.id ? 'Cập nhật' : 'Thêm' }} người dùng</h2>
      <div>
        <label class="block">Tên:</label>
        <input v-model="selectedUser.name" class="form-control border p-2 w-full" />
      </div>
      <div>
        <label class="block">Email:</label>
        <input v-model="selectedUser.email" class="form-control border p-2 w-full" />
      </div>
      <div>
        <label class="block">Mật khẩu:</label>
        <input type="password" v-model="selectedUser.password" class="form-control border p-2 w-full" />
      </div>
      <div class="mt-3">
        <button @click="saveUser" class="btn btn-success">
          {{ selectedUser.id ? 'Cập nhật' : 'Thêm' }}
        </button>
        <button @click="showForm = false" class="btn btn-danger m-2">Hủy</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ddd;
  padding: 8px;
}
th {
  background-color: #f4f4f4;
}
</style>
