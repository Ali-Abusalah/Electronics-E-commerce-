import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || (
  window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : `http://${window.location.hostname}:8000`
)

const api = axios.create({
  baseURL: `${API_URL}/api`,
  withCredentials: true,
  // We send X-XSRF-TOKEN ourselves below, decoded from the cookie. Axios's
  // automatic handling sends the still URL-encoded cookie value, which Laravel
  // rejects with "CSRF token mismatch".
  withXSRFToken: false,
})

let csrfCookiePromise = null

function readXsrfCookie() {
  const match = document.cookie
    .split('; ')
    .find((part) => part.startsWith('XSRF-TOKEN='))
  if (!match) return null
  try {
    return decodeURIComponent(match.slice('XSRF-TOKEN='.length))
  } catch {
    return null
  }
}

// The SPA origin is a Sanctum "stateful" domain, so every non-safe request
// must first fetch the CSRF cookie and then echo its decoded value back in
// the X-XSRF-TOKEN header.
function ensureCsrfCookie() {
  if (!csrfCookiePromise) {
    csrfCookiePromise = api
      .get(`${API_URL}/sanctum/csrf-cookie`)
      .catch((err) => {
        csrfCookiePromise = null
        throw err
      })
  }
  return csrfCookiePromise
}

api.interceptors.request.use(async (config) => {
  const method = (config.method || 'get').toLowerCase()

  if (!['get', 'head', 'options'].includes(method)) {
    await ensureCsrfCookie()
    const xsrf = readXsrfCookie()
    if (xsrf) {
      config.headers['X-XSRF-TOKEN'] = xsrf
    }
  }

  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

export default api
