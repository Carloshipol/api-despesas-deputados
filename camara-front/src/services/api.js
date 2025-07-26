import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // ou o endereço da sua API backend
})

export default api
