import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react'

const WishlistContext = createContext(null)
const STORAGE_KEY = 'wishlist_items'

function loadWishlist() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    return raw ? JSON.parse(raw) : []
  } catch {
    return []
  }
}

export function WishlistProvider({ children }) {
  const [items, setItems] = useState(loadWishlist)

  useEffect(() => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items))
  }, [items])

  const toggle = useCallback((product) => {
    setItems((prev) => {
      const exists = prev.find((item) => item.id === product.id)
      if (exists) {
        return prev.filter((item) => item.id !== product.id)
      }
      return [...prev, product]
    })
  }, [])

  const isWishlisted = useCallback(
    (productId) => items.some((item) => item.id === productId),
    [items]
  )

  const wishlistCount = useMemo(
    () => items.length,
    [items]
  )

  const value = useMemo(
    () => ({ items, toggle, isWishlisted, wishlistCount }),
    [items, toggle, isWishlisted, wishlistCount]
  )

  return <WishlistContext.Provider value={value}>{children}</WishlistContext.Provider>
}

export function useWishlist() {
  const ctx = useContext(WishlistContext)
  if (!ctx) {
    throw new Error('useWishlist must be used within a WishlistProvider')
  }
  return ctx
}
