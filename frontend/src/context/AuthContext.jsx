import { createContext, useCallback, useContext, useEffect, useState } from 'react'
import api from '../api/axios'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [token, setToken] = useState(localStorage.getItem('auth_token'))
  const [loading, setLoading] = useState(Boolean(token))

  useEffect(() => {
    let active = true

    const validate = async () => {
      const stored = localStorage.getItem('auth_token')
      if (!stored) {
        setLoading(false)
        return
      }
      try {
        const { data } = await api.get('/user')
        if (!active) return
        setUser(data)
        setToken(stored)
      } catch {
        if (!active) return
        localStorage.removeItem('auth_token')
        setToken(null)
        setUser(null)
      } finally {
        if (active) setLoading(false)
      }
    }

    if (token) {
      validate()
    }

    return () => {
      active = false
    }
  }, [token])

  const login = useCallback(async (email, password) => {
    const { data } = await api.post('/login', { email, password })
    localStorage.setItem('auth_token', data.token)
    setToken(data.token)
    setUser(data.user)
    return data
  }, [])

  const register = useCallback(async (name, email, password, passwordConfirmation) => {
    const { data } = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
    localStorage.setItem('auth_token', data.token)
    setToken(data.token)
    setUser(data.user)
    return data
  }, [])

  const logout = useCallback(async () => {
    try {
      await api.post('/logout')
    } catch {
      // token already invalid — still clear local state
    }
    localStorage.removeItem('auth_token')
    setToken(null)
    setUser(null)
  }, [])

  return (
    <AuthContext.Provider value={{ user, token, loading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return ctx
}
