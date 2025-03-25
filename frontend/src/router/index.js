import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import CreateTask from '../components/CreateTask.vue';
import EditTask from '../components/EditTask.vue';
import User from '../components/User.vue';
import Mytask from '../components/MyTask.vue';

const routes = [
    { path: '/', redirect: '/dashboard' }, // Chuyển hướng mặc định
    { path: '/login', name: 'Login', component: Login },
    { path: '/register', name: 'Register', component: Register },
    { path: '/dashboard', name: 'Dashboard', component: Dashboard },
    { path: '/tasks/create', name: 'CreateTask', component: CreateTask },
    { path: '/tasks/:id/edit', name: 'EditTask', component: EditTask }, // Trang chỉnh sửa công việc
    { path: '/User', name: 'User', component: User },
    { path: '/Mytask', name: 'Mytask', component: Mytask },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
