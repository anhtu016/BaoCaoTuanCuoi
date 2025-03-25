<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const newTask = ref('');
const newTaskStatus = ref('pending');
const description = ref('');
const assignedTo = ref('');
const dueDate = ref('');
const errorMessage = ref('');
const users = ref([]); // Danh sách người dùng

// Lấy danh sách người dùng từ API khi component được mounted
onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/users', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    users.value = response.data;
  } catch (error) {
    console.error('Lỗi khi lấy danh sách người dùng:', error);
  }
});

const createTask = async () => {
  if (!newTask.value.trim() || !assignedTo.value || !dueDate.value) {
    errorMessage.value = alert('Vui lòng nhập đầy đủ thông tin');
    return;
  }

  try {
    await axios.post(
      'http://127.0.0.1:8000/api/tasks',
      { 
        name: newTask.value, 
        status: newTaskStatus.value,
        assigned_to: assignedTo.value, 
        due_date: dueDate.value,
        description: description.value,
      },
      { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    );

    router.push('/Dashboard'); // Quay lại danh sách công việc
  } catch (error) {
    console.error('Lỗi khi tạo công việc:', error);
    errorMessage.value = 'Lỗi khi tạo công việc.';
  }
};
</script>

<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <h2 class="text-center">Thêm công việc</h2>

    <div v-if="errorMessage" class="text-red-500 mb-4">{{ errorMessage }}</div>

    <div class="container-sm">
      <div class="mb-3">
        <label class="block mb-2">Tên công việc:</label>
        <input v-model="newTask" placeholder="Nhập công việc mới" class="form-control" />
      </div>

      <div class="mb-3">
        <label class="block mb-2">Ngày hết hạn:</label>
        <input type="date" v-model="dueDate" class="form-control" />
      </div>

      <div class="mb-3">
        <label class="block mb-2">Người phụ trách:</label>
        <select v-model="assignedTo" class="form-control">
          <option value="" disabled>Chọn người phụ trách</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
      </div>

      <div class="mb-3">
        <label class="block mb-2">Mô tả:</label>
        <textarea v-model="description" placeholder="Nhập mô tả" class="form-control"></textarea>
      </div>

      <div class="mb-3">
        <label class="block mb-2">Trạng thái:</label>
        <select v-model="newTaskStatus" class="form-control">
          <option value="pending">Chưa bắt đầu</option>
          <option value="in_progress">Đang thực hiện</option>
          <option value="completed">Đã hoàn thành</option>
        </select>
      </div>

      <button @click="createTask" class="btn btn-success mt-4">
        Thêm công việc
      </button>
    </div>
  </div>
</template>
