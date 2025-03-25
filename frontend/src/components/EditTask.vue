<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const task = ref({
  name: '',
  due_date: '',
  assigned_to: '',
  status: 'pending',
  description: '',
});
const users = ref([]); // Danh sách người dùng
const loading = ref(false);
const errorMessage = ref('');

// Lấy token từ localStorage
const token = localStorage.getItem('token');
if (!token) {
  alert('Bạn chưa đăng nhập! Vui lòng đăng nhập lại.');
  router.push('/login');
}

// Lấy danh sách người dùng từ API
const fetchUsers = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/users', {
      headers: { Authorization: `Bearer ${token}` }
    });
    users.value = response.data;
  } catch (error) {
    console.error('Lỗi khi tải danh sách người dùng:', error.response);
  }
};

// Lấy thông tin công việc từ API
onMounted(async () => {
  if (!route.params.id) {
    alert('Không tìm thấy ID công việc!');
    router.push('/dashboard');
    return;
  }

  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/tasks/${route.params.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    if (response.data) {
      task.value = { ...response.data };
      await fetchUsers(); // Lấy danh sách người dùng trước khi cập nhật
      await nextTick(); // Đảm bảo Vue cập nhật xong
    }
  } catch (error) {
    console.error('Lỗi khi tải công việc:', error.response);
    errorMessage.value = 'Không thể tải công việc. Hãy thử lại!';
  }
});

// Cập nhật công việc
const updateTask = async () => {
  if (!task.value.name.trim() || !task.value.due_date || !task.value.assigned_to) {
    errorMessage.value =  alert('vui lòng nhập đủ thông tin');
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    await axios.put(
      `http://127.0.0.1:8000/api/tasks/${route.params.id}`,
      task.value,
      { headers: { Authorization: `Bearer ${token}` } }
    );

    alert('Cập nhật công việc thành công!');
    router.push('/dashboard');
  } catch (error) {
    console.error('Lỗi khi cập nhật công việc:', error.response);
    errorMessage.value = 'Lỗi khi cập nhật công việc. ' + (error.response?.data?.message || '');
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-center text-lg font-bold">Chỉnh sửa công việc</h1>
    <div v-if="errorMessage" class="text-red-500 mb-4">{{ errorMessage }}</div>
  
    <div class="container">
      <div class="mb-3">
        <label class="block mb-2">Tên công việc:</label>
        <input v-model="task.name" class="form-control" />
      </div>

      <div class="mb-3">
        <label class="block mb-2">Ngày hết hạn:</label>
        <input type="date" v-model="task.due_date" class="form-control" />  
      </div>

      <div class="mb-3">
        <label class="block mb-2">Người phụ trách:</label>
        <select v-model="task.assigned_to" class="form-control">
          <option value="" disabled>Chọn người phụ trách</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
      </div>

      <div class="mb-3">
        <label class="block mb-2">Trạng thái:</label>
        <select v-model="task.status" class="form-control">
          <option value="pending">Chưa bắt đầu</option>
          <option value="in_progress">Đang thực hiện</option>
          <option value="completed">Đã hoàn thành</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="block mb-2">Mô tả:</label>
        <textarea v-model="task.description" class="form-control"></textarea>
      </div>

      <button @click="updateTask" :disabled="loading"
        class="bg-blue-500 px-4 py-2 rounded mt-4 hover:bg-blue-600 disabled:opacity-50">
        {{ loading ? "Đang cập nhật..." : "Cập nhật công việc" }}
      </button>
    </div>
  </div>
</template>
