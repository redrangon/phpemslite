import {defineStore} from 'pinia';
import api from '@/framework/api';
import {decrypt, encrypt} from "@/framework/security";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: decrypt(localStorage.getItem('user'),true) || {},
        basic:decrypt(localStorage.getItem('basic'),true) || {}
    }),

    actions: {
        getCurrentUser: async function () {
            try{
                this.user = await api.userApi.getCurrentUser();
            }catch (e){
                this.user = null;
            }
            finally {
                localStorage.setItem('user', encrypt(this.user,true));
            }
        },
        clearUser: function () {
            this.user = null;
            localStorage.removeItem('user');
        },
        setBasic: async function () {
            try{
                this.basic = await api.examApi.getExamBasic();
            }catch (e){
                this.basic = null;
            }
            finally {
                localStorage.setItem('basic', encrypt(this.basic,true));
            }
        },
    },

    getters: {
        isLoggedIn: (state) => {
            return !!state.user?.username;
        },
        userInfo: (state) => state.user,
        isMaster:(state) => state.user?.isadmin === 1
    }
});
