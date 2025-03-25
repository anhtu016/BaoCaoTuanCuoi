<script setup>
import { ref, computed, onMounted } from 'vue';
import { useUserStore } from '../stores/userStore';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const userStore = useUserStore();
const filter = ref("all");
const tasks = ref([]);

onMounted(() => {
  userStore.fetchUser();
  fetchTasks();
});

// Lấy danh sách công việc từ API
const fetchTasks = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/tasks', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    tasks.value = response.data.tasks; // Đảm bảo lấy danh sách đúng từ API
  } catch (error) {
    console.error('Lỗi khi lấy danh sách công việc:', error);
  }
};

// Cập nhật trạng thái công việc
const updateTaskStatus = async (task, newStatus) => {
  try {
    await axios.put(`http://127.0.0.1:8000/api/tasks/${task.id}`,
      { status: newStatus },
      { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    );

    // Cập nhật UI ngay lập tức
    task.status = newStatus;
  } catch (error) {
    console.error('Lỗi khi cập nhật trạng thái công việc:', error);
  }
};

// Xóa công việc
const deleteTask = async (id) => {
  try {
    const response = await axios.delete(`http://127.0.0.1:8000/api/tasks/${id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    if (response.status === 200) {
      // Cập nhật danh sách công việc sau khi xóa
      tasks.value = tasks.value.filter(task => task.id !== id);
    }
  } catch (error) {
    console.error('Lỗi khi xóa công việc:', error);
  }
};

// Lọc danh sách công việc theo trạng thái
const filteredTasks = computed(() => {
  if (filter.value === "all") return tasks.value;
  return tasks.value.filter((task) => task.status === filter.value);
});

// Đăng xuất
const logout = async () => {
  try {
    await axios.post('http://127.0.0.1:8000/api/logout', {}, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    localStorage.removeItem('token');
    router.push('/login');
  } catch (error) {
    console.error('Lỗi khi đăng xuất:', error);
  }
};
</script>

<template>
  <div class="text-center">
    <h2>Thông tin người dùng</h2>
    <div v-if="userStore.user">
      <p><strong>Tên:</strong> {{ userStore.user.name }}</p>
      <p><strong>Email:</strong> {{ userStore.user.email }}</p>    
      <button @click="logout" class="btn btn-info">Đăng xuất</button>
    </div>
    <div v-else>
      <p>Đang tải thông tin user...</p>    
      <a href="./Login"><button class="btn btn-primary">Đăng nhập</button></a> |
      <a href="./Register"><button class="btn btn-success">Đăng ký</button></a>
    </div>      
  </div>

  <div class="p-6 bg-gray-100 min-h-screen">
    <h2 class="">Danh sách công việc</h2>
    <div class="border p-2 d-flex">
      <select v-model="filter" class="p-2 border m-3">
        <option value="all">Tất cả</option>
        <option value="pending">Chưa bắt đầu</option>
        <option value="in_progress">Đang thực hiện</option>
        <option value="completed">Đã hoàn thành</option>
      </select> 
   
      <button @click="router.push('/tasks/create')" class="btn btn-success border m-3 ms-auto">   
        Thêm công việc
      </button> 
      <a href="./User"><button class="btn btn-primary border m-3"> Danh sách người dùng</button></a>
      <a href="./MyTask"><button  class="btn btn-secondary border m-3">Công việc của tôi</button></a>
    </div>

    <div class="bg-white p-4 shadow rounded text-center">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="border p-2">Tên công việc</th>
            <th class="border p-2">Trạng thái</th>
            <th class="border p-2">Ngày hết hạn</th>
            <th class="border p-2">Người phụ trách</th>
            <th class="border p-2">Mô tả</th>
            <th class="border p-2">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in filteredTasks" :key="task.id" class="text-center">
            <td class="border p-2">{{ task.name }}</td>
            <td class="border p-2">
              <select :value="task.status" @change="updateTaskStatus(task, $event.target.value)" class="p-1 border rounded">
                <option value="pending">Chưa bắt đầu</option>
                <option value="in_progress">Đang thực hiện</option>
                <option value="completed">Đã hoàn thành</option>
              </select>
            </td>
            <td class="border p-2">{{ task.due_date }}</td>
            <td class="border p-2">
              {{ task.user ? task.user.name : 'Chưa phân công' }}
            </td>
            <td class="border p-2">{{ task.description }}</td>
            <td class="border p-2">
              <button @click="router.push({ name: 'EditTask', params: { id: task.id } })" class="btn btn-warning border m-2"> Sửa </button>
              <button @click="deleteTask(task.id)" class="text-red-500 px-2 btn btn-danger border m-2">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
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
