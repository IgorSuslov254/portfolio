import axios from "axios";
axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://localhost:8000';

export default class MeasuringAPI
{
    static async measuringParams()
    {
        try{
            const response = await axios.get('/api/measuring-params', {
                headers: {Authorization: 'Bearer '+localStorage.getItem('token')}
            });

            return {
                'status': 'success',
                'data': response.data,
            };
        } catch (e) {
            let data = "";

            if(!e.response.data.message){
                data = e.response.data;
            } else {
                data = e.response.data.message;
            }

            return {
                'status': 'error',
                'headers': e.response.status,
                'data': data
            };
        }
    }

    static async calculateEstimate(formData)
    {
        try{
            const response = await axios.post('/api/calculate-estimate', formData, {
                headers: {Authorization: 'Bearer '+localStorage.getItem('token')}
            });

            return {
                'status': 'success',
                'data': response.data,
            };
        } catch (e) {
            let data = "";

            if(!e.response.data.message){
                data = e.response.data;
            } else {
                data = e.response.data.message;
            }

            return {
                'status': 'error',
                'headers': e.response.status,
                'data': data
            };
        }
    }

    static async saveEstimate(formData)
    {
        try{
            const response = await axios.post('/api/save-estimate', formData, {
                headers: {Authorization: 'Bearer '+localStorage.getItem('token')}
            });

            return {
                'status': 'success',
                'data': response.data,
            };
        } catch (e) {
            let data = "";

            if(!e.response.data.message){
                data = e.response.data;
            } else {
                data = e.response.data.message;
            }

            return {
                'status': 'error',
                'headers': e.response.status,
                'data': data
            };
        }
    }

    static async searchEstimate(formData)
    {
        try{
            const response = await axios.post('/api/search-estimate', formData, {
                headers: {Authorization: 'Bearer '+localStorage.getItem('token')}
            });

            return {
                'status': 'success',
                'data': response.data,
            };
        } catch (e) {
            let data = "";

            if(!e.response.data.message){
                data = e.response.data;
            } else {
                data = e.response.data.message;
            }

            return {
                'status': 'error',
                'headers': e.response.status,
                'data': data
            };
        }
    }
}