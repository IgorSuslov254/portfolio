import axios from "axios";
axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://localhost:8000';

export default class Authenticated
{
    static async login(formData) {
        try{
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/api/login',formData);
            return {
                'status': 'success',
                'data': response.data,
            };
        }catch (e) {
            return {
                'status': 'error',
                'data': e.response.data.message,
            };
        }
    }

    static async register(formData) {
        try{
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/api/register',formData);
            return {
                'status': 'success',
                'data': response.data,
            };
        }catch (e) {
            return {
                'status': 'error',
                'data': e.response.data.message,
            };
        }
    }

    static async logOut(){
        try{
            await axios.post('/api/logout',{}, {
                headers: {Authorization: 'Bearer '+localStorage.getItem('token')}
            });
            // console.log(response.data, 'logOut');
        } catch (e) {
            console.log(e.response, 'logOut catch');
        }
    }
}