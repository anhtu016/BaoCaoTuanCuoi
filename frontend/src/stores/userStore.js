import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useUserStore = defineStore('user', () => {
    const user = ref(null);

    const fetchUser = async () => {
        try {
            const response = await axios.get('http://127.0.0.1:8000/api/user', {
                headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
            });
            user.value = response.data;
        } catch (error) {
            console.error('Lỗi lấy thông tin user:', error);
        }
    };

    return { user, fetchUser };
});
